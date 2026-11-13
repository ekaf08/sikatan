@php
    $pengguna = auth()->user();
    $inisial = strtoupper(mb_substr($pengguna->name ?? 'P', 0, 1));
@endphp

<header class="app-topbar">
    <button class="btn-icon" type="button" id="sidebarToggle" aria-label="Ciutkan sidebar" aria-controls="appSidebar"
        aria-expanded="true">
        <i class="bi bi-layout-sidebar-inset"></i>
    </button>

    <div class="topbar-title d-none d-md-flex">
        <strong>@yield('topbarTitle', 'Dashboard')</strong>
        <small>@yield('topbarSubtitle', 'Ringkasan kas desa hari ini')</small>
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
                <span class="avatar" style="width:34px;height:34px;font-size:13px">{{ $inisial }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text small text-muted-app">Masuk sebagai
                        {{ $pengguna->name ?? 'Pengguna' }}</span>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="{{ route('password.ganti') }}"><i
                            class="bi bi-key me-2"></i>Ganti kata sandi</a>
                </li>
                @if ($pengguna?->adalahAdmin())
                    <li><a class="dropdown-item" href="{{ route('pengaturan') }}"><i
                                class="bi bi-gear me-2"></i>Pengaturan</a>
                    </li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item" type="submit"><i
                                class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
