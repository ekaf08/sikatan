<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class WilayahController extends Controller
{
    public function index(): View
    {
        return view('pages.master.wilayah', [
            'daftarRw' => Rw::query()->select('id', 'nama')->orderBy('nama')->get(),
            'totalRw' => Rw::count(),
            'totalRt' => Rt::count(),
            'totalKk' => (int) Rt::sum('jumlah_kk'),
        ]);
    }

    public function dataRw(): JsonResponse
    {
        return DataTables::eloquent(Rw::query()->withCount('rt')->orderByDesc('id'))
            ->addIndexColumn()
            ->addColumn('aksi', function (Rw $rw): string {
                $nama = e($rw->nama);
                $ketua = e($rw->nama_ketua ?? '');

                return '<button type="button" class="btn btn-sm btn-outline-secondary btn-edit-rw"'
                    .' data-id="'.$rw->id.'" data-nama="'.$nama.'" data-ketua="'.$ketua.'" title="Ubah">'
                    .'<i class="bi bi-pencil"></i></button>'
                    .'<button type="button" class="btn btn-sm btn-outline-danger btn-hapus-rw ms-1"'
                    .' data-id="'.$rw->id.'" data-nama="'.$nama.'" title="Hapus">'
                    .'<i class="bi bi-trash"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }

    public function simpanRw(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', Rule::unique('rw', 'nama')],
            'nama_ketua' => ['nullable', 'string', 'max:255'],
        ], $this->pesanValidasiRw());

        $rw = Rw::create($data);

        return response()->json([
            'message' => "Wilayah {$rw->nama} berhasil ditambahkan.",
        ]);
    }

    public function ubahRw(Request $request, Rw $rw): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', Rule::unique('rw', 'nama')->ignore($rw->id)],
            'nama_ketua' => ['nullable', 'string', 'max:255'],
        ], $this->pesanValidasiRw());

        $rw->update($data);

        return response()->json([
            'message' => "Wilayah {$rw->nama} berhasil diperbarui.",
        ]);
    }

    public function hapusRw(Rw $rw): JsonResponse
    {
        if ($rw->rt()->exists()) {
            return response()->json([
                'message' => "Wilayah {$rw->nama} masih memiliki data RT. Hapus RT terlebih dahulu.",
            ], 422);
        }

        $nama = $rw->nama;
        $rw->delete();

        return response()->json([
            'message' => "Wilayah {$nama} berhasil dihapus.",
        ]);
    }

    public function opsiRw(): JsonResponse
    {
        return response()->json(
            Rw::query()->select('id', 'nama')->orderBy('nama')->get()
        );
    }

    public function dataRt(): JsonResponse
    {
        $query = Rt::query()->with('rw')->orderByDesc('id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('rw_nama', fn (Rt $rt): string => $rt->rw?->nama ?? '-')
            ->addColumn('aksi', function (Rt $rt): string {
                $nomor = e($rt->nomor);
                $penarik = e($rt->nama_penarik ?? '');

                return '<button type="button" class="btn btn-sm btn-outline-secondary btn-edit-rt"'
                    .' data-id="'.$rt->id.'" data-rw-id="'.$rt->rw_id.'" data-nomor="'.$nomor.'"'
                    .' data-penarik="'.$penarik.'" data-kk="'.$rt->jumlah_kk.'" title="Ubah">'
                    .'<i class="bi bi-pencil"></i></button>'
                    .'<button type="button" class="btn btn-sm btn-outline-danger btn-hapus-rt ms-1"'
                    .' data-id="'.$rt->id.'" data-nama="RT '.$nomor.'" title="Hapus">'
                    .'<i class="bi bi-trash"></i></button>';
            })
            ->filterColumn('rw_nama', function ($query, string $keyword): void {
                $query->whereHas('rw', function ($q) use ($keyword): void {
                    $q->where('nama', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('rw_nama', function ($query, string $order): void {
                $query->orderBy(
                    Rw::query()->select('nama')->whereColumn('rw.id', 'rt.rw_id'),
                    $order
                );
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }

    public function simpanRt(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rw_id' => ['required', 'integer', Rule::exists('rw', 'id')],
            'nomor' => [
                'required', 'string', 'max:10',
                Rule::unique('rt', 'nomor')->where(
                    fn ($query) => $query->where('rw_id', $request->input('rw_id'))
                ),
            ],
            'nama_penarik' => ['nullable', 'string', 'max:255'],
            'jumlah_kk' => ['required', 'integer', 'min:0'],
        ], $this->pesanValidasiRt());

        $rt = Rt::create([
            'rw_id' => $data['rw_id'],
            'nomor' => $data['nomor'],
            'nama_penarik' => $data['nama_penarik'] ?? null,
            'jumlah_kk' => $data['jumlah_kk'],
        ]);

        return response()->json([
            'message' => "RT {$rt->nomor} berhasil ditambahkan.",
        ]);
    }

    public function ubahRt(Request $request, Rt $rt): JsonResponse
    {
        $data = $request->validate([
            'rw_id' => ['required', 'integer', Rule::exists('rw', 'id')],
            'nomor' => [
                'required', 'string', 'max:10',
                Rule::unique('rt', 'nomor')
                    ->where(fn ($query) => $query->where('rw_id', $request->input('rw_id')))
                    ->ignore($rt->id),
            ],
            'nama_penarik' => ['nullable', 'string', 'max:255'],
            'jumlah_kk' => ['required', 'integer', 'min:0'],
        ], $this->pesanValidasiRt());

        $rt->update([
            'rw_id' => $data['rw_id'],
            'nomor' => $data['nomor'],
            'nama_penarik' => $data['nama_penarik'] ?? null,
            'jumlah_kk' => $data['jumlah_kk'],
        ]);

        return response()->json([
            'message' => "RT {$rt->nomor} berhasil diperbarui.",
        ]);
    }

    public function hapusRt(Rt $rt): JsonResponse
    {
        $nomor = $rt->nomor;

        try {
            $rt->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => "RT {$nomor} tidak dapat dihapus karena sudah dipakai pada data transaksi.",
            ], 422);
        }

        return response()->json([
            'message' => "RT {$nomor} berhasil dihapus.",
        ]);
    }

    public function statistik(): JsonResponse
    {
        return response()->json([
            'total_rw' => Rw::count(),
            'total_rt' => Rt::count(),
            'total_kk' => (int) Rt::sum('jumlah_kk'),
        ]);
    }

    private function pesanValidasiRw(): array
    {
        return [
            'nama.required' => 'Nama RW wajib diisi.',
            'nama.max' => 'Nama RW maksimal 255 karakter.',
            'nama.unique' => 'Nama RW sudah terdaftar.',
            'nama_ketua.max' => 'Nama ketua maksimal 255 karakter.',
        ];
    }

    private function pesanValidasiRt(): array
    {
        return [
            'rw_id.required' => 'RW wajib dipilih.',
            'rw_id.integer' => 'RW tidak valid.',
            'rw_id.exists' => 'RW tidak ditemukan.',
            'nomor.required' => 'Nomor RT wajib diisi.',
            'nomor.max' => 'Nomor RT maksimal 10 karakter.',
            'nomor.unique' => 'Nomor RT sudah ada pada RW tersebut.',
            'nama_penarik.max' => 'Nama penarik maksimal 255 karakter.',
            'jumlah_kk.required' => 'Jumlah KK wajib diisi.',
            'jumlah_kk.integer' => 'Jumlah KK harus berupa angka bulat.',
            'jumlah_kk.min' => 'Jumlah KK tidak boleh negatif.',
        ];
    }
}
