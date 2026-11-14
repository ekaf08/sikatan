<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosDana;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PosDanaController extends Controller
{
    private const JENIS = [
        'kematian' => 'Kas Kematian',
        'masjid' => 'Kas Masjid',
        'sedekah_bumi' => 'Kas Sedekah Bumi',
        'umum' => 'Umum / Lain-lain',
    ];

    public function index(): View
    {
        return view('pages.master.pos-dana', array_merge(
            ['daftarJenis' => self::JENIS],
            $this->statistikData()
        ));
    }

    public function data(): JsonResponse
    {
        $query = PosDana::query()
            ->select('pos_dana.*')
            ->selectSub(function ($sub) {
                $sub->from('transaksi')
                    ->selectRaw('COALESCE(SUM(nominal), 0)')
                    ->whereColumn('transaksi.pos_dana_id', 'pos_dana.id')
                    ->where('transaksi.jenis', 'masuk')
                    ->where('transaksi.status', 'aktif');
            }, 'total_masuk')
            ->selectSub(function ($sub) {
                $sub->from('transaksi')
                    ->selectRaw('COALESCE(SUM(nominal), 0)')
                    ->whereColumn('transaksi.pos_dana_id', 'pos_dana.id')
                    ->where('transaksi.jenis', 'keluar')
                    ->where('transaksi.status', 'aktif');
            }, 'total_keluar')
            ->orderByDesc('id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('jenis_label', fn (PosDana $pos): string => self::JENIS[$pos->jenis] ?? ucfirst(str_replace('_', ' ', $pos->jenis)))
            ->addColumn('saldo_berjalan', function (PosDana $pos): float {
                return round((float) $pos->saldo_awal + (float) $pos->total_masuk - (float) $pos->total_keluar, 2);
            })
            ->addColumn('aksi', function (PosDana $pos): string {
                $nama = e($pos->nama);
                $jenis = e($pos->jenis);
                $saldo = e((int) $pos->saldo_awal);
                $tanggal = $pos->tanggal_saldo_awal?->format('Y-m-d') ?? '';
                $keterangan = e($pos->keterangan ?? '');

                return '<button type="button" class="btn btn-sm btn-outline-secondary btn-edit-pos"'
                    . ' data-id="' . $pos->id . '" data-nama="' . $nama . '" data-jenis="' . $jenis . '"'
                    . ' data-saldo="' . $saldo . '" data-tanggal="' . $tanggal . '" data-keterangan="' . $keterangan . '" title="Ubah">'
                    . '<i class="bi bi-pencil"></i></button>'
                    . '<button type="button" class="btn btn-sm btn-outline-danger btn-hapus-pos ms-1"'
                    . ' data-id="' . $pos->id . '" data-nama="' . $nama . '" title="Hapus">'
                    . '<i class="bi bi-trash"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }

    public function simpan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'jenis' => ['required', Rule::in(array_keys(self::JENIS))],
            'saldo_awal' => ['required', 'numeric', 'min:0'],
            'tanggal_saldo_awal' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], $this->pesanValidasi());

        $pos = PosDana::create([
            'nama' => $data['nama'],
            'jenis' => $data['jenis'],
            'saldo_awal' => $data['saldo_awal'],
            'tanggal_saldo_awal' => $data['tanggal_saldo_awal'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        return response()->json([
            'message' => "Pos dana {$pos->nama} berhasil ditambahkan.",
        ]);
    }

    public function ubah(Request $request, PosDana $posDana): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'jenis' => ['required', Rule::in(array_keys(self::JENIS))],
            'saldo_awal' => ['required', 'numeric', 'min:0'],
            'tanggal_saldo_awal' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], $this->pesanValidasi());

        $posDana->update([
            'nama' => $data['nama'],
            'jenis' => $data['jenis'],
            'saldo_awal' => $data['saldo_awal'],
            'tanggal_saldo_awal' => $data['tanggal_saldo_awal'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        return response()->json([
            'message' => "Pos dana {$posDana->nama} berhasil diperbarui.",
        ]);
    }

    public function hapus(PosDana $posDana): JsonResponse
    {
        $nama = $posDana->nama;

        try {
            $posDana->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Pos dana {$nama} tidak dapat dihapus karena sudah dipakai pada data transaksi atau mutasi dana.",
            ], 422);
        }

        return response()->json([
            'message' => "Pos dana {$nama} berhasil dihapus.",
        ]);
    }

    public function statistik(): JsonResponse
    {
        $data = $this->statistikData();

        return response()->json([
            'total_pos' => $data['totalPos'],
            'total_saldo_awal' => $data['totalSaldoAwal'],
            'total_saldo_berjalan' => $data['totalSaldoBerjalan'],
            'pos_terakhir_nama' => $data['posTerakhirNama'],
            'pos_terakhir_tanggal' => $data['posTerakhirTanggal'],
        ]);
    }

    private function statistikData(): array
    {
        $totalSaldoAwal = (float) PosDana::sum('saldo_awal');
        $totalMasuk = (float) DB::table('transaksi')
            ->where('status', 'aktif')
            ->where('jenis', 'masuk')
            ->sum('nominal');
        $totalKeluar = (float) DB::table('transaksi')
            ->where('status', 'aktif')
            ->where('jenis', 'keluar')
            ->sum('nominal');

        $terakhir = PosDana::query()->orderByDesc('updated_at')->first();

        $posTerakhirTanggal = null;
        if ($terakhir?->updated_at) {
            $posTerakhirTanggal = $terakhir->updated_at->copy()->locale('id')->translatedFormat('d M Y');
        }

        return [
            'totalPos' => PosDana::count(),
            'totalSaldoAwal' => $totalSaldoAwal,
            'totalSaldoBerjalan' => $totalSaldoAwal + $totalMasuk - $totalKeluar,
            'posTerakhirNama' => $terakhir?->nama,
            'posTerakhirTanggal' => $posTerakhirTanggal,
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'nama.required' => 'Nama pos wajib diisi.',
            'nama.max' => 'Nama pos maksimal 255 karakter.',
            'jenis.required' => 'Jenis pos wajib dipilih.',
            'jenis.in' => 'Jenis pos tidak valid.',
            'saldo_awal.required' => 'Saldo awal wajib diisi.',
            'saldo_awal.numeric' => 'Saldo awal harus berupa angka.',
            'saldo_awal.min' => 'Saldo awal tidak boleh negatif.',
            'tanggal_saldo_awal.date' => 'Tanggal saldo awal tidak valid.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.',
        ];
    }
}
