<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Acara;
use App\Models\AnggaranAcara;
use App\Models\PosDana;
use App\Models\Transaksi;
use App\Services\SaldoDana;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcaraController extends Controller
{
    public function __construct(private readonly SaldoDana $saldoDana) {}

    public function index(Request $request): View
    {
        $daftarAcara = Acara::query()
            ->withCount('anggaran')
            ->orderByDesc('tahun')
            ->orderByDesc('id')
            ->get();

        $acara = null;

        if ($daftarAcara->isNotEmpty()) {
            $acara = $daftarAcara->firstWhere('id', $request->integer('acara'))
                ?? $daftarAcara->first();
        }

        return view('pages.acara', array_merge([
            'daftarAcara' => $daftarAcara,
            'acara' => $acara,
            'statusAcara' => [
                'rencana' => 'Rencana',
                'berjalan' => 'Berjalan',
                'selesai' => 'Selesai',
            ],
        ], $this->ringkasan($acara)));
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $this->validasiAcara($request);

        $acara = Acara::create($data);

        return $this->kembali($acara, "Acara {$acara->nama} berhasil ditambahkan.");
    }

    public function ubah(Request $request, Acara $acara): RedirectResponse
    {
        $data = $this->validasiAcara($request);

        $acara->update($data);

        return $this->kembali($acara, "Acara {$acara->nama} berhasil diperbarui.");
    }

    public function hapus(Acara $acara): RedirectResponse
    {
        $dipakai = Transaksi::query()->where('acara_id', $acara->id)->exists();

        if ($dipakai) {
            return redirect()->route('acara', ['acara' => $acara->id])
                ->with('galat', "Acara {$acara->nama} tidak dapat dihapus karena sudah dipakai pada transaksi.");
        }

        DB::transaction(function () use ($acara) {
            $acara->anggaran()->delete();
            $acara->delete();
        });

        return redirect()->route('acara')->with('sukses', "Acara {$acara->nama} berhasil dihapus.");
    }

    public function anggaranSimpan(Request $request, Acara $acara): RedirectResponse
    {
        $data = $this->validasiAnggaran($request);

        $acara->anggaran()->create($data);

        return $this->kembali($acara, 'Item anggaran berhasil ditambahkan.');
    }

    public function anggaranUbah(Request $request, Acara $acara, AnggaranAcara $anggaran): RedirectResponse
    {
        $this->pastikanMilikAcara($acara, $anggaran);

        $data = $this->validasiAnggaran($request);

        $anggaran->update($data);

        return $this->kembali($acara, 'Item anggaran berhasil diperbarui.');
    }

    public function anggaranHapus(Acara $acara, AnggaranAcara $anggaran): RedirectResponse
    {
        $this->pastikanMilikAcara($acara, $anggaran);

        $dipakai = Transaksi::query()->where('anggaran_acara_id', $anggaran->id)->exists();

        if ($dipakai) {
            return $this->kembali($acara, 'Item anggaran tidak dapat dihapus karena sudah dipakai pada transaksi.', 'galat');
        }

        $anggaran->delete();

        return $this->kembali($acara, 'Item anggaran berhasil dihapus.');
    }

    private function ringkasan(?Acara $acara): array
    {
        $posSedekah = PosDana::query()->where('jenis', 'sedekah_bumi')->first();

        $data = [
            'anggaran' => collect(),
            'totalAnggaran' => 0.0,
            'totalRealisasi' => 0.0,
            'sisaAnggaran' => 0.0,
            'persenRealisasi' => 0.0,
            'saldoSedekahBumi' => 0.0,
            'pengeluaranAcara' => collect(),
            'pemasukanRw' => collect(),
            'totalPemasukanSedekahBumi' => 0.0,
        ];

        if (! $acara) {
            return $data;
        }

        $anggaran = $acara->anggaran()->orderBy('id')->get();

        $realisasiPerItem = Transaksi::query()
            ->where('acara_id', $acara->id)
            ->where('jenis', 'keluar')
            ->where('status', 'aktif')
            ->whereNotNull('anggaran_acara_id')
            ->selectRaw('anggaran_acara_id, COALESCE(SUM(nominal), 0) as total')
            ->groupBy('anggaran_acara_id')
            ->pluck('total', 'anggaran_acara_id');

        $totalAnggaran = 0.0;

        foreach ($anggaran as $item) {
            $nominal = (float) $item->nominal_anggaran;
            $realisasi = (float) ($realisasiPerItem[$item->id] ?? 0);
            $totalAnggaran += $nominal;

            $item->setAttribute('realisasi', $realisasi);
            $item->setAttribute('sisa_item', $nominal - $realisasi);
            $item->setAttribute('persen_item', $nominal > 0 ? min(100, round($realisasi / $nominal * 100, 1)) : 0);
        }

        $totalRealisasi = (float) Transaksi::query()
            ->where('acara_id', $acara->id)
            ->where('jenis', 'keluar')
            ->where('status', 'aktif')
            ->sum('nominal');

        $pengeluaran = Transaksi::query()
            ->with(['kategori', 'posDana', 'anggaranAcara'])
            ->where('acara_id', $acara->id)
            ->where('jenis', 'keluar')
            ->where('status', 'aktif')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $pemasukanRw = collect();
        $totalPemasukan = 0.0;

        if ($posSedekah) {
            $pemasukan = Transaksi::query()
                ->with('rt.rw')
                ->where('pos_dana_id', $posSedekah->id)
                ->where('jenis', 'masuk')
                ->where('status', 'aktif')
                ->whereYear('tanggal', $acara->tahun)
                ->get();

            $pemasukanRw = $pemasukan
                ->groupBy(fn (Transaksi $transaksi): string => $transaksi->rt?->rw?->nama ?? 'Tanpa RW')
                ->map(function ($items, string $nama): array {
                    return [
                        'nama' => $nama,
                        'jumlah' => (float) $items->sum('nominal'),
                        'jumlah_rt' => $items->pluck('rt_id')->filter()->unique()->count(),
                    ];
                })
                ->sortBy('nama')
                ->values();

            $totalPemasukan = (float) $pemasukan->sum('nominal');
        }

        return [
            'anggaran' => $anggaran,
            'totalAnggaran' => $totalAnggaran,
            'totalRealisasi' => $totalRealisasi,
            'sisaAnggaran' => $totalAnggaran - $totalRealisasi,
            'persenRealisasi' => $totalAnggaran > 0 ? min(100, round($totalRealisasi / $totalAnggaran * 100, 1)) : 0,
            'saldoSedekahBumi' => $posSedekah ? $this->saldoDana->hitung($posSedekah) : 0.0,
            'pengeluaranAcara' => $pengeluaran,
            'pemasukanRw' => $pemasukanRw,
            'totalPemasukanSedekahBumi' => $totalPemasukan,
        ];
    }

    private function validasiAcara(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'status' => ['required', Rule::in(['rencana', 'berjalan', 'selesai'])],
        ], [
            'nama.required' => 'Nama acara wajib diisi.',
            'nama.max' => 'Nama acara maksimal 255 karakter.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'tahun.min' => 'Tahun tidak valid.',
            'tahun.max' => 'Tahun tidak valid.',
            'status.required' => 'Status acara wajib dipilih.',
            'status.in' => 'Status acara tidak valid.',
        ]);
    }

    private function validasiAnggaran(Request $request): array
    {
        $request->merge([
            'nominal_anggaran' => (int) preg_replace('/\D/', '', (string) $request->input('nominal_anggaran')),
        ]);

        return $request->validate([
            'uraian' => ['required', 'string', 'max:255'],
            'nominal_anggaran' => ['required', 'integer', 'min:1'],
        ], [
            'uraian.required' => 'Uraian item wajib diisi.',
            'uraian.max' => 'Uraian item maksimal 255 karakter.',
            'nominal_anggaran.required' => 'Nominal anggaran wajib diisi.',
            'nominal_anggaran.min' => 'Nominal anggaran harus lebih dari nol.',
        ]);
    }

    private function pastikanMilikAcara(Acara $acara, AnggaranAcara $anggaran): void
    {
        abort_unless((int) $anggaran->acara_id === (int) $acara->id, 404);
    }

    private function kembali(Acara $acara, string $pesan, string $kunci = 'sukses'): RedirectResponse
    {
        return redirect()->route('acara', ['acara' => $acara->id])->with($kunci, $pesan);
    }
}
