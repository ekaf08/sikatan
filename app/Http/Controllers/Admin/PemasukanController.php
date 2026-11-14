<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Lampiran;
use App\Models\Pengaturan;
use App\Models\PosDana;
use App\Models\Rt;
use App\Models\Transaksi;
use App\Services\KompresiGambar;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PemasukanController extends Controller
{
    private const TIPE = [
        'setoran' => ['pos' => 'kematian', 'kategori' => 'Iuran Kematian'],
        'jumat' => ['pos' => 'masjid', 'kategori' => 'Uang Kotak Jumat'],
        'donasi' => ['pos' => 'masjid', 'kategori' => 'Donasi Masjid'],
    ];

    private const LABEL_TIPE = [
        'setoran' => 'Setoran iuran kematian',
        'jumat' => 'Uang kotak Jumat',
        'donasi' => 'Donasi',
        'lain' => 'Lain-lain',
    ];

    public function __construct(private readonly KompresiGambar $kompresi) {}

    public function index(): View
    {
        return view('pages.pemasukan', array_merge([
            'daftarPos' => PosDana::query()->orderBy('nama')->get(),
            'daftarKategoriLain' => Kategori::query()
                ->where('jenis', 'masuk')
                ->whereNotIn('nama', array_column(self::TIPE, 'kategori'))
                ->orderBy('nama')
                ->get(),
            'daftarRt' => Rt::query()->with('rw')->orderBy('rw_id')->orderBy('nomor')->get(),
            'iuranPerKk' => (int) (Pengaturan::query()->where('kunci', 'iuran_kematian_per_kk')->value('nilai') ?? 5000),
        ], $this->statistikData()));
    }

    public function data(): JsonResponse
    {
        $query = Transaksi::query()
            ->with(['posDana', 'kategori', 'rt.rw', 'lampiran'])
            ->where('jenis', 'masuk')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('tanggal_format', fn (Transaksi $transaksi): string => $transaksi->tanggal
                ? $transaksi->tanggal->copy()->locale('id')->translatedFormat('d M Y')
                : '-')
            ->addColumn('tipe', fn (Transaksi $transaksi): string => $this->tipeTransaksi($transaksi))
            ->addColumn('tipe_label', fn (Transaksi $transaksi): string => self::LABEL_TIPE[$this->tipeTransaksi($transaksi)])
            ->addColumn('pos_nama', fn (Transaksi $transaksi): string => $transaksi->posDana?->nama ?? '-')
            ->addColumn('kategori_nama', fn (Transaksi $transaksi): string => $transaksi->kategori?->nama ?? '-')
            ->addColumn('rincian', fn (Transaksi $transaksi): string => $this->rincian($transaksi))
            ->addColumn('nominal_format', fn (Transaksi $transaksi): string => number_format((float) $transaksi->nominal, 0, ',', '.'))
            ->addColumn('lampiran', fn (Transaksi $transaksi): string => $this->lampiranHtml($transaksi))
            ->addColumn('status_label', fn (Transaksi $transaksi): string => $transaksi->status === 'batal'
                ? '<span class="badge-soft badge-neutral">Batal</span>'
                : '<span class="badge-soft badge-in">Aktif</span>')
            ->addColumn('aksi', function (Transaksi $transaksi): string {
                $lampiran = $transaksi->lampiran->map(fn (Lampiran $item): array => [
                    'id' => $item->id,
                    'url' => asset('storage/'.$item->lokasi_berkas),
                    'nama' => basename($item->lokasi_berkas),
                ])->values();

                return '<button type="button" class="btn btn-sm btn-outline-secondary btn-edit-masuk"'
                    .' data-id="'.$transaksi->id.'"'
                    .' data-tipe="'.$this->tipeTransaksi($transaksi).'"'
                    .' data-tanggal="'.($transaksi->tanggal?->format('Y-m-d') ?? '').'"'
                    .' data-nominal="'.e((int) $transaksi->nominal).'"'
                    .' data-rt="'.($transaksi->rt_id ?? '').'"'
                    .' data-jumlah-kk="'.($transaksi->jumlah_kk_bayar ?? '').'"'
                    .' data-nama-donatur="'.e($transaksi->nama_donatur ?? '').'"'
                    .' data-anonim="'.($transaksi->donatur_anonim ? '1' : '0').'"'
                    .' data-tujuan="'.e($transaksi->tujuan ?? '').'"'
                    .' data-pos="'.$transaksi->pos_dana_id.'"'
                    .' data-kategori="'.$transaksi->kategori_id.'"'
                    .' data-catatan-publik="'.e($transaksi->catatan_publik ?? '').'"'
                    .' data-catatan-internal="'.e($transaksi->catatan_internal ?? '').'"'
                    .' data-lampiran="'.e($lampiran->toJson()).'" title="Ubah">'
                    .'<i class="bi bi-pencil"></i></button>'
                    .'<button type="button" class="btn btn-sm btn-outline-danger btn-hapus-masuk ms-1"'
                    .' data-id="'.$transaksi->id.'"'
                    .' data-info="'.e(($transaksi->kategori?->nama ?? 'Pemasukan').' Rp'.number_format((float) $transaksi->nominal, 0, ',', '.')).'" title="Hapus">'
                    .'<i class="bi bi-trash"></i></button>';
            })
            ->rawColumns(['rincian', 'lampiran', 'status_label', 'aksi'])
            ->toJson();
    }

    public function simpan(Request $request): JsonResponse
    {
        $data = $this->validasi($request);
        $meta = $this->resolveMeta($data);

        $transaksi = Transaksi::create([
            'pos_dana_id' => $meta['pos']->id,
            'kategori_id' => $meta['kategori']->id,
            'rt_id' => $data['tipe'] === 'setoran' ? ($data['rt_id'] ?? null) : null,
            'acara_id' => null,
            'dibuat_oleh' => $request->user()->id,
            'jenis' => 'masuk',
            'tanggal' => $data['tanggal'],
            'nominal' => $data['nominal'],
            'jumlah_kk_bayar' => $data['tipe'] === 'setoran' ? ($data['jumlah_kk_bayar'] ?? null) : null,
            'nama_donatur' => $data['tipe'] === 'donasi' ? ($data['nama_donatur'] ?? null) : null,
            'donatur_anonim' => $data['tipe'] === 'donasi' ? $request->boolean('donatur_anonim') : false,
            'tujuan' => $data['tipe'] === 'donasi' ? ($data['tujuan'] ?? null) : null,
            'catatan_publik' => $data['catatan_publik'] ?? null,
            'catatan_internal' => $data['catatan_internal'] ?? null,
            'status' => 'aktif',
        ]);

        $this->simpanLampiran($request, $transaksi, $data['tipe']);

        return response()->json([
            'message' => 'Pemasukan sebesar Rp'.number_format((float) $transaksi->nominal, 0, ',', '.').' berhasil dicatat.',
        ]);
    }

    public function ubah(Request $request, Transaksi $pemasukan): JsonResponse
    {
        if ($pemasukan->jenis !== 'masuk') {
            return response()->json(['message' => 'Data bukan merupakan pemasukan.'], 422);
        }

        $data = $this->validasi($request);
        $meta = $this->resolveMeta($data);

        $pemasukan->update([
            'pos_dana_id' => $meta['pos']->id,
            'kategori_id' => $meta['kategori']->id,
            'rt_id' => $data['tipe'] === 'setoran' ? ($data['rt_id'] ?? null) : null,
            'tanggal' => $data['tanggal'],
            'nominal' => $data['nominal'],
            'jumlah_kk_bayar' => $data['tipe'] === 'setoran' ? ($data['jumlah_kk_bayar'] ?? null) : null,
            'nama_donatur' => $data['tipe'] === 'donasi' ? ($data['nama_donatur'] ?? null) : null,
            'donatur_anonim' => $data['tipe'] === 'donasi' ? $request->boolean('donatur_anonim') : false,
            'tujuan' => $data['tipe'] === 'donasi' ? ($data['tujuan'] ?? null) : null,
            'catatan_publik' => $data['catatan_publik'] ?? null,
            'catatan_internal' => $data['catatan_internal'] ?? null,
        ]);

        if (! in_array($data['tipe'], ['donasi', 'lain'], true)) {
            $pemasukan->lampiran()->delete();
        } else {
            $this->hapusLampiran($request, $pemasukan);
            $this->simpanLampiran($request, $pemasukan, $data['tipe']);
        }

        return response()->json([
            'message' => 'Pemasukan berhasil diperbarui.',
        ]);
    }

    public function hapus(Transaksi $pemasukan): JsonResponse
    {
        if ($pemasukan->jenis !== 'masuk') {
            return response()->json(['message' => 'Data bukan merupakan pemasukan.'], 422);
        }

        $nominal = (float) $pemasukan->nominal;

        try {
            $pemasukan->lampiran()->delete();
            $pemasukan->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Pemasukan tidak dapat dihapus karena masih terhubung dengan data lain.',
            ], 422);
        }

        return response()->json([
            'message' => 'Pemasukan sebesar Rp'.number_format($nominal, 0, ',', '.').' berhasil dibatalkan.',
        ]);
    }

    public function statistik(): JsonResponse
    {
        $data = $this->statistikData();

        return response()->json([
            'total_pemasukan' => $data['totalPemasukan'],
            'jumlah_transaksi' => $data['jumlahTransaksi'],
            'pemasukan_bulan_ini' => $data['pemasukanBulanIni'],
            'terakhir_kategori' => $data['terakhirKategori'],
            'terakhir_tanggal' => $data['terakhirTanggal'],
        ]);
    }

    private function simpanLampiran(Request $request, Transaksi $transaksi, string $tipe): void
    {
        if (! in_array($tipe, ['donasi', 'lain'], true)) {
            return;
        }

        $berkas = $request->file('lampiran', []);

        if (! is_array($berkas)) {
            $berkas = $berkas ? [$berkas] : [];
        }

        foreach ($berkas as $gambar) {
            if (! $gambar) {
                continue;
            }

            $lokasi = $this->kompresi->simpan($gambar, 'lampiran', 500);

            $transaksi->lampiran()->create([
                'lokasi_berkas' => $lokasi,
                'tampil_publik' => true,
            ]);
        }
    }

    private function hapusLampiran(Request $request, Transaksi $transaksi): void
    {
        $daftar = $request->input('hapus_lampiran', []);

        if (! is_array($daftar) || $daftar === []) {
            return;
        }

        $transaksi->lampiran()->whereIn('id', $daftar)->delete();
    }

    private function validasi(Request $request): array
    {
        $tipe = $request->input('tipe');

        $aturan = [
            'tipe' => ['required', Rule::in(array_keys(self::LABEL_TIPE))],
            'tanggal' => ['required', 'date'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'catatan_publik' => ['nullable', 'string', 'max:1000'],
            'catatan_internal' => ['nullable', 'string', 'max:2000'],
        ];

        if ($tipe === 'setoran') {
            $aturan['rt_id'] = ['required', Rule::exists('rt', 'id')];
            $aturan['jumlah_kk_bayar'] = ['required', 'integer', 'min:1'];
        }

        if ($tipe === 'donasi') {
            $aturan['nama_donatur'] = ['nullable', 'string', 'max:255'];
            $aturan['donatur_anonim'] = ['nullable', 'boolean'];
            $aturan['tujuan'] = ['nullable', 'string', 'max:255'];
        }

        if ($tipe === 'lain') {
            $aturan['pos_dana_id'] = ['required', Rule::exists('pos_dana', 'id')];
            $aturan['kategori_id'] = ['required', Rule::exists('kategori', 'id')->where('jenis', 'masuk')];
        }

        if (in_array($tipe, ['donasi', 'lain'], true)) {
            $aturan['lampiran'] = ['nullable', 'array'];
            $aturan['lampiran.*'] = ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192'];
            $aturan['hapus_lampiran'] = ['nullable', 'array'];
            $aturan['hapus_lampiran.*'] = ['integer'];
        }

        $validator = Validator::make($request->all(), $aturan, $this->pesanValidasi());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private function resolveMeta(array $data): array
    {
        if ($data['tipe'] === 'lain') {
            return [
                'pos' => PosDana::query()->findOrFail($data['pos_dana_id']),
                'kategori' => Kategori::query()->findOrFail($data['kategori_id']),
            ];
        }

        $konfig = self::TIPE[$data['tipe']];

        $kategori = Kategori::query()
            ->where('jenis', 'masuk')
            ->where('nama', $konfig['kategori'])
            ->first();

        $pos = PosDana::query()->where('jenis', $konfig['pos'])->first();

        if (! $kategori || ! $pos) {
            throw ValidationException::withMessages([
                'tipe' => 'Master pos dana atau kategori untuk jenis pemasukan ini belum tersedia.',
            ]);
        }

        return ['pos' => $pos, 'kategori' => $kategori];
    }

    private function tipeTransaksi(Transaksi $transaksi): string
    {
        return match ($transaksi->kategori?->nama) {
            'Iuran Kematian' => 'setoran',
            'Uang Kotak Jumat' => 'jumat',
            'Donasi Masjid' => 'donasi',
            default => 'lain',
        };
    }

    private function rincian(Transaksi $transaksi): string
    {
        $bagian = [];

        if ($transaksi->rt) {
            $bagian[] = 'RT '.$transaksi->rt->nomor.' / '.($transaksi->rt->rw?->nama ?? '-');
        }

        if ($transaksi->jumlah_kk_bayar) {
            $bagian[] = $transaksi->jumlah_kk_bayar.' KK';
        }

        if ($transaksi->nama_donatur) {
            $bagian[] = $transaksi->donatur_anonim ? 'Hamba Allah' : $transaksi->nama_donatur;
        }

        if ($transaksi->tujuan) {
            $bagian[] = $transaksi->tujuan;
        }

        if ($bagian === [] && $transaksi->catatan_publik) {
            $bagian[] = Str::limit($transaksi->catatan_publik, 45);
        }

        return $bagian === []
            ? '<span class="hint">&mdash;</span>'
            : e(implode(' · ', $bagian));
    }

    private function lampiranHtml(Transaksi $transaksi): string
    {
        if ($transaksi->lampiran->isEmpty()) {
            return '<span class="hint">&mdash;</span>';
        }

        $html = '<div class="d-flex flex-wrap gap-1">';

        foreach ($transaksi->lampiran->take(3) as $item) {
            $url = e(asset('storage/'.$item->lokasi_berkas));
            $html .= '<a href="'.$url.'" target="_blank" rel="noopener">'
                .'<img src="'.$url.'" alt="lampiran" style="width:38px;height:38px;object-fit:cover;border-radius:.35rem;border:1px solid rgba(0,0,0,.15)">'
                .'</a>';
        }

        if ($transaksi->lampiran->count() > 3) {
            $html .= '<span class="badge-soft badge-neutral">+'.($transaksi->lampiran->count() - 3).'</span>';
        }

        return $html.'</div>';
    }

    private function statistikData(): array
    {
        $base = Transaksi::query()->where('jenis', 'masuk')->where('status', 'aktif');

        $totalPemasukan = (float) (clone $base)->sum('nominal');
        $jumlahTransaksi = (clone $base)->count();
        $pemasukanBulanIni = (float) (clone $base)
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('nominal');

        $terakhir = (clone $base)->with('kategori')->orderByDesc('tanggal')->orderByDesc('id')->first();

        return [
            'totalPemasukan' => $totalPemasukan,
            'jumlahTransaksi' => $jumlahTransaksi,
            'pemasukanBulanIni' => $pemasukanBulanIni,
            'terakhirKategori' => $terakhir?->kategori?->nama,
            'terakhirTanggal' => $terakhir?->tanggal
                ? $terakhir->tanggal->copy()->locale('id')->translatedFormat('d M Y')
                : null,
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'tipe.required' => 'Jenis pemasukan wajib dipilih.',
            'tipe.in' => 'Jenis pemasukan tidak valid.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Tanggal tidak valid.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal harus lebih besar dari nol.',
            'rt_id.required' => 'RT wajib dipilih.',
            'rt_id.exists' => 'RT tidak valid.',
            'jumlah_kk_bayar.required' => 'Jumlah KK wajib diisi.',
            'jumlah_kk_bayar.integer' => 'Jumlah KK harus berupa angka bulat.',
            'jumlah_kk_bayar.min' => 'Jumlah KK minimal 1.',
            'nama_donatur.max' => 'Nama donatur maksimal 255 karakter.',
            'tujuan.max' => 'Tujuan donasi maksimal 255 karakter.',
            'pos_dana_id.required' => 'Pos dana wajib dipilih.',
            'pos_dana_id.exists' => 'Pos dana tidak valid.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid atau bukan kategori pemasukan.',
            'catatan_publik.max' => 'Catatan publik maksimal 1000 karakter.',
            'catatan_internal.max' => 'Catatan internal maksimal 2000 karakter.',
            'lampiran.array' => 'Lampiran tidak valid.',
            'lampiran.*.image' => 'Berkas lampiran harus berupa gambar.',
            'lampiran.*.mimes' => 'Format gambar harus jpg, jpeg, png, webp, atau gif.',
            'lampiran.*.max' => 'Ukuran gambar maksimal 8 MB sebelum dikompresi.',
        ];
    }
}
