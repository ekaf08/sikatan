<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAudit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LogAuditController extends Controller
{
    private const LABEL_TABEL = [
        'transaksi' => 'Transaksi',
        'mutasi_dana' => 'Mutasi dana',
        'pengaturan' => 'Pengaturan',
        'acara' => 'Acara',
        'anggaran_acara' => 'Item anggaran',
        'users' => 'Akun pengguna',
    ];

    private const LABEL_FIELD = [
        'tanggal' => 'tanggal',
        'pos' => 'pos dana',
        'kategori' => 'kategori',
        'rt' => 'RT',
        'nominal' => 'nominal',
        'jumlah_kk' => 'jumlah KK',
        'donatur' => 'donatur',
        'tujuan' => 'tujuan',
        'catatan_publik' => 'catatan publik',
        'catatan_internal' => 'catatan internal',
        'status' => 'status',
        'penerima' => 'penerima',
        'acara' => 'acara',
        'item_anggaran' => 'item anggaran',
        'uraian' => 'uraian',
        'nominal_anggaran' => 'nominal anggaran',
        'nama' => 'nama',
        'username' => 'username',
        'email' => 'email',
        'peran' => 'peran',
        'aktif' => 'aktif',
        'kata_sandi' => 'kata sandi',
        'tahun' => 'tahun',
        'dari' => 'pos asal',
        'ke' => 'pos tujuan',
        'keterangan' => 'keterangan',
    ];

    public function index(): View
    {
        return view('pages.log-audit', [
            'daftarPengguna' => User::query()->orderBy('name')->get(['id', 'name']),
            'daftarTabel' => self::LABEL_TABEL,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = LogAudit::query()
            ->with('pengguna')
            ->orderByDesc('waktu')
            ->orderByDesc('id');

        if (in_array($request->query('aksi'), ['tambah', 'ubah', 'batal'], true)) {
            $query->where('aksi', $request->query('aksi'));
        }

        if (($tabel = $request->query('tabel')) && array_key_exists($tabel, self::LABEL_TABEL)) {
            $query->where('nama_tabel', $tabel);
        }

        if (($pengguna = (int) $request->query('pengguna')) > 0) {
            $query->where('pengguna_id', $pengguna);
        }

        if (($dari = $request->query('dari')) !== null && $dari !== '') {
            $query->whereDate('waktu', '>=', $dari);
        }

        if (($sampai = $request->query('sampai')) !== null && $sampai !== '') {
            $query->whereDate('waktu', '<=', $sampai);
        }

        return DataTables::eloquent($query)
            ->addColumn('waktu_format', fn (LogAudit $log): string => $log->waktu
                ? $log->waktu->copy()->locale('id')->translatedFormat('d M Y H.i')
                : '-')
            ->addColumn('pengguna_nama', fn (LogAudit $log): string => e($log->pengguna?->name ?? 'Sistem'))
            ->addColumn('aksi_label', fn (LogAudit $log): string => $this->aksiLabel($log->aksi))
            ->addColumn('data_label', fn (LogAudit $log): string => $this->dataLabel($log))
            ->addColumn('perubahan', fn (LogAudit $log): string => $this->perubahan($log))
            ->filterColumn('waktu_format', function ($query, string $keyword): void {
                $query->whereRaw('CAST(waktu AS TEXT) LIKE ?', ['%'.$keyword.'%']);
            })
            ->filterColumn('pengguna_nama', function ($query, string $keyword): void {
                $kata = '%'.Str::lower($keyword).'%';

                $query->whereHas('pengguna', fn ($sub) => $sub->whereRaw('LOWER(name) LIKE ?', [$kata]));
            })
            ->filterColumn('aksi_label', function ($query, string $keyword): void {
                $kata = Str::lower($keyword);
                $peta = [
                    'tambah' => 'tambah',
                    'ubah' => 'ubah',
                    'batal' => 'batal',
                ];

                foreach ($peta as $nilai => $label) {
                    if (str_contains($kata, $label)) {
                        $query->where('aksi', $nilai);

                        return;
                    }
                }

                $query->whereRaw('1 = 0');
            })
            ->filterColumn('data_label', function ($query, string $keyword): void {
                $kata = '%'.Str::lower($keyword).'%';
                $cocok = [];

                foreach (self::LABEL_TABEL as $tabel => $label) {
                    if (str_contains(Str::lower($label), Str::lower($keyword))) {
                        $cocok[] = $tabel;
                    }
                }

                if ($cocok === []) {
                    $query->whereRaw('1 = 0');

                    return;
                }

                $query->whereIn('nama_tabel', $cocok);
            })
            ->filterColumn('perubahan', function ($query, string $keyword): void {
                $kata = '%'.Str::lower($keyword).'%';

                $query->where(function ($sub) use ($kata): void {
                    $sub->whereRaw('LOWER(CAST(nilai_lama AS TEXT)) LIKE ?', [$kata])
                        ->orWhereRaw('LOWER(CAST(nilai_baru AS TEXT)) LIKE ?', [$kata]);
                });
            })
            ->rawColumns(['aksi_label', 'data_label', 'perubahan'])
            ->toJson();
    }

    private function aksiLabel(string $aksi): string
    {
        return match ($aksi) {
            'tambah' => '<span class="badge-soft badge-in">Tambah</span>',
            'ubah' => '<span class="badge-soft badge-warn">Ubah</span>',
            default => '<span class="badge-soft badge-neutral">Batal</span>',
        };
    }

    private function dataLabel(LogAudit $log): string
    {
        $label = self::LABEL_TABEL[$log->nama_tabel] ?? Str::headline($log->nama_tabel);
        $nilai = $log->nilai_baru ?? $log->nilai_lama ?? [];
        $jenis = $nilai['jenis'] ?? null;

        if ($jenis === 'masuk') {
            $label .= " \u{00B7} masuk";
        } elseif ($jenis === 'keluar') {
            $label .= " \u{00B7} keluar";
        }

        return e($label);
    }

    private function perubahan(LogAudit $log): string
    {
        $lama = $log->nilai_lama ?? [];
        $baru = $log->nilai_baru ?? [];

        if ($log->aksi === 'tambah') {
            return $this->daftarNilai($baru);
        }

        if ($log->aksi === 'batal') {
            $baris = [];

            if (isset($lama['status'])) {
                $baris[] = 'status: <span class="diff-old">'.e($this->tampil($lama['status'])).'</span>'
                    .' &rarr; <span class="diff-new">batal</span>';
            }

            return implode('<br>', array_merge($baris, [$this->daftarNilai($lama, 3)]));
        }

        $baris = [];
        $kunci = array_unique(array_merge(array_keys($lama), array_keys($baru)));

        foreach ($kunci as $field) {
            if ($field === 'jenis') {
                continue;
            }

            $sebelum = $lama[$field] ?? null;
            $sesudah = $baru[$field] ?? null;

            if ($this->normalisasi($sebelum) === $this->normalisasi($sesudah)) {
                continue;
            }

            $baris[] = $this->labelField($field).': '
                .'<span class="diff-old">'.e($this->tampil($sebelum, $field)).'</span>'
                .' &rarr; <span class="diff-new">'.e($this->tampil($sesudah, $field)).'</span>';
        }

        return $baris === []
            ? '<span class="hint">Tidak ada perubahan nilai.</span>'
            : implode('<br>', $baris);
    }

    private function daftarNilai(array $nilai, int $batas = 6): string
    {
        $baris = [];

        foreach ($nilai as $field => $isi) {
            if ($field === 'jenis' || $isi === null || $isi === '') {
                continue;
            }

            $baris[] = $this->labelField($field).': <span class="diff-new">'.e($this->tampil($isi, $field)).'</span>';

            if (count($baris) >= $batas) {
                break;
            }
        }

        return $baris === []
            ? '<span class="hint">&mdash;</span>'
            : implode('<br>', $baris);
    }

    private function labelField(string $field): string
    {
        return self::LABEL_FIELD[$field] ?? Str::headline($field);
    }

    private function tampil(mixed $nilai, ?string $field = null): string
    {
        if ($nilai === null || $nilai === '') {
            return '(kosong)';
        }

        if (is_bool($nilai)) {
            return $nilai ? 'ya' : 'tidak';
        }

        if (is_array($nilai)) {
            return json_encode($nilai, JSON_UNESCAPED_UNICODE) ?: '-';
        }

        if (in_array($field, ['nominal', 'nominal_anggaran'], true) && is_numeric($nilai)) {
            return 'Rp'.number_format((float) $nilai, 0, ',', '.');
        }

        return (string) $nilai;
    }

    private function normalisasi(mixed $nilai): string
    {
        if (is_bool($nilai)) {
            return $nilai ? '1' : '0';
        }

        return (string) ($nilai ?? '');
    }
}
