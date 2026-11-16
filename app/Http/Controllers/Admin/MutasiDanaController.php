<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAudit;
use App\Models\MutasiDana;
use App\Models\PosDana;
use App\Services\SaldoDana;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class MutasiDanaController extends Controller
{
    public function __construct(private readonly SaldoDana $saldo) {}

    public function index(): View
    {
        $base = MutasiDana::query();
        $terakhir = (clone $base)->with(['dariPos', 'kePos'])->orderByDesc('tanggal')->orderByDesc('id')->first();

        return view('pages.mutasi-dana', [
            'daftarPos' => $this->saldo->semua(),
            'totalMutasi' => (float) (clone $base)->sum('nominal'),
            'jumlahMutasi' => (clone $base)->count(),
            'mutasiBulanIni' => (float) (clone $base)
                ->whereYear('tanggal', now()->year)
                ->whereMonth('tanggal', now()->month)
                ->sum('nominal'),
            'terakhirMutasi' => $terakhir,
        ]);
    }

    public function data(): JsonResponse
    {
        $query = MutasiDana::query()
            ->with(['dariPos', 'kePos', 'dibuatOleh'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('tanggal_format', fn (MutasiDana $mutasi): string => $mutasi->tanggal
                ? $mutasi->tanggal->copy()->locale('id')->translatedFormat('d M Y')
                : '-')
            ->addColumn('dari_nama', fn (MutasiDana $mutasi): string => $mutasi->dariPos?->nama ?? '-')
            ->addColumn('ke_nama', fn (MutasiDana $mutasi): string => $mutasi->kePos?->nama ?? '-')
            ->addColumn('keterangan', fn (MutasiDana $mutasi): string => $mutasi->keterangan ?: '-')
            ->addColumn('nominal_format', fn (MutasiDana $mutasi): string => number_format((float) $mutasi->nominal, 0, ',', '.'))
            ->addColumn('dibuat_oleh_nama', fn (MutasiDana $mutasi): string => $mutasi->dibuatOleh?->name ?? '-')
            ->filterColumn('dari_nama', function ($query, string $keyword): void {
                $query->whereHas('dariPos', fn ($sub) => $sub->whereRaw('LOWER(nama) LIKE ?', ['%'.mb_strtolower($keyword).'%']));
            })
            ->filterColumn('ke_nama', function ($query, string $keyword): void {
                $query->whereHas('kePos', fn ($sub) => $sub->whereRaw('LOWER(nama) LIKE ?', ['%'.mb_strtolower($keyword).'%']));
            })
            ->filterColumn('dibuat_oleh_nama', function ($query, string $keyword): void {
                $query->whereHas('dibuatOleh', fn ($sub) => $sub->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($keyword).'%']));
            })
            ->addColumn('aksi', function (MutasiDana $mutasi): string {
                return '<button type="button" class="btn btn-sm btn-outline-secondary btn-ubah-mutasi"'
                    .' data-id="'.$mutasi->id.'"'
                    .' data-tanggal="'.($mutasi->tanggal?->format('Y-m-d') ?? '').'"'
                    .' data-dari="'.$mutasi->dari_pos_id.'"'
                    .' data-ke="'.$mutasi->ke_pos_id.'"'
                    .' data-nominal="'.e((int) $mutasi->nominal).'"'
                    .' data-keterangan="'.e($mutasi->keterangan ?? '').'" title="Ubah">'
                    .'<i class="bi bi-pencil"></i></button>'
                    .'<button type="button" class="btn btn-sm btn-outline-danger btn-hapus-mutasi ms-1"'
                    .' data-id="'.$mutasi->id.'"'
                    .' data-info="'.e(($mutasi->dariPos?->nama ?? '-').' → '.($mutasi->kePos?->nama ?? '-').' Rp'.number_format((float) $mutasi->nominal, 0, ',', '.')).'" title="Hapus">'
                    .'<i class="bi bi-trash"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }

    public function simpan(Request $request): JsonResponse
    {
        $data = $this->validasi($request);

        $mutasi = MutasiDana::create([
            'dari_pos_id' => $data['dari_pos_id'],
            'ke_pos_id' => $data['ke_pos_id'],
            'dibuat_oleh' => $request->user()->id,
            'tanggal' => $data['tanggal'],
            'nominal' => $data['nominal'],
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        $this->catatAudit($request, $mutasi, 'tambah');

        return $this->responsBerhasil('Mutasi dana berhasil disimpan.', $mutasi);
    }

    public function ubah(Request $request, MutasiDana $mutasi): JsonResponse
    {
        $data = $this->validasi($request, $mutasi);
        $sebelum = $this->nilaiAudit($mutasi);

        $mutasi->update([
            'dari_pos_id' => $data['dari_pos_id'],
            'ke_pos_id' => $data['ke_pos_id'],
            'tanggal' => $data['tanggal'],
            'nominal' => $data['nominal'],
            'keterangan' => $data['keterangan'] ?? null,
        ]);

        $this->catatAudit($request, $mutasi, 'ubah', $sebelum);

        return $this->responsBerhasil('Mutasi dana berhasil diperbarui.', $mutasi);
    }

    public function hapus(Request $request, MutasiDana $mutasi): JsonResponse
    {
        $nominal = (float) $mutasi->nominal;
        $sebelum = $this->nilaiAudit($mutasi);

        $this->catatAudit($request, $mutasi, 'batal', $sebelum);
        $mutasi->delete();

        return response()->json([
            'message' => 'Mutasi dana sebesar Rp'.number_format($nominal, 0, ',', '.').' berhasil dihapus.',
            'saldo' => $this->daftarSaldo(),
        ]);
    }

    public function saldo(): JsonResponse
    {
        return response()->json(['data' => $this->daftarSaldo()]);
    }

    public function statistik(): JsonResponse
    {
        $base = MutasiDana::query();
        $terakhir = (clone $base)->with(['dariPos', 'kePos'])->orderByDesc('tanggal')->orderByDesc('id')->first();

        return response()->json([
            'total_mutasi' => (float) (clone $base)->sum('nominal'),
            'jumlah_mutasi' => (clone $base)->count(),
            'mutasi_bulan_ini' => (float) (clone $base)
                ->whereYear('tanggal', now()->year)
                ->whereMonth('tanggal', now()->month)
                ->sum('nominal'),
            'terakhir' => $terakhir
                ? ($terakhir->dariPos?->nama ?? '-').' → '.($terakhir->kePos?->nama ?? '-')
                : null,
            'terakhir_tanggal' => $terakhir?->tanggal
                ? $terakhir->tanggal->copy()->locale('id')->translatedFormat('d M Y')
                : null,
        ]);
    }

    private function responsBerhasil(string $pesan, MutasiDana $mutasi): JsonResponse
    {
        return response()->json([
            'message' => $pesan,
            'data' => [
                'id' => $mutasi->id,
                'dari_pos_id' => $mutasi->dari_pos_id,
                'ke_pos_id' => $mutasi->ke_pos_id,
                'tanggal' => $mutasi->tanggal?->toDateString(),
                'nominal' => (float) $mutasi->nominal,
                'keterangan' => $mutasi->keterangan,
            ],
            'saldo' => $this->daftarSaldo(),
        ]);
    }

    private function daftarSaldo(): array
    {
        return $this->saldo->semua()->map(fn (PosDana $pos): array => [
            'id' => $pos->id,
            'nama' => $pos->nama,
            'jenis' => $pos->jenis,
            'saldo' => (float) $pos->saldo_terkini,
        ])->values()->all();
    }

    private function validasi(Request $request, ?MutasiDana $mutasi = null): array
    {
        $validator = Validator::make($request->all(), [
            'dari_pos_id' => ['required', Rule::exists('pos_dana', 'id')],
            'ke_pos_id' => ['required', Rule::exists('pos_dana', 'id'), 'different:dari_pos_id'],
            'tanggal' => ['required', 'date'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], $this->pesanValidasi());

        $validator->after(function ($validator) use ($request, $mutasi): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $posDari = PosDana::query()->find($request->input('dari_pos_id'));

            if (! $posDari) {
                return;
            }

            $tersedia = $this->saldo->hitung($posDari);

            if ($mutasi) {
                if ((int) $mutasi->dari_pos_id === (int) $posDari->id) {
                    $tersedia += (float) $mutasi->nominal;
                }

                if ((int) $mutasi->ke_pos_id === (int) $posDari->id) {
                    $tersedia -= (float) $mutasi->nominal;
                }
            }

            $nominal = (float) $request->input('nominal');

            if ($nominal > $tersedia) {
                $validator->errors()->add(
                    'nominal',
                    'Saldo pos '.$posDari->nama.' tidak mencukupi. Saldo tersedia Rp'.number_format($tersedia, 0, ',', '.').'.'
                );
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private function catatAudit(Request $request, MutasiDana $mutasi, string $aksi, ?array $nilaiLama = null): void
    {
        LogAudit::create([
            'pengguna_id' => $request->user()?->id,
            'nama_tabel' => 'mutasi_dana',
            'id_data' => $mutasi->id,
            'aksi' => $aksi,
            'nilai_lama' => $nilaiLama,
            'nilai_baru' => $aksi === 'batal' ? null : $this->nilaiAudit($mutasi),
            'waktu' => now(),
        ]);
    }

    private function nilaiAudit(MutasiDana $mutasi): array
    {
        $mutasi->load(['dariPos', 'kePos']);

        return [
            'tanggal' => $mutasi->tanggal?->format('Y-m-d'),
            'dari' => $mutasi->dariPos?->nama,
            'ke' => $mutasi->kePos?->nama,
            'nominal' => (float) $mutasi->nominal,
            'keterangan' => $mutasi->keterangan,
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'dari_pos_id.required' => 'Pos asal wajib dipilih.',
            'dari_pos_id.exists' => 'Pos asal tidak valid.',
            'ke_pos_id.required' => 'Pos tujuan wajib dipilih.',
            'ke_pos_id.exists' => 'Pos tujuan tidak valid.',
            'ke_pos_id.different' => 'Pos tujuan harus berbeda dari pos asal.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Tanggal tidak valid.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal harus lebih besar dari nol.',
            'keterangan.max' => 'Keterangan maksimal 255 karakter.',
        ];
    }
}
