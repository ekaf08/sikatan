<div class="card h-100">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-baseline gap-2 mb-2">
            <h2 class="section-title">{{ $judul }}</h2>
            <span class="hint">{{ $hint }}</span>
        </div>
        <div class="table-responsive">
            <table class="table-app" id="{{ $id }}" @if (count($items) > 0) data-datatables @endif>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pos dana</th>
                        <th>Keterangan</th>
                        <th class="num">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td style="white-space:nowrap">{{ $item['tanggal'] }}</td>
                            <td>{{ $item['pos'] }}</td>
                            <td>
                                <div>{{ $item['keterangan'] }}</div>
                                @if (!empty($item['lampiran']))
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @foreach (array_slice($item['lampiran'], 0, 3) as $url)
                                            <a href="{{ $url }}" target="_blank" rel="noopener" title="Lihat bukti">
                                                <img src="{{ $url }}" alt="Bukti" style="width:36px;height:36px;object-fit:cover;border-radius:.35rem;border:1px solid rgba(0,0,0,.12)">
                                            </a>
                                        @endforeach
                                        @if (count($item['lampiran']) > 3)
                                            <span class="hint">+{{ count($item['lampiran']) - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="num">Rp{{ number_format($item['nominal'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center hint py-4">{{ $kosong }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
