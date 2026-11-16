@php
    $batal = $transaksi->trashed() || $transaksi->status === 'batal';
    $jenis = $transaksi->jenis === 'masuk' ? 'Pemasukan' : 'Pengeluaran';
    $tanda = $transaksi->jenis === 'masuk' ? '+' : '-';
    $nominal = $tanda.'Rp'.number_format((float) $transaksi->nominal, 0, ',', '.');
    $tanggal = $transaksi->tanggal?->copy()->locale('id')->translatedFormat('d F Y') ?? '-';
    $dibuat = $transaksi->dibuatOleh?->name ?? '-';
    $dibuatPada = $transaksi->created_at?->copy()->locale('id')->translatedFormat('d M Y H.i') ?? '-';

    $detail = [
        ['label' => 'Pos dana', 'nilai' => $transaksi->posDana?->nama ?? '-'],
        ['label' => 'Kategori', 'nilai' => $transaksi->kategori?->nama ?? '-'],
        ['label' => 'Tanggal transaksi', 'nilai' => $tanggal],
        ['label' => 'Dicatat oleh', 'nilai' => $dibuat],
    ];

    if ($transaksi->rt) {
        $detail[] = ['label' => 'RT / RW', 'nilai' => 'RT '.$transaksi->rt->nomor.' / '.($transaksi->rt->rw?->nama ?? '-')];
    }

    if ($transaksi->jenis === 'masuk' && $transaksi->jumlah_kk_bayar) {
        $detail[] = ['label' => 'Jumlah KK dibayar', 'nilai' => $transaksi->jumlah_kk_bayar.' KK'];
    }

    if ($transaksi->nama_donatur) {
        $detail[] = ['label' => 'Donatur', 'nilai' => $transaksi->donatur_anonim ? 'Hamba Allah' : $transaksi->nama_donatur];
    }

    if ($transaksi->tujuan) {
        $detail[] = ['label' => 'Tujuan', 'nilai' => $transaksi->tujuan];
    }

    if ($transaksi->acara) {
        $detail[] = ['label' => 'Acara', 'nilai' => $transaksi->acara->nama];
    }

    if ($transaksi->anggaranAcara) {
        $detail[] = ['label' => 'Item anggaran', 'nilai' => $transaksi->anggaranAcara->uraian];
    }

    if ($transaksi->penerima) {
        $detail[] = ['label' => 'Penerima', 'nilai' => $transaksi->penerima];
    }

    if ($transaksi->alasan_selisih) {
        $detail[] = ['label' => 'Alasan selisih', 'nilai' => $transaksi->alasan_selisih];
    }

    if ($transaksi->alasan_batal) {
        $detail[] = ['label' => 'Alasan batal', 'nilai' => $transaksi->alasan_batal];
    }

    if ($batal && $transaksi->deleted_at) {
        $detail[] = [
            'label' => 'Dibatalkan pada',
            'nilai' => $transaksi->deleted_at->copy()->locale('id')->translatedFormat('d M Y H.i'),
        ];
    }

    $detail[] = ['label' => 'Terakhir diperbarui', 'nilai' => $transaksi->updated_at?->copy()->locale('id')->translatedFormat('d M Y H.i') ?? '-'];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 pb-3 mb-3 border-bottom">
    <div>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="badge-soft {{ $transaksi->jenis === 'masuk' ? 'badge-in' : 'badge-out' }}">
                {{ $jenis }}
            </span>
            <span class="badge-soft {{ $batal ? 'badge-neutral' : 'badge-in' }}">
                {{ $batal ? 'Batal' : 'Aktif' }}
            </span>
        </div>

        <div class="fs-3 fw-bold" style="color: var(--ks-{{ $transaksi->jenis === 'masuk' ? 'success' : 'warning' }}-text);">
            {{ $nominal }}
        </div>

        <div class="hint mt-1">{{ $tanggal }} &middot; {{ $transaksi->kategori?->nama ?? '-' }}</div>
    </div>

    <div class="text-md-end">
        <div class="form-label-app">Dicatat</div>
        <div>{{ $dibuatPada }}</div>
    </div>
</div>

<div class="row g-3">
    @foreach ($detail as $item)
        <div class="col-12 col-sm-6">
            <div class="form-label-app">{{ $item['label'] }}</div>
            <div class="text-break">{{ $item['nilai'] }}</div>
        </div>
    @endforeach
</div>

@if ($transaksi->catatan_publik || $transaksi->catatan_internal)
    <hr class="my-4">

    <div class="row g-3">
        @if ($transaksi->catatan_publik)
            <div class="col-12 col-sm-6">
                <div class="form-label-app">Catatan publik</div>
                <div class="text-break">{!! nl2br(e($transaksi->catatan_publik)) !!}</div>
            </div>
        @endif

        @if ($transaksi->catatan_internal)
            <div class="col-12 col-sm-6">
                <div class="form-label-app">Catatan internal</div>
                <div class="text-break">{!! nl2br(e($transaksi->catatan_internal)) !!}</div>
            </div>
        @endif
    </div>
@endif

<hr class="my-4">

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h3 class="section-title mb-0">Foto / lampiran</h3>

    @if ($transaksi->sembunyikan_lampiran)
        <span class="badge-soft badge-neutral">Disembunyikan dari publik</span>
    @endif
</div>

@if ($transaksi->lampiran->isEmpty())
    <p class="hint mb-0">Tidak ada foto atau lampiran pada transaksi ini.</p>
@else
    <div class="d-flex flex-wrap gap-2">
        @foreach ($transaksi->lampiran as $lampiran)
            <a href="{{ asset('storage/'.$lampiran->lokasi_berkas) }}" target="_blank" rel="noopener"
                title="Buka lampiran">
                <img src="{{ asset('storage/'.$lampiran->lokasi_berkas) }}" alt="Lampiran transaksi"
                    style="width: 96px; height: 96px; object-fit: cover; border-radius: .5rem; border: 1px solid rgba(0,0,0,.15);">
            </a>
        @endforeach
    </div>
@endif
