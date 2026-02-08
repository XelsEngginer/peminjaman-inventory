<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Barang</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        
        /* Kop Surat */
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 14px; }
        
        /* Tabel Laporan */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th { background-color: #f2f2f2; padding: 10px; font-size: 12px; }
        td { padding: 8px; font-size: 12px; text-align: center; }
        
        /* Tanda Tangan */
        .ttd { margin-top: 50px; float: right; text-align: center; width: 200px; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>SMK MUHAMMADIYAH 8 SILIRAGUNG</h2>
        <p>Jl. Raya Siliragung No. 123, Banyuwangi, Jawa Timur</p>
        <p>Telp: (0333) 123456 | Email: info@smkmodels.sch.id</p>
    </div>

    <h3 style="text-align: center;">LAPORAN PEMINJAMAN BARANG</h3>
    <p>Periode: {{ date('F Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Nama Alat</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $p->user->username }}</td>
                <td style="text-align: left;">{{ $p->alat->nama_alat }}</td>
                <td>{{ date('d-m-Y', strtotime($p->tgl_pinjam)) }}</td>
                <td>{{ $p->tgl_kembali ? date('d-m-Y', strtotime($p->tgl_kembali)) : '-' }}</td>
                <td>{{ ucfirst($p->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd">
        <p>Siliragung, {{ date('d F Y') }}</p>
        <p>Kepala Lab,</p>
        <br><br><br>
        <p><b>_______________________</b></p>
        <p>NIP. 19850101 201001 1 001</p>
    </div>

</body>
</html>