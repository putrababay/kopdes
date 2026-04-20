<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopdes Admin - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; color: #334155; }
        .navbar { background: #ffffff; border-bottom: 1px solid #e2e8f0; }
        .main-content { padding-top: 20px; padding-bottom: 100px; } /* Space for bottom menu */
        
        /* Minimalist Bottom Navigation for Mobile */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            z-index: 1030;
        }
        .nav-item-mobile { text-align: center; color: #64748b; text-decoration: none; font-size: 0.75rem; }
        .nav-item-mobile i { display: block; font-size: 1.25rem; margin-bottom: 2px; }
        .nav-item-mobile.active { color: #4e73df; font-weight: 600; }
        
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-card { padding: 20px; text-align: center; }
    </style>
     <script src="https://code.highcharts.com/highcharts.js"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

     
     
</head>
<body>

    <nav class="navbar sticky-top px-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <span class="navbar-brand fw-bold text-primary">KOPDES</span>
            <div class="dropdown">
                <a href="#" class="text-decoration-none text-dark" id="userMenu" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container main-content">
        @yield('content')
    </main>

    <div class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="nav-item-mobile {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="#" class="nav-item-mobile">
            <i class="fas fa-wallet"></i>
            <span>Simpanan</span>
        </a>
        <a href="{{ route('pinjam.index') }}" class="nav-item-mobile {{ request()->is('pinjam') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Pinjaman</span>
        </a>
         <a href="#" class="nav-item-mobile">
            <i class="fas fa-mobile"></i>
            <span>Pulsa</span>
        </a>
        <a href="{{ route('nasabah.index') }}" class="nav-item-mobile {{ request()->is('nasabah') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Anggota</span>
        </a>
    </div>

   
  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>