<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Acara;
use App\Models\Kategori;
use App\Models\Lampiran;
use App\Models\PosDana;
use App\Models\Transaksi;
use App\Services\KompresiGambar;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PengeluaranController extends Controller
{
    public function __construct(private readonly KompresiGambar $kompresi)
    {
    }

    public function index(): View
    {
        return view('pages.pengeluaran', array_merge([
            'daftarPos' => PosDana::query()->orderBy('nama')->get(),
            'daftarKategori' => Kategori::query()->where('jenis', 'keluar')->orderBy('nama')->get(),
            'daftarAcara' => Acara::query()->orderByDesc('tahun')->get(),
        ], $this->statistikData()));
    }

    public function data(): JsonResponse
    {
        $query = Transaksi::query()
            ->with(['posDana', 'kategori', 'acara', 'lampiran'])
            ->where('jenis', 'keluar')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('tanggal_format', fn (Transaksi $transaksi): string => $transaksi->tanggal
                ? $transaksi->tanggal->copy()->locale('id')->translatedFormat('d M Y')
                : '-')
            ->addColumn('pos_nama', fn (Transaksi $transaksi): string => $transaksi->posDana?->nama ?? '-')
            ->addColumn('kategori_nama', fn (Transaksi $transaksi): string => $transaksi->kategori?->nama ?? '-')
            ->addColumn('penerima', fn (Transaksi $transaksi): ?string => $transaksi->penerima)
            ->addColumn('acara_nama', fn (Transaksi $transaksi): string => $transaksi->acara?->nama ?? '-')
            ->addColumn('nominal_format', fn (Transaksi $transaksi): string => number_format((float) $transaksi->nominal, 0, ',', '.'))
            ->addColumn('foto', function (Transaksi $transaksi): string {
                if ($transaksi->lampiran->isEmpty()) {
                    return '';
                }

                $html = '<div class="d-flex flex-wrap gap-1">';

                foreach ($transaksi->lampiran as $item) {
                    $url = e(asset('storage/' . $item->lokasi_berkas));
                    $html .= '<a href="' . $url . '" target="_blank" rel="noopener">'
                        . '<img src="' . $url . '" alt="nota" style="width:38px;height:38px;object-fit:cover;border-radius:.375rem;border:1px solid rgba(0,0,0,.15)">'
                        . '</a>';
                }

                return $html . '</div>';
            })
            ->addColumn('status_label', fn (Transaksi $transaksi): string => $transaksi->status === 'batal' ? 'Batal' : 'Aktif')
            ->addColumn('aksi', function (Transaksi $transaksi): string {
                $catatanPublik = e($transaksi->catatan_publik ?? '');
                $catatanInternal = e($transaksi->catatan_internal ?? '');
                $alasanSelisih = e($transaksi->alasan_selisih ?? '');
                $penerima = e($transaksi->penerima ?? '');

                $lampiran = $transaksi->lampiran->map(fn (Lampiran $item): array => [
                    'id' => $item->id,
                    'url' => asset('storage/' . $item->lokasi_berkas),
                    'nama' => basename($item->lokasi_berkas),
                ])->values();

                $lampiranJson = e($lampiran->toJson());

                return '<button type="button" class="btn btn-sm btn-outline-secondary btn-edit-keluar"'
                    . ' data-id="' . $transaksi->id . '"'
                    . ' data-tanggal="' . ($transaksi->tanggal?->format('Y-m-d') ?? '') . '"'
                    . ' data-pos="' . $transaksi->pos_dana_id . '"'
                    . ' data-kategori="' . $transaksi->kategori_id . '"'
                    . ' data-acara="' . ($transaksi->acara_id ?? '') . '"'
                    . ' data-nominal="' . e((int) $transaksi->nominal) . '"'
                    . ' data-alasan="' . $alasanSelisih . '"'
                    . ' data-penerima="' . $penerima . '"'
                    . ' data-catatan-publik="' . $catatanPublik . '"'
                    . ' data-catatan-internal="' . $catatanInternal . '"'
                    . ' data-sembunyikan="' . ($transaksi->sembunyikan_lampiran ? '1' : '0') . '"'
                    . ' data-lampiran="' . $lampiranJson . '" title="Ubah">'
                    . '<i class="bi bi-pencil"></i></button>'
                    . '<button type="button" class="btn btn-sm btn-outline-danger btn-hapus-keluar ms-1"'
                    . ' data-id="' . $transaksi->id . '"'
                    . ' data-info="' . e(($transaksi->kategori?->nama ?? 'Pengeluaran') . ' Rp' . number_format((float) $transaksi->nominal, 0, ',', '.')) . '" title="Hapus">'
                    . '<i class="bi bi-trash"></i></button>';
            })
            ->rawColumns(['foto', 'aksi'])
            ->toJson();
    }

    public function simpan(Request $request): JsonResponse
    {
        $data = $this->validasi($request);

        $transaksi = Transaksi::create([
            'pos_dana_id' => $data['pos_dana_id'],
            'kategori_id' => $data['kategori_id'],
            'acara_id' => $data['acara_id'] ?? null,
            'dibuat_oleh' => $request->user()->id,
            'jenis' => 'keluar',
            'tanggal' => $data['tanggal'],
            'nominal' => $data['nominal'],
            'alasan_selisih' => $data['alasan_selisih'] ?? null,
            'catatan_publik' => $data['catatan_publik'] ?? null,
            'catatan_internal' => $data['catatan_internal'] ?? null,
            'penerima' => $data['penerima'] ?? null,
            'sembunyikan_lampiran' => $request->boolean('sembunyikan_lampiran'),
            'status' => 'aktif',
        ]);

        $this->simpanLampiran($request, $transaksi);

        return response()->json([
            'message' => 'Pengeluaran sebesar Rp' . number_format((float) $transaksi->nominal, 0, ',', '.') . ' berhasil dicatat.',
        ]);
    }

    public function ubah(Request $request, Transaksi $pengeluaran): JsonResponse
    {
        if ($pengeluaran->jenis !== 'keluar') {
            return response()->json(['message' => 'Data bukan merupakan pengeluaran.'], 422);
        }

        $data = $this->validasi($request);

        $pengeluaran->update([
            'pos_dana_id' => $data['pos_dana_id'],
            'kategori_id' => $data['kategori_id'],
            'acara_id' => $data['acara_id'] ?? null,
            'tanggal' => $data['tanggal'],
            'nominal' => $data['nominal'],
            'alasan_selisih' => $data['alasan_selisih'] ?? null,
            'catatan_publik' => $data['catatan_publik'] ?? null,
            'catatan_internal' => $data['catatan_internal'] ?? null,
            'penerima' => $data['penerima'] ?? null,
            'sembunyikan_lampiran' => $request->boolean('sembunyikan_lampiran'),
        ]);

        $this->hapusLampiran($request, $pengeluaran);
        $this->simpanLampiran($request, $pengeluaran);

        return response()->json([
            'message' => 'Pengeluaran berhasil diperbarui.',
        ]);
    }

    public function hapus(Transaksi $pengeluaran): JsonResponse
    {
        if ($pengeluaran->jenis !== 'keluar') {
            return response()->json(['message' => 'Data bukan merupakan pengeluaran.'], 422);
        }

        $nominal = (float) $pengeluaran->nominal;

        try {
            $pengeluaran->lampiran()->delete();
            $pengeluaran->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Pengeluaran tidak dapat dihapus karena masih terhubung dengan data lain.',
            ], 422);
        }

        return response()->json([
            'message' => 'Pengeluaran sebesar Rp' . number_format($nominal, 0, ',', '.') . ' berhasil dibatalkan.',
        ]);
    }

    public function statistik(): JsonResponse
    {
        $data = $this->statistikData();

        return response()->json([
            'total_pengeluaran' => $data['totalPengeluaran'],
            'jumlah_transaksi' => $data['jumlahTransaksi'],
            'pengeluaran_bulan_ini' => $data['pengeluaranBulanIni'],
            'terakhir_kategori' => $data['terakhirKategori'],
            'terakhir_tanggal' => $data['terakhirTanggal'],
        ]);
    }

    private function simpanLampiran(Request $request, Transaksi $transaksi): void
    {
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
                'tampil_publik' => ! $request->boolean('sembunyikan_lampiran'),
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
        $validator = Validator::make($request->all(), [
            'tanggal' => ['required', 'date'],
            'pos_dana_id' => ['required', Rule::exists('pos_dana', 'id')],
            'kategori_id' => ['required', Rule::exists('kategori', 'id')->where('jenis', 'keluar')],
            'acara_id' => ['nullable', Rule::exists('acara', 'id')],
            'nominal' => ['required', 'numeric', 'min:1'],
            'alasan_selisih' => ['nullable', 'string', 'max:1000'],
            'catatan_publik' => ['nullable', 'string', 'max:1000'],
            'catatan_internal' => ['nullable', 'string', 'max:2000'],
            'penerima' => ['nullable', 'string', 'max:255'],
            'sembunyikan_lampiran' => ['nullable', 'boolean'],
            'lampiran' => ['nullable', 'array'],
            'lampiran.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192'],
            'hapus_lampiran' => ['nullable', 'array'],
            'hapus_lampiran.*' => ['integer'],
        ], $this->pesanValidasi());

        $validator->after(function ($validator) use ($request) {
            $kategori = Kategori::find($request->input('kategori_id'));
            $standar = $kategori?->nominal_standar;

            if ($standar === null) {
                return;
            }

            $nominal = (float) $request->input('nominal');

            if (round($nominal, 2) !== round((float) $standar, 2) && ! $request->filled('alasan_selisih')) {
                $validator->errors()->add(
                    'alasan_selisih',
                    'Alasan wajib diisi karena nominal berbeda dari standar Rp' . number_format((float) $standar, 0, ',', '.') . '.'
                );
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private function statistikData(): array
    {
        $base = Transaksi::query()->where('jenis', 'keluar')->where('status', 'aktif');

        $totalPengeluaran = (float) (clone $base)->sum('nominal');
        $jumlahTransaksi = (clone $base)->count();
        $pengeluaranBulanIni = (float) (clone $base)
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('nominal');

        $terakhir = (clone $base)->with('kategori')->orderByDesc('tanggal')->orderByDesc('id')->first();

        return [
            'totalPengeluaran' => $totalPengeluaran,
            'jumlahTransaksi' => $jumlahTransaksi,
            'pengeluaranBulanIni' => $pengeluaranBulanIni,
            'terakhirKategori' => $terakhir?->kategori?->nama,
            'terakhirTanggal' => $terakhir?->tanggal
                ? $terakhir->tanggal->copy()->locale('id')->translatedFormat('d M Y')
                : null,
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Tanggal tidak valid.',
            'pos_dana_id.required' => 'Pos dana wajib dipilih.',
            'pos_dana_id.exists' => 'Pos dana tidak valid.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid atau bukan kategori pengeluaran.',
            'acara_id.exists' => 'Acara tidak valid.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal harus lebih besar dari nol.',
            'alasan_selisih.max' => 'Alasan maksimal 1000 karakter.',
            'catatan_publik.max' => 'Catatan publik maksimal 1000 karakter.',
            'catatan_internal.max' => 'Catatan internal maksimal 2000 karakter.',
            'penerima.max' => 'Nama penerima maksimal 255 karakter.',
            'lampiran.array' => 'Lampiran tidak valid.',
            'lampiran.*.image' => 'Berkas lampiran harus berupa gambar.',
            'lampiran.*.mimes' => 'Format gambar harus jpg, jpeg, png, webp, atau gif.',
            'lampiran.*.max' => 'Ukuran gambar maksimal 8 MB sebelum dikompresi.',
        ];
    }
}
