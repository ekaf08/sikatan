<header class="app-topbar">
    <button class="btn-icon" type="button" id="sidebarToggle" aria-label="Ciutkan sidebar" aria-controls="appSidebar"
        aria-expanded="true">
        <i class="bi bi-layout-sidebar-inset"></i>
    </button>

    <div class="topbar-title d-none d-md-flex">
        <strong>Dashboard</strong>
        <small>Ringkasan kas desa hari ini</small>
    </div>

    <div class="ms-auto d-flex align-items-center gap-2">
        <div class="topbar-search d-none d-lg-block">
            {{-- <i class="bi bi-search"></i> --}}
            {{-- <input class="form-control" type="search" placeholder="Cari transaksi atau warga..." aria-label="Cari"> --}}
        </div>

        <button class="btn-icon position-relative" type="button" aria-label="Notifikasi">
            <i class="bi bi-bell"></i>
            <span class="notif-dot"></span>
        </button>

        <div class="dropdown">
            <button class="btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                aria-label="Menu pengguna">
                <span class="avatar" style="width:34px;height:34px;font-size:13px">B</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text small text-muted-app">Masuk sebagai Bendahara</span>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="index.html"><i class="bi bi-person me-2"></i>Profil</a>
                </li>
                <li><a class="dropdown-item" href="index.html"><i class="bi bi-gear me-2"></i>Pengaturan</a>
                </li>
                <li><a class="dropdown-item" href="login.html"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
            </ul>
        </div>
    </div>
</header>
