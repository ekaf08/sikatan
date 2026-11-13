@php
    $pengguna = auth()->user();
    $inisial = strtoupper(mb_substr($pengguna->name ?? 'P', 0, 1));
@endphp

<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <a class="brand-link" href="{{ route('dashboard') }}">
            <span class="brand-mark"><i class="bi bi-wallet2"></i></span>
            <span class="brand-text">
                <span class="brand-title">Kas Desa Babatan</span>
                <span class="brand-sub">Area bendahara</span>
            </span>
        </a>
        <button class="sidebar-close d-lg-none" type="button" id="sidebarClose" aria-label="Tutup menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="sidebar-nav" aria-label="Navigasi utama">
        <a class="nav-item-app @if (request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}"
            @if (request()->routeIs('dashboard')) aria-current="page" @endif data-label="Dashboard">
            <i class="bi bi-grid-1x2"></i><span class="nav-text">Dashboard</span>
        </a>

        <div class="nav-section"><span>Transaksi</span></div>
        <a class="nav-item-app @if (request()->routeIs('pemasukan')) active @endif" href="{{ route('pemasukan') }}"
            @if (request()->routeIs('pemasukan')) aria-current="page" @endif data-label="Pemasukan">
            <i class="bi bi-arrow-down-left-circle"></i><span class="nav-text">Pemasukan</span>
        </a>
        <a class="nav-item-app @if (request()->routeIs('pengeluaran')) active @endif" href="{{ route('pengeluaran') }}"
            @if (request()->routeIs('pengeluaran')) aria-current="page" @endif data-label="Pengeluaran">
            <i class="bi bi-arrow-up-right-circle"></i><span class="nav-text">Pengeluaran</span>
        </a>
        <a class="nav-item-app @if (request()->routeIs('transaksi')) active @endif" href="{{ route('transaksi') }}"
            @if (request()->routeIs('transaksi')) aria-current="page" @endif data-label="Daftar transaksi">
            <i class="bi bi-list-ul"></i><span class="nav-text">Daftar transaksi</span>
        </a>
        <a class="nav-item-app @if (request()->routeIs('mutasi-dana')) active @endif" href="{{ route('mutasi-dana') }}"
            @if (request()->routeIs('mutasi-dana')) aria-current="page" @endif data-label="Mutasi dana">
            <i class="bi bi-arrow-left-right"></i><span class="nav-text">Mutasi dana</span>
        </a>

        <div class="nav-section"><span>Kegiatan dan laporan</span></div>
        <a class="nav-item-app @if (request()->routeIs('acara')) active @endif" href="{{ route('acara') }}"
            @if (request()->routeIs('acara')) aria-current="page" @endif data-label="Acara sedekah bumi">
            <i class="bi bi-calendar-event"></i><span class="nav-text">Acara sedekah bumi</span>
        </a>
        <a class="nav-item-app @if (request()->routeIs('laporan')) active @endif" href="{{ route('laporan') }}"
            @if (request()->routeIs('laporan')) aria-current="page" @endif data-label="Laporan dan ekspor">
            <i class="bi bi-file-earmark-bar-graph"></i><span class="nav-text">Laporan dan ekspor</span>
        </a>

        <div class="nav-section"><span>Pengelolaan</span></div>
        <a class="nav-item-app @if (request()->routeIs('master-*')) active @endif" href="{{ route('master-wilayah') }}"
            @if (request()->routeIs('master-*')) aria-current="page" @endif data-label="Master data">
            <i class="bi bi-database"></i><span class="nav-text">Master data</span>
        </a>
        <div class="nav-sub">
            <a class="nav-sub-item @if (request()->routeIs('master-wilayah')) active @endif"
                href="{{ route('master-wilayah') }}">Wilayah RW/RT</a>
            <a class="nav-sub-item @if (request()->routeIs('master-pos-dana')) active @endif"
                href="{{ route('master-pos-dana') }}">Pos dana</a>
            <a class="nav-sub-item @if (request()->routeIs('master-kategori')) active @endif"
                href="{{ route('master-kategori') }}">Kategori</a>
        </div>

        @if ($pengguna?->adalahAdmin())
            <a class="nav-item-app @if (request()->routeIs('pengaturan')) active @endif" href="{{ route('pengaturan') }}"
                @if (request()->routeIs('pengaturan')) aria-current="page" @endif data-label="Pengaturan">
                <i class="bi bi-gear"></i><span class="nav-text">Pengaturan</span>
            </a>
        @endif
        <a class="nav-item-app @if (request()->routeIs('log-audit')) active @endif" href="{{ route('log-audit') }}"
            @if (request()->routeIs('log-audit')) aria-current="page" @endif data-label="Log audit">
            <i class="bi bi-shield-check"></i><span class="nav-text">Log audit</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-mini">
            <span class="avatar">{{ $inisial }}</span>
            <span class="user-text">
                <strong>{{ $pengguna->name ?? 'Pengguna' }}</strong>
                <small>{{ $pengguna->username ?? $pengguna->email ?? '' }}</small>
            </span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="nav-item-app w-100 border-0 bg-transparent text-start" type="submit" data-label="Keluar">
                <i class="bi bi-box-arrow-right"></i><span class="nav-text">Keluar</span>
            </button>
        </form>
    </div>
</aside>
