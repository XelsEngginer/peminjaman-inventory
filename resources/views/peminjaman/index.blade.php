<?php
session_start();
// Jalur Database Breee
$conn = mysqli_connect("localhost", "root", "", "peminjaman_alat");
if (!$conn) {
    die("<div class='alert alert-danger'>Koneksi gagal! Periksa database kamu.</div>");
}

// --- LOGIKA LOGIN ---
if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = md5($_POST['password']);
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username='$u' AND password='$p'");
    if (mysqli_num_rows($q) > 0) {
        $_SESSION['user'] = mysqli_fetch_assoc($q);
        header("Location:proses.php"); exit;
    } else {
        $error = "Username atau Password salah!";
    }
}

// --- LOGIKA LOGOUT ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location:index.php"); exit;
}

// --- LOGIKA PROSES DATA ---
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    $menu = isset($_GET['menu']) ? $_GET['menu'] : 'dashboard';

    // Manajemen Simpan/Update Alat
    if (isset($_POST['simpan_alat'])) {
        $n = $_POST['nama']; $k = $_POST['kategori']; $s = $_POST['stok']; $id = $_POST['id'];
        if ($id == '') {
            mysqli_query($conn, "INSERT INTO alat VALUES(NULL, '$n', '$k', '$s')");
        } else {
            mysqli_query($conn, "UPDATE alat SET nama_alat='$n', id_kategori='$k', stok='$s' WHERE id_alat='$id'");
        }
        header("Location:?menu=alat"); exit;
    }

    // Manajemen Hapus Alat
    if (isset($_GET['hapus_alat'])) {
        mysqli_query($conn, "DELETE FROM alat WHERE id_alat='".$_GET['hapus_alat']."'");
        header("Location:?menu=alat"); exit;
    }

    // Proses Pengajuan Pinjam
    if (isset($_POST['pinjam'])) {
        mysqli_query($conn, "INSERT INTO peminjaman VALUES(NULL, '".$_SESSION['user']['id_user']."', '".$_POST['id_alat']."', CURDATE(), NULL, 'pending', 0)");
        header("Location:?menu=lihat"); exit;
    }

    // Proses Persetujuan Admin (Verifikasi)
    if (isset($_GET['setujui'])) {
        $id_p = $_GET['setujui'];
        $res = mysqli_query($conn, "SELECT id_alat FROM peminjaman WHERE id_pinjam='$id_p'");
        $data = mysqli_fetch_assoc($res);
        mysqli_query($conn, "UPDATE peminjaman SET status='disetujui', tgl_kembali=DATE_ADD(tgl_pinjam, INTERVAL 3 DAY) WHERE id_pinjam='$id_p'");
        mysqli_query($conn, "UPDATE alat SET stok = stok - 1 WHERE id_alat='".$data['id_alat']."'");
        header("Location:?menu=verifikasi"); exit;
    }

    // Proses Pengembalian Alat
    if (isset($_GET['kembali'])) {
        $id_p = $_GET['kembali'];
        $q = mysqli_query($conn, "SELECT id_alat, DATEDIFF(CURDATE(), tgl_kembali) AS t FROM peminjaman WHERE id_pinjam='$id_p'");
        $d = mysqli_fetch_assoc($q);
        $denda = ($d['t'] > 0) ? $d['t'] * 1000 : 0;
        mysqli_query($conn, "UPDATE peminjaman SET status='dikembalikan', denda='$denda' WHERE id_pinjam='$id_p'");
        mysqli_query($conn, "UPDATE alat SET stok = stok + 1 WHERE id_alat='".$d['id_alat']."'");
        header("Location:?menu=kembali"); exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Peminjaman Alat - SMK MODELS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: white; padding-top: 20px; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 12px 20px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #3498db; color: white; }
        .content { padding: 30px; }
        .card { border-radius: 12px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['user'])): ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card p-4">
                    <h3 class="text-center fw-bold mb-4">LOGIN USER</h3>
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <form method="POST">
                        <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                        <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                        <button type="submit" name="login" class="btn btn-primary w-100 fw-bold">MASUK</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-0 text-center">
                <h4 class="fw-bold mt-3">UKK MODELS</h4>
                <p class="badge bg-primary"><?= strtoupper($role) ?></p>
                <hr>
                <a href="?menu=dashboard" class="<?= $menu=='dashboard'?'active':'' ?>"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                <?php if ($role == 'admin'): ?>
                    <a href="?menu=alat" class="<?= $menu=='alat'?'active':'' ?>"><i class="fas fa-boxes me-2"></i> Kelola Alat</a>
                    <a href="?menu=verifikasi" class="<?= $menu=='verifikasi'?'active':'' ?>"><i class="fas fa-check-double me-2"></i> Verifikasi</a>
                    <a href="?menu=kembali" class="<?= $menu=='kembali'?'active':'' ?>"><i class="fas fa-undo-alt me-2"></i> Kembali</a>
                <?php endif; ?>
                <a href="?menu=lihat" class="<?= $menu=='lihat'?'active':'' ?>"><i class="fas fa-search me-2"></i> Cari Alat</a>
                <a href="?menu=pinjam" class="<?= $menu=='pinjam'?'active':'' ?>"><i class="fas fa-hand-holding me-2"></i> Pinjam</a>
                <a href="?logout=1" class="text-danger mt-5"><i class="fas fa-power-off me-2"></i> Keluar</a>
            </div>

            <div class="col-md-10 content">
                <?php 
                switch($menu) {
                    case 'alat':
                        // BAGIAN MANAJEMEN ALAT
                        $ea = array('id_alat'=>'','nama_alat'=>'','id_kategori'=>'','stok'=>'');
                        if(isset($_GET['edit_alat'])){
                            $res_e = mysqli_query($conn,"SELECT * FROM alat WHERE id_alat='".$_GET['edit_alat']."'");
                            $ea = mysqli_fetch_assoc($res_e);
                        }
                        ?>
                        <h3 class="fw-bold mb-4">Kelola Data Alat</h3>
                        <div class="card p-4 mb-4">
                            <form method="post" class="row g-3">
                                <input type="hidden" name="id" value="<?= $ea['id_alat'] ?>">
                                <div class="col-md-4"><label class="form-label">Nama Alat</label><input name="nama" class="form-control" value="<?= $ea['nama_alat'] ?>" required></div>
                                <div class="col-md-3"><label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-select">
                                        <?php $qk = mysqli_query($conn,"SELECT * FROM kategori"); while($k = mysqli_fetch_array($qk)){
                                            $sel = ($ea['id_kategori'] == $k['id_kategori']) ? 'selected' : '';
                                            echo "<option value='$k[id_kategori]' $sel>$k[nama_kategori]</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-md-2"><label class="form-label">Stok</label><input name="stok" type="number" class="form-control" value="<?= $ea['stok'] ?>"></div>
                                <div class="col-md-3 d-flex align-items-end"><button name="simpan_alat" class="btn btn-primary w-100">SIMPAN</button></div>
                            </form>
                        </div>
                        <div class="card p-4">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light"><tr><th>Alat</th><th>Stok</th><th>Aksi</th></tr></thead>
                                <tbody>
                                    <?php $qa = mysqli_query($conn, "SELECT * FROM alat"); while($a = mysqli_fetch_array($qa)): ?>
                                    <tr><td><?= $a['nama_alat'] ?></td><td><?= $a['stok'] ?></td><td>
                                        <a href="?menu=alat&edit_alat=<?= $a['id_alat'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        <a href="?menu=alat&hapus_alat=<?= $a['id_alat'] ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                    </td></tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php break;

                    case 'verifikasi':
                        // BAGIAN PERSETUJUAN PINJAM
                        ?>
                        <h3 class="fw-bold mb-4">Verifikasi Peminjaman</h3>
                        <div class="card p-4">
                            <table class="table align-middle">
                                <thead class="table-primary">
                                    <tr><th>Nama Peminjam</th><th>Nama Alat</th><th>Tgl Pinjam</th><th class="text-center">Aksi</th></tr>
                                </thead>
                                <tbody>
                                    <?php $q=mysqli_query($conn,"SELECT * FROM peminjaman JOIN users USING(id_user) JOIN alat USING(id_alat) WHERE status='pending'"); 
                                    while($p=mysqli_fetch_array($q)){ ?>
                                    <tr><td><?= $p['username'] ?></td><td><?= $p['nama_alat'] ?></td><td><?= $p['tgl_pinjam'] ?></td>
                                        <td class="text-center"><a href='?menu=verifikasi&setujui=<?= $p['id_pinjam'] ?>' class='btn btn-success'><i class="fas fa-check-circle me-1"></i> Setujui</a></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php break;

                    case 'kembali':
                        // BAGIAN PROSES KEMBALI
                        ?>
                        <h3 class="fw-bold mb-4">Proses Pengembalian</h3>
                        <div class="card p-4">
                            <table class="table align-middle">
                                <thead class="table-info"><tr><th>Peminjam</th><th>Batas Waktu</th><th class="text-center">Aksi</th></tr></thead>
                                <tbody>
                                    <?php $q=mysqli_query($conn,"SELECT * FROM peminjaman JOIN users USING(id_user) WHERE status='disetujui'"); 
                                    while($p=mysqli_fetch_array($q)){ ?>
                                    <tr><td><?= $p['username'] ?></td><td><span class="text-danger fw-bold"><?= $p['tgl_kembali'] ?></span></td>
                                        <td class="text-center"><a href='?menu=kembali&kembali=<?= $p['id_pinjam'] ?>' class='btn btn-primary'><i class="fas fa-undo me-1"></i> Proses Kembali</a></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php break;

                    case 'lihat':
                        // TAMPILAN GRID ALAT
                        ?>
                        <h3 class="fw-bold mb-4">Daftar Alat Praktik</h3>
                        <div class="row">
                            <?php $ql = mysqli_query($conn, "SELECT * FROM alat JOIN kategori USING(id_kategori)"); while($a = mysqli_fetch_array($ql)): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card p-3 h-100 border-top border-primary border-4">
                                    <small class="text-muted fw-bold"><?= strtoupper($a['nama_kategori']) ?></small>
                                    <h4 class="fw-bold text-dark"><?= $a['nama_alat'] ?></h4>
                                    <span class="badge bg-success w-50">Tersedia: <?= $a['stok'] ?></span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php break;

                    case 'pinjam':
                        // FORM PINJAM
                        ?>
                        <div class="card p-5 mx-auto shadow" style="max-width: 500px;">
                            <div class="text-center mb-4"><i class="fas fa-hand-holding-box fa-3x text-success"></i></div>
                            <form method="post">
                                <div class="mb-4"><label class="form-label fw-bold">Pilih Alat</label>
                                    <select name="id_alat" class="form-select form-select-lg">
                                        <?php $qp = mysqli_query($conn,"SELECT * FROM alat WHERE stok > 0"); while($p = mysqli_fetch_array($qp)) echo "<option value='$p[id_alat]'>$p[nama_alat] ($p[stok])</option>"; ?>
                                    </select>
                                </div>
                                <button name="pinjam" class="btn btn-success btn-lg w-100 fw-bold">AJUKAN SEKARANG</button>
                            </form>
                        </div>
                        <?php break;

                    default: // DASHBOARD DENGAN STATISTIK
                        $jml_user = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));
                        $jml_alat = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alat"));
                        $jml_pinjam = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman"));
                        ?>
                        <div class="row text-center mb-5">
                            <div class="col-md-4 mb-3"><div class="card p-4 border-bottom border-primary border-4"><h5>Total User</h5><h1 class="fw-bold text-primary"><?= $jml_user ?></h1></div></div>
                            <div class="col-md-4 mb-3"><div class="card p-4 border-bottom border-success border-4"><h5>Total Alat</h5><h1 class="fw-bold text-success"><?= $jml_alat ?></h1></div></div>
                            <div class="col-md-4 mb-3"><div class="card p-4 border-bottom border-warning border-4"><h5>Total Pinjaman</h5><h1 class="fw-bold text-warning"><?= $jml_pinjam ?></h1></div></div>
                        </div>
                        <div class="card p-5 text-center">
                            <i class="fas fa-laptop-house fa-5x text-primary mb-4"></i>
                            <h1 class="fw-bold">Selamat Datang di Peminjaman Alat</h1>
                            <p class="text-muted lead">UKK SMK MODELS</p>
                        </div>
                        <?php break;
                } ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>