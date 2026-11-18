<div class="card h-100">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-baseline gap-2 mb-3">
            <h2 class="section-title">{{ $judul }}</h2>
            <span class="hint">{{ $hint }}</span>
        </div>

        @if (count($grafik['items']) > 0)
            @php
                $gradien = [];
                $mulaiPersen = 0;
                foreach ($grafik['items'] as $item) {
                    $akhirPersen = $mulaiPersen + $item['persen'];
                    $gradien[] = $item['warna'].' '.round($mulaiPersen, 2).'% '.round($akhirPersen, 2).'%';
                    $mulaiPersen = $akhirPersen;
                }
            @endphp
            <div class="pie-wrap">
                <div class="pie-chart" style="background: conic-gradient({{ implode(', ', $gradien) }})"
                    role="img" aria-label="{{ $judul }}"></div>
                <div class="pie-legend">
                    @foreach ($grafik['items'] as $item)
                        <div class="pie-legend-item">
                            <span class="pie-dot" style="background:{{ $item['warna'] }}"></span>
                            <span class="pie-legend-name" title="{{ $item['nama'] }}">{{ $item['nama'] }}</span>
                            <span class="pie-legend-value">Rp{{ number_format($item['total'], 0, ',', '.') }}</span>
                            <span class="pie-legend-percent">{{ number_format($item['persen'], 1, ',', '.') }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="hint mt-3 mb-0">{{ $totalLabel }}:
                Rp{{ number_format($grafik['total'], 0, ',', '.') }}</p>
        @else
            <p class="hint mb-0">{{ $kosong }}</p>
        @endif
    </div>
</div>
