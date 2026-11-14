<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    private const JENIS = [
        'masuk' => 'Masuk',
        'keluar' => 'Keluar',
    ];

    public function index(): View
    {
        return view('pages.master.kategori', $this->statistikData());
    }

    public function data(): JsonResponse
    {
        $query = Kategori::query()->orderByDesc('id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('jenis_label', fn (Kategori $kategori): string => self::JENIS[$kategori->jenis] ?? ucfirst($kategori->jenis))
            ->addColumn('nominal_format', fn (Kategori $kategori): ?string => $kategori->nominal_standar !== null
                ? number_format((float) $kategori->nominal_standar, 0, ',', '.')
                : null)
            ->addColumn('aksi', function (Kategori $kategori): string {
                $nama = e($kategori->nama);
                $jenis = e($kategori->jenis);
                $nominal = $kategori->nominal_standar !== null ? e((int) $kategori->nominal_standar) : '';
                $sensitif = $kategori->sensitif ? '1' : '0';

                return '<button type="button" class="btn btn-sm btn-outline-secondary btn-edit-kategori"'
                    . ' data-id="' . $kategori->id . '" data-nama="' . $nama . '" data-jenis="' . $jenis . '"'
                    . ' data-nominal="' . $nominal . '" data-sensitif="' . $sensitif . '" title="Ubah">'
                    . '<i class="bi bi-pencil"></i></button>'
                    . '<button type="button" class="btn btn-sm btn-outline-danger btn-hapus-kategori ms-1"'
                    . ' data-id="' . $kategori->id . '" data-nama="' . $nama . '" title="Hapus">'
                    . '<i class="bi bi-trash"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }

    public function simpan(Request $request): JsonResponse
    {
        $data = $request->validate($this->aturanValidasi(), $this->pesanValidasi());

        $kategori = Kategori::create([
            'nama' => $data['nama'],
            'jenis' => $data['jenis'],
            'nominal_standar' => $data['nominal_standar'] ?? null,
            'sensitif' => $request->boolean('sensitif'),
        ]);

        return response()->json([
            'message' => "Kategori {$kategori->nama} berhasil ditambahkan.",
        ]);
    }

    public function ubah(Request $request, Kategori $kategori): JsonResponse
    {
        $data = $request->validate($this->aturanValidasi($kategori), $this->pesanValidasi());

        $kategori->update([
            'nama' => $data['nama'],
            'jenis' => $data['jenis'],
            'nominal_standar' => $data['nominal_standar'] ?? null,
            'sensitif' => $request->boolean('sensitif'),
        ]);

        return response()->json([
            'message' => "Kategori {$kategori->nama} berhasil diperbarui.",
        ]);
    }

    public function hapus(Kategori $kategori): JsonResponse
    {
        $nama = $kategori->nama;

        try {
            $kategori->delete();
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Kategori {$nama} tidak dapat dihapus karena sudah dipakai pada data transaksi.",
            ], 422);
        }

        return response()->json([
            'message' => "Kategori {$nama} berhasil dihapus.",
        ]);
    }

    public function statistik(): JsonResponse
    {
        $data = $this->statistikData();

        return response()->json([
            'total_kategori' => $data['totalKategori'],
            'total_masuk' => $data['totalMasuk'],
            'total_keluar' => $data['totalKeluar'],
            'total_sensitif' => $data['totalSensitif'],
        ]);
    }

    private function statistikData(): array
    {
        return [
            'totalKategori' => Kategori::count(),
            'totalMasuk' => Kategori::where('jenis', 'masuk')->count(),
            'totalKeluar' => Kategori::where('jenis', 'keluar')->count(),
            'totalSensitif' => Kategori::where('sensitif', true)->count(),
        ];
    }

    private function aturanValidasi(?Kategori $kategori = null): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategori', 'nama')
                    ->where(fn ($query) => $query->where('jenis', request('jenis')))
                    ->ignore($kategori?->id),
            ],
            'jenis' => ['required', Rule::in(array_keys(self::JENIS))],
            'nominal_standar' => ['nullable', 'numeric', 'min:0'],
            'sensitif' => ['nullable', 'boolean'],
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.max' => 'Nama kategori maksimal 255 karakter.',
            'nama.unique' => 'Nama kategori sudah dipakai pada jenis yang sama.',
            'jenis.required' => 'Jenis kategori wajib dipilih.',
            'jenis.in' => 'Jenis kategori tidak valid.',
            'nominal_standar.numeric' => 'Nominal standar harus berupa angka.',
            'nominal_standar.min' => 'Nominal standar tidak boleh negatif.',
        ];
    }
}
