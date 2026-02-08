<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>INVENTORY SYSTEM PRO | SMK MODELS 8</title>
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            const preBg = savedTheme === 'dark' ? '#0b1120' : '#F3F4F6';
            document.write('<style id="anti-flash-lock">body { visibility: hidden !important; background-color: ' + preBg + ' !important; }</style>');
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ------------------------------------------------------------------
           2.1. DESIGN TOKENS (VARIABLES)
           ------------------------------------------------------------------ */
        :root {
            --sidebar-width: 280px;
            --header-height: 80px;
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --primary-soft: #EEF2FF;
            --bg-body: #F3F4F6;
            --bg-card: #FFFFFF;
            --bg-input: #F8FAFC;
            --text-main: #0F172A;
            --text-body: #334155;
            --text-muted: #64748B;
            --border: #E2E8F0;
            --radius-lg: 24px;
            --radius-md: 16px;
            
            --success: #10B981; --bg-success: #D1FAE5;
            --warning: #F59E0B; --bg-warning: #FEF3C7;
            --danger: #EF4444;  --bg-danger: #FEE2E2;
            --info: #0EA5E9;    --bg-info: #E0F2FE;
        }

        [data-theme="dark"] {
            --primary: #818CF8;
            --primary-hover: #6366F1;
            --primary-soft: rgba(99, 102, 241, 0.15);
            --bg-body: #0B1120;
            --bg-card: #151E2E;
            --bg-input: #1F2937;
            --text-main: #FFFFFF !important;
            --text-body: #E2E8F0 !important;
            --text-muted: #94A3B8 !important;
            --border: #2D3748;
            
            --bg-success: rgba(16, 185, 129, 0.15);
            --bg-warning: rgba(245, 158, 11, 0.15);
            --bg-danger: rgba(239, 68, 68, 0.15);
        }

        *, *::before, *::after { box-sizing: border-box; outline: none; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0; padding: 0;
            overflow-x: hidden;
            transition: background-color 0.4s ease;
        }

        h1, h2, h3, h4, h5, h6, strong, b { color: var(--text-main) !important; font-weight: 800; }
        p, span, div, label, li, td, th { color: var(--text-main); }
        .text-muted { color: var(--text-muted) !important; }
        
        a { text-decoration: none; transition: 0.2s; color: inherit; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--text-muted); border-radius: 10px; }

        /* SIDEBAR STYLE */
        .sidebar {
            width: var(--sidebar-width); height: 100vh;
            position: fixed; top: 0; left: 0;
            background: var(--bg-card); border-right: 1px solid var(--border);
            display: flex; flex-direction: column; z-index: 1050; padding: 24px;
            transition: transform 0.3s ease, background-color 0.4s ease;
        }

        .brand { display: flex; align-items: center; gap: 14px; padding-bottom: 25px; border-bottom: 1px solid var(--border); margin-bottom: 25px; }
        .brand-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), #4338CA);
            color: white !important; display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        .brand-text h4 { font-size: 1.2rem; margin: 0; letter-spacing: -0.5px; font-weight: 800; }

        .sidebar-menu { flex: 1; overflow-y: auto; overflow-x: hidden; margin-right: -10px; padding-right: 10px; }
        .nav-label { font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin: 24px 0 12px 12px; letter-spacing: 1.2px; }
        
        .nav-link { 
            display: flex; align-items: center; gap: 12px; padding: 12px 16px; 
            border-radius: 14px; color: var(--text-muted); font-weight: 600; 
            margin-bottom: 6px; transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .nav-link:hover { background: var(--bg-body); color: var(--primary) !important; transform: translateX(5px); }
        .nav-link.active { background: var(--primary-soft); color: var(--primary) !important; font-weight: 700; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1); }
        .nav-link i { width: 22px; text-align: center; font-size: 1.15rem; }

        .sidebar-footer { margin-top: auto; padding-top: 20px; border-top: 1px solid var(--border); }
        .user-logout-wrapper {
            display: flex; align-items: center; justify-content: space-between;
            background: var(--bg-body); padding: 12px; border-radius: 16px;
            border: 1px solid var(--border); transition: 0.3s;
        }
        
        .user-meta-info { display: flex; align-items: center; gap: 10px; flex: 1; overflow: hidden; cursor: pointer; }
        .user-avatar-small { 
            width: 40px; height: 40px; background: var(--primary); color: white !important; 
            border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0;
        }
        .user-text-box { overflow: hidden; }
        .user-text-box .u-name { display: block; font-weight: 700; font-size: 0.85rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-text-box .u-role { display: block; font-size: 0.7rem; color: var(--text-muted); text-transform: capitalize; }

        .btn-logout-circle {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--bg-danger); color: var(--danger) !important;
            display: flex; align-items: center; justify-content: center;
            transition: 0.3s; border: none; text-decoration: none; flex-shrink: 0;
        }
        .btn-logout-circle:hover { background: var(--danger); color: white !important; transform: scale(1.05); }

        /* MAIN CONTENT STYLE */
        .main-content {
            margin-left: var(--sidebar-width); padding: 30px 45px;
            min-height: 100vh; width: calc(100% - var(--sidebar-width));
            display: flex; flex-direction: column;
        }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        
        .search-wrap { position: relative; width: 350px; }
        .search-wrap i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--text-muted); z-index: 2; }
        .search-input { 
            width: 100%; padding: 14px 20px 14px 52px; border-radius: 50px; 
            border: 1px solid var(--border); background: var(--bg-card); 
            color: var(--text-main) !important; transition: 0.3s; font-size: 0.95rem;
        }
        .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 5px var(--primary-soft); }

        .btn-icon { width: 48px; height: 48px; border-radius: 50%; background: var(--bg-card); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--text-muted); cursor: pointer; transition: 0.2s; font-size: 1.2rem; }
        .btn-icon:hover { transform: translateY(-3px); border-color: var(--primary); color: var(--primary) !important; box-shadow: 0 10px 15px rgba(0,0,0,0.05); }

        .card-stat {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 28px; height: 100%;
            display: flex; align-items: center; justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; overflow: hidden;
        }
        .card-stat:hover { transform: translateY(-8px); border-color: var(--primary); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        
        .stat-val { font-size: 2.8rem; font-weight: 800; line-height: 1; margin: 0; }
        .stat-lbl { font-size: 0.9rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        
        .icon-box { width: 64px; height: 64px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; }
        .bg-b { background: rgba(79, 70, 229, 0.1); color: #4F46E5 !important; }
        .bg-g { background: rgba(16, 185, 129, 0.1); color: #10B981 !important; }
        .bg-o { background: rgba(245, 158, 11, 0.1); color: #F59E0B !important; }

        .banner-hero {
            border-radius: var(--radius-lg); background: linear-gradient(135deg, #4F46E5, #4338CA);
            padding: 50px; color: white !important; box-shadow: 0 15px 35px rgba(79, 70, 229, 0.3);
            height: 100%; position: relative; overflow: hidden;
        }
        .banner-hero h2 { font-weight: 800; font-size: 2.2rem; margin-bottom: 15px; color: white !important; }
        .banner-hero p { font-size: 1.1rem; opacity: 0.9; color: white !important; max-width: 500px; }
        .btn-banner { 
            background: #FFFFFF !important; color: #4F46E5 !important; border: none; 
            padding: 14px 40px; border-radius: 50px; font-weight: 800; 
            display: inline-flex; align-items: center; gap: 10px; margin-top: 30px; transition: 0.3s; 
        }
        .btn-banner:hover { transform: scale(1.05) translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        .item-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 22px; padding: 25px; transition: 0.3s; height: 100%; }
        .item-card:hover { border-color: var(--primary); transform: translateY(-5px); }
        
        .badge-cat { 
            display: inline-block; padding: 6px 14px; border-radius: 10px; font-size: 0.75rem; 
            font-weight: 800; text-transform: uppercase; border: 1px solid var(--border); 
            color: var(--text-main) !important; margin-bottom: 15px; 
        }
        .stock-tag { float: right; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; }
        .tag-ok { background: var(--bg-success); color: var(--success) !important; }
        .tag-no { background: var(--bg-danger); color: var(--danger) !important; }

        .item-title { font-size: 1.3rem; font-weight: 800; margin-bottom: 20px; }

        .table-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 24px; overflow: hidden; }
        .table-clean th { background: var(--bg-input); padding: 20px 24px; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; font-weight: 800; border-bottom: 1px solid var(--border); }
        .table-clean td { padding: 22px 24px; border-bottom: 1px solid var(--border); vertical-align: middle; }

        .btn-main { background: var(--primary); color: white !important; border: none; padding: 16px; border-radius: 16px; font-weight: 800; width: 100%; display: flex; justify-content: center; align-items: center; gap: 10px; transition: 0.3s; }
        .btn-main:hover { background: var(--primary-hover); transform: translateY(-3px); box-shadow: 0 10px 15px rgba(79, 70, 229, 0.2); }
        .btn-main:disabled { opacity: 0.6; cursor: not-allowed; filter: grayscale(0.5); }

        .qty-box { display: flex; align-items: center; justify-content: space-between; background: var(--bg-body); padding: 6px; border-radius: 14px; border: 1px solid var(--border); }
        .btn-qty { width: 34px; height: 34px; border-radius: 10px; border: none; background: var(--bg-card); color: var(--text-main); font-weight: 800; cursor: pointer; transition: 0.2s; }
        .qty-inp { width: 45px; text-align: center; border: none; background: transparent; font-weight: 800; font-size: 1.1rem; color: var(--text-main) !important; }

        .fade-short { animation: fadeInShort 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeInShort { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* ------------------------------------------------------------------
           3. RESPONSIVE OPTIMIZATION (THE FIX)
           ------------------------------------------------------------------ */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); } 
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; padding: 20px; }
            .header { flex-direction: column; align-items: flex-start; gap: 20px; }
            .search-wrap { width: 100%; }
            .banner-hero { padding: 30px; }
            .banner-hero h2 { font-size: 1.6rem; }
            .stat-val { font-size: 2rem; }
        }

        @media (max-width: 576px) {
            .main-content { padding: 15px; }
            .table-clean td, .table-clean th { padding: 12px 15px; font-size: 0.8rem; }
            .badge-cat { padding: 4px 8px; font-size: 0.65rem; }
        }

        /* ------------------------------------------------------------------
           MODERN GLASSMORPHISM POPUP THEME
           ------------------------------------------------------------------ */
        .swal2-popup {
            border-radius: 35px !important;
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(25px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(25px) saturate(180%) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.4) !important;
            padding: 2.5rem !important;
        }
        
        [data-theme="dark"] .swal2-popup {
            background: rgba(15, 23, 42, 0.85) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .swal2-title { font-weight: 800 !important; color: var(--text-main) !important; }
        .swal2-html-container { color: var(--text-muted) !important; font-weight: 600 !important; }
        .swal2-confirm { border-radius: 18px !important; padding: 14px 40px !important; font-weight: 800 !important; text-transform: uppercase; letter-spacing: 1px !important; }
        .swal2-cancel { border-radius: 18px !important; padding: 14px 40px !important; font-weight: 800 !important; }

        /* Print Optimization */
        @media print {
            .sidebar, .header, .btn-main, .btn-icon, .search-wrap, #themeBtn { display: none !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
            .table-card { border: none !important; box-shadow: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="animate__animated animate__fadeIn">

    <div id="overlay" onclick="toggleSidebar()" style="position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; display:none; backdrop-filter:blur(4px);"></div>

    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-school"></i></div>
            <div class="brand-text"><h4>INVENTORY</h4><span class="text-muted small fw-bold">SMK MODELS 8</span></div>
        </div>
        
        <div class="sidebar-menu">
            <div class="nav-label">Menu Utama</div>
            <a href="?menu=dashboard" class="nav-link {{ $menu == 'dashboard' || $menu == '' ? 'active' : '' }}"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
            
            @if(Auth::user()->role == 'admin')
            <div class="nav-label">Administrator</div>
            <a href="?menu=user" class="nav-link {{ $menu == 'user' ? 'active' : '' }}"><i class="fas fa-users-cog"></i> <span>User Management</span></a>
            <a href="?menu=alat" class="nav-link {{ $menu == 'alat' ? 'active' : '' }}"><i class="fas fa-box-open"></i> <span>Inventory Master</span></a>
            @endif
            
            <div class="nav-label">Transactions</div>
            @if(Auth::user()->role != 'peminjam')
            <a href="?menu=persetujuan" class="nav-link {{ $menu == 'persetujuan' ? 'active' : '' }}"><i class="fas fa-clipboard-check"></i> <span>Verification</span>
            @if($jml_pinjam > 0) <span class="badge bg-warning text-dark ms-auto rounded-pill">{{ $jml_pinjam }}</span> @endif </a>
            @endif
            <a href="?menu=lihat" class="nav-link {{ $menu == 'lihat' ? 'active' : '' }}"><i class="fas fa-search"></i> <span>Borrow Assets</span></a>
            <a href="?menu=pinjam" class="nav-link {{ $menu == 'pinjam' ? 'active' : '' }}"><i class="fas fa-clock-rotate-left"></i> <span>My History</span></a>
        </div>

        <div class="sidebar-footer">
            <div class="user-logout-wrapper">
                <div class="user-meta-info" data-bs-toggle="modal" data-bs-target="#profileModal">
                    <div class="user-avatar-small">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</div>
                    <div class="user-text-box">
                        <span class="u-name">{{ Auth::user()->username }}</span>
                        <span class="u-role">{{ ucfirst(Auth::user()->role) }}</span>
                    </div>
                </div>
                <a href="{{ route('logout') }}" class="btn-logout-circle" title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn-icon d-lg-none" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div>
                    <h1 class="m-0 fs-4">Control Center</h1>
                    <p class="small m-0 text-muted">Akses Terkendali: <strong>{{ Auth::user()->username }}</strong></p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 w-100 justify-content-lg-end">
                <div class="search-wrap d-none d-md-block">
                    <i class="fas fa-search"></i>
                    <input type="text" id="liveSearch" class="search-input" placeholder="Cari data inventaris...">
                </div>
                <button class="btn-icon" id="themeBtn" title="Ganti Tema"><i class="fas fa-moon" id="themeIcon"></i></button>
                @if(Auth::user()->role != 'peminjam')
                    <a href="{{ route('laporan.cetak') }}" target="_blank" class="btn-main" style="width:auto; padding:0 28px;"><i class="fas fa-print me-2"></i>Cetak</a>
                @endif
            </div>
        </header>

        <div class="fade-short">
            @if($menu == 'dashboard' || $menu == '')
                <div class="row g-3 g-md-4 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="card-stat"><div><h2 class="stat-val">{{ $jml_user }}</h2><span class="stat-lbl">Active Users</span></div><div class="icon-box bg-b"><i class="fas fa-users"></i></div></div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card-stat"><div><h2 class="stat-val">{{ $jml_alat }}</h2><span class="stat-lbl">Total Assets</span></div><div class="icon-box bg-g"><i class="fas fa-box-open"></i></div></div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card-stat"><div><h2 class="stat-val">{{ $jml_pinjam }}</h2><span class="stat-lbl">Pending Acc</span></div><div class="icon-box bg-o"><i class="fas fa-clock"></i></div></div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <div class="banner-hero">
                            <h2>Sistem Inventaris Sekolah</h2>
                            <p>Kelola peminjaman dan pengembalian aset praktik SMK Muhammadiyah 8 Siliragung secara digital dan transparan.</p>
                            <a href="?menu=lihat" class="btn-banner">Cari & Pinjam Alat <i class="fas fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="table-card p-4 h-100">
                            <h6 class="fw-bold mb-4">Aktivitas Terakhir</h6>
                            <div class="d-flex flex-column gap-4">
                                @foreach($all_pinjam->take(5) as $p)
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:42px;height:42px;background:var(--primary-soft);color:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">{{ strtoupper(substr($p->user->username,0,1)) }}</div>
                                    <div style="overflow:hidden;"><p class="m-0 fw-bold text-truncate" style="font-size:0.9rem;">{{ $p->user->username }}</p><p class="m-0 text-muted small text-truncate">Meminjam {{ $p->alat->nama_alat }}</p></div>
                                    <small class="ms-auto text-muted" style="font-size:0.7rem; font-weight:700;">{{ $p->created_at->diffForHumans() }}</small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($menu == 'lihat')
                <div class="row g-3 g-md-4 searchable-container">
                    @forelse($alats_with_kategori as $a)
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 searchable-item">
                        <div class="item-card d-flex flex-column">
                            <div class="mb-2">
                                <span class="badge-cat">{{ $a->kategori->nama_kategori ?? 'Umum' }}</span>
                                <span class="stock-tag {{ $a->stok > 0 ? 'tag-ok':'tag-no' }}">{{ $a->stok > 0 ? $a->stok.' Tersedia' : 'Kosong' }}</span>
                            </div>
                            <h5 class="item-title"><strong>{{ $a->nama_alat }}</strong></h5>
                            
                            <form action="{{ route('pinjam.store') }}" method="POST" class="mt-auto" id="form-{{$a->id_alat}}">
                                @csrf
                                <input type="hidden" name="id_alat" value="{{ $a->id_alat }}">
                                @if($a->stok > 0)
                                    <div class="qty-box mb-3">
                                        <button type="button" class="btn-qty" onclick="updateQty(this, -1)">-</button>
                                        <input type="number" name="jumlah_pinjam" value="1" min="1" max="{{ $a->stok }}" class="qty-inp" readonly>
                                        <button type="button" class="btn-qty" onclick="updateQty(this, 1, {{ $a->stok }})">+</button>
                                    </div>
                                @endif
                                <button type="button" class="btn-main mt-1" onclick="confirmAction('form-{{$a->id_alat}}', 'Ingin meminjam unit ini?')" {{ $a->stok < 1 ? 'disabled' : '' }}>
                                    <span>{{ $a->stok < 1 ? 'Habis' : 'Ajukan Peminjaman' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5"><h3>Belum ada data barang</h3></div>
                    @endforelse
                </div>
            @endif

            @if(in_array($menu, ['alat', 'user', 'persetujuan', 'pinjam']))
                <div class="row g-4">
                    @if($menu == 'alat' || $menu == 'user')
                    <div class="col-12 col-lg-4">
                        <div class="table-card p-4" style="position: sticky; top: 20px; z-index: 5;">
                            <h5 class="fw-bold mb-4">Kelola Data</h5>
                            <form action="{{ $menu == 'alat' ? route('alat.store') : route('user.store') }}" method="POST">
                                @csrf
                                @if($menu == 'alat')
                                    <input type="hidden" name="id" value="{{ $ea->id_alat ?? '' }}">
                                    <div class="mb-3"><label class="small fw-bold">Nama Item</label><input name="nama" class="form-control" value="{{ $ea->nama_alat ?? '' }}" required></div>
                                    <div class="mb-3"><label class="small fw-bold">Kategori</label><select name="kategori" class="form-select" required><option value="">-- Pilih --</option>@foreach($kategoris as $k)<option value="{{ $k->id_kategori }}" {{ (isset($ea) && $ea->id_kategori == $k->id_kategori) ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>@endforeach</select></div>
                                    <div class="mb-4"><label class="small fw-bold">Stok Ready</label><input name="stok" type="number" class="form-control" value="{{ $ea->stok ?? '' }}" required></div>
                                @else
                                    <div class="mb-3"><label class="small fw-bold">Username</label><input name="username" class="form-control" required></div>
                                    <div class="mb-3"><label class="small fw-bold">Password</label><input name="password" type="password" class="form-control" required></div>
                                    <div class="mb-4"><label class="small fw-bold">Level Akses</label><select name="role" class="form-select"><option value="petugas">Petugas</option><option value="peminjam">Peminjam</option><option value="admin">Admin</option></select></div>
                                @endif
                                <button class="btn-main">Simpan Data Baru</button>
                            </form>
                        </div>
                    </div>
                    @endif
                    <div class="{{ ($menu == 'alat' || $menu == 'user') ? 'col-lg-8' : 'col-12' }}">
                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table-clean w-100 searchable-table">
                                    <thead>
                                        <tr>
                                            @if($menu == 'alat')<th>Item</th><th>Category</th><th>Stock</th><th>Action</th>
                                            @elseif($menu == 'user')<th>User ID</th><th>Level</th><th>Action</th>
                                            @elseif($menu == 'persetujuan')<th>Requester</th><th>Asset</th><th>Date</th><th>Action</th>
                                            @else<th>Asset</th><th>Date</th><th>Status</th><th>Action</th>@endif
                                        </tr>
                                    </thead>
                                    <tbody class="searchable-container">
                                        @if($menu == 'alat')
                                            @foreach($alats as $a)
                                            <tr class="searchable-item"><td><strong>{{ $a->nama_alat }}</strong></td><td><span class="badge-cat m-0">{{ $a->kategori->nama_kategori ?? '-' }}</span></td><td class="fw-bold">{{ $a->stok }}</td><td><a href="?menu=alat&edit_alat={{ $a->id_alat }}" class="btn-icon d-inline-flex me-1" style="width:34px;height:34px;font-size:0.85rem;"><i class="fas fa-pen"></i></a><a href="{{ route('alat.destroy', $a->id_alat) }}" class="btn-icon text-danger d-inline-flex" style="width:34px;height:34px;font-size:0.85rem;" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a></td></tr>
                                            @endforeach
                                        @elseif($menu == 'user')
                                            @foreach($users as $u)
                                            <tr class="searchable-item"><td><strong>{{ $u->username }}</strong></td><td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1">{{ $u->role }}</span></td><td><a href="{{ route('user.destroy', $u->id) }}" class="text-danger fw-bold small text-decoration-none">Terminate</a></td></tr>
                                            @endforeach
                                        @elseif($menu == 'persetujuan')
                                            @foreach($all_pinjam as $p)
                                            <tr class="searchable-item">
                                                <td><strong>{{ $p->user->username }}</strong></td>
                                                <td>{{ $p->alat->nama_alat }}</td>
                                                <td class="text-muted small">{{ $p->tgl_pinjam }}</td>
                                                <td>
                                                    @if($p->status == 'pending') 
                                                        <div class="d-flex gap-2">
                                                            <button onclick="confirmActionLink('{{ route('pinjam.setujui', $p->id_pinjam) }}', this, 'Setujui peminjaman ini?')" class="btn-main py-1 px-3 w-auto" style="font-size:0.8rem">Approve</button> 
                                                            <button onclick="confirmActionLink('{{ route('pinjam.tolak', $p->id_pinjam) }}', this, 'Tolak permintaan ini?')" class="btn-main py-1 px-3 w-auto bg-danger border-0" style="font-size:0.8rem;">Reject</button>
                                                        </div>
                                                    @elseif($p->status == 'disetujui') 
                                                        <button onclick="confirmActionLink('{{ route('pinjam.kembalikan', $p->id_pinjam) }}', this, 'Konfirmasi pengembalian barang?')" class="text-danger fw-bold small bg-transparent border-0">Mark Return</button> 
                                                    @elseif($p->status == 'ditolak')
                                                        <span class="text-danger fw-bold small"><i class="fas fa-times-circle"></i> Rejected</span>
                                                    @else 
                                                        <span class="text-success fw-bold small"><i class="fas fa-check-double"></i> Finished</span> 
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            @foreach(Auth::user()->peminjamans as $p)
                                            <tr class="searchable-item">
                                                <td><strong>{{ $p->alat->nama_alat }}</strong></td>
                                                <td class="text-muted small">{{ $p->tgl_pinjam }}</td>
                                                <td>
                                                    @if($p->status == 'disetujui')<span class="badge bg-success bg-opacity-10 text-success">Borrowed</span>
                                                    @elseif($p->status == 'pending')<span class="badge bg-warning bg-opacity-10 text-warning">Awaiting</span>
                                                    @elseif($p->status == 'ditolak')<span class="badge bg-danger bg-opacity-10 text-danger">Rejected</span>
                                                    @else<span class="badge bg-secondary bg-opacity-10 text-secondary">Returned</span>@endif
                                                </td>
                                                <td>@if($p->status == 'disetujui' && Auth::user()->role != 'peminjam') <button onclick="confirmActionLink('{{ route('pinjam.kembalikan', $p->id_pinjam) }}', this, 'Kembalikan barang ini ke gudang?')" class="text-danger fw-bold small bg-transparent border-0">Return Now</button> @endif</td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>

    <div class="modal fade" id="profileModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content p-5 text-center border-0" style="border-radius:30px;"><div class="user-avatar mx-auto mb-3" style="width:85px;height:85px;background:var(--primary);color:white;font-size:2.2rem;display:flex;align-items:center;justify-content:center;border-radius:20px;font-weight:800;">{{ strtoupper(substr(Auth::user()->username, 0, 1)) }}</div><h5 class="fw-bold mb-1">{{ Auth::user()->username }}</h5><span class="badge bg-primary bg-opacity-10 text-primary mb-4 px-3 py-2">{{ strtoupper(Auth::user()->role) }}</span><button class="btn btn-light btn-sm w-100 border" style="border-radius:12px;" data-bs-dismiss="modal">Close</button></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const html = document.documentElement;
        const themeBtn = document.getElementById('themeBtn');
        const themeIcon = document.getElementById('themeIcon');
        const lockStyle = document.getElementById('anti-flash-lock');

        window.addEventListener('DOMContentLoaded', () => { if(lockStyle) lockStyle.remove(); document.body.style.visibility = 'visible'; });

        function updateUI(theme) { themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon'; }
        updateUI(html.getAttribute('data-theme'));

        themeBtn.addEventListener('click', () => {
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateUI(next);
        });

        function toggleSidebar() { 
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('active'); 
            overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none'; 
        }
        
        const sInp = document.getElementById('liveSearch');
        if(sInp){ sInp.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase(), items = document.querySelectorAll('.searchable-item');
            items.forEach(m => { const t = m.innerText.toLowerCase(); m.style.display = t.includes(filter) ? '' : 'none'; });
        }); }
        
        function updateQty(b,c,m=100){ const i = b.parentElement.querySelector('.qty-inp'); let n = parseInt(i.value)+c; if(n>=1&&n<=m) i.value = n; }

        function confirmAction(formId, msg){ 
            Swal.fire({
                title: 'SYSTEM VERIFICATION',
                html: `<span style="font-weight:600; opacity:0.8;">${msg}</span>`,
                icon: 'question',
                iconColor: '#6366f1',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'CONFIRM',
                cancelButtonText: 'CANCEL',
                background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#0f172a' : '#fff',
                color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#fff' : '#0f172a',
                showClass: { popup: 'animate__animated animate__zoomIn animate__faster' },
                hideClass: { popup: 'animate__animated animate__fadeOut animate__faster' }
            }).then((result) => { 
                if (result.isConfirmed) {
                    const form = document.getElementById(formId);
                    const btn = form.querySelector('button');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-atom fa-spin me-2"></i> SYSTEM SYNCING...';
                    form.submit();
                } 
            }); 
        }

        function confirmActionLink(url, btnElement, msg){
            let isDanger = url.includes('tolak') || url.includes('destroy');
            Swal.fire({
                title: 'CORE PROTOCOL',
                html: `<span style="font-weight:600; opacity:0.8;">${msg}</span>`,
                icon: isDanger ? 'warning' : 'info',
                iconColor: isDanger ? '#ff4757' : '#6366f1',
                showCancelButton: true,
                confirmButtonColor: isDanger ? '#ff4757' : '#6366f1',
                cancelButtonColor: '#334155',
                confirmButtonText: 'EXECUTE',
                cancelButtonText: 'BACK',
                background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#0f172a' : '#fff',
                color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#fff' : '#0f172a',
                showClass: { popup: 'animate__animated animate__backInDown animate__faster' },
                hideClass: { popup: 'animate__animated animate__backOutUp animate__faster' }
            }).then((result) => {
                if (result.isConfirmed) {
                    btnElement.disabled = true;
                    btnElement.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
                    window.location.href = url;
                }
            });
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1e293b' : '#fff',
            color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#fff' : '#0f172a',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success')) 
            Toast.fire({ icon: 'success', title: 'ACCESS GRANTED', text: "{{session('success')}}" }); 
        @endif
        @if(session('error')) 
            Toast.fire({ icon: 'error', title: 'SYSTEM BREACH', text: "{{session('error')}}" }); 
        @endif
    </script>
</body>
</html>