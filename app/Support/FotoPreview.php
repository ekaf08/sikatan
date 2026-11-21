<?php

namespace App\Support;

use App\Models\Lampiran;
use Illuminate\Support\Collection;

class FotoPreview
{
    public static function url(string $lokasiBerkas): string
    {
        $basis = rtrim(request()->getBaseUrl(), '/');

        return $basis.'/storage/'.ltrim($lokasiBerkas, '/');
    }

    public static function render(Collection $lampiran, int $limit = 3): string
    {
        if ($lampiran->isEmpty()) {
            return '<span class="hint">&mdash;</span>';
        }

        $daftar = $lampiran->map(fn (Lampiran $item): array => [
            'url' => self::url($item->lokasi_berkas),
            'nama' => basename($item->lokasi_berkas),
        ])->values();

        $json = json_encode($daftar->all(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $json = $json === false ? '[]' : $json;

        $html = '<div class="d-flex flex-wrap gap-1 foto-preview-group">';

        foreach ($daftar->take($limit) as $indeks => $foto) {
            $url = e($foto['url']);
            $nama = e($foto['nama']);

            $html .= '<button type="button" class="foto-preview-trigger p-0 border-0 bg-transparent"'
                .' data-foto-preview'
                .' data-bs-toggle="modal"'
                .' data-bs-target="#modalFotoPreview"'
                .' data-foto-daftar="'.e($json).'"'
                .' data-foto-indeks="'.$indeks.'"'
                .' data-foto-nama="'.$nama.'"'
                .' title="Lihat foto">'
                .'<img src="'.$url.'" alt="Foto lampiran" loading="lazy"'
                .' style="width:40px;height:40px;object-fit:cover;border-radius:.375rem;border:1px solid rgba(0,0,0,.15)">'
                .'</button>';
        }

        if ($daftar->count() > $limit) {
            $html .= '<span class="badge-soft badge-neutral">+'.($daftar->count() - $limit).'</span>';
        }

        return $html.'</div>';
    }
}
