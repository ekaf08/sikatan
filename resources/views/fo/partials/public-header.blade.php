@php
    $menuLayanan = app(\App\Services\MenuLayanan::class);
    $menuPublik = $menuLayanan->ambil('publik', auth()->user());
@endphp
<header class="public-header">
    <div class="public-header-inner">
        <div class="public-brand">
            <strong>Sistem Kampung Babatan</strong>
            <span>Portal informasi dan transparansi kampung</span>
        </div>

        <nav class="public-nav" aria-label="Navigasi publik">
            @if ($menuPublik->isNotEmpty())
                @foreach ($menuPublik as $menu)
                    @php
                        $href = $menuLayanan->href($menu);
                        $isActive = $menuLayanan->aktif($menu);
                    @endphp
                    <a href="{{ $href }}" class="{{ $isActive ? 'active' : '' }}"
                        @if ($isActive) aria-current="page" @endif
                        @if ($menu->target === '_blank') target="_blank" rel="noopener" @endif>{{ $menu->judul }}</a>
                @endforeach
            @else
                <a href="{{ route('publik') }}" class="{{ request()->routeIs('publik') ? 'active' : '' }}"
                    @if (request()->routeIs('publik')) aria-current="page" @endif>Beranda</a>
                <a href="{{ route('iuran-kematian') }}"
                    class="{{ request()->routeIs('iuran-kematian') ? 'active' : '' }}"
                    @if (request()->routeIs('iuran-kematian')) aria-current="page" @endif>Iuran Kematian</a>
                <a href="{{ route('donasi') }}" class="{{ request()->routeIs('donasi') ? 'active' : '' }}"
                    @if (request()->routeIs('donasi')) aria-current="page" @endif>Donasi</a>
                <a href="{{ route('login') }}">Masuk bendahara</a>
            @endif
        </nav>
    </div>
</header>
