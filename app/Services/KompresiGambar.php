<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KompresiGambar
{
    private const MAX_DIMENSI = 1600;

    private const KUALITAS_AWAL = 82;

    private const KUALITAS_MINIMAL = 30;

    public function simpan(UploadedFile $berkas, string $folder = 'lampiran', int $maksKb = 500): string
    {
        $biner = @file_get_contents($berkas->getRealPath());
        $sumber = $biner !== false ? @imagecreatefromstring($biner) : false;

        if ($sumber === false) {
            return $berkas->store($folder, 'public');
        }

        [$lebar, $tinggi] = $this->sesuaikanDimensi(imagesx($sumber), imagesy($sumber));

        $kanvas = imagecreatetruecolor($lebar, $tinggi);
        $putih = imagecolorallocate($kanvas, 255, 255, 255);
        imagefilledrectangle($kanvas, 0, 0, $lebar, $tinggi, $putih);
        imagecopyresampled($kanvas, $sumber, 0, 0, 0, 0, $lebar, $tinggi, imagesx($sumber), imagesy($sumber));

        $maksBita = $maksKb * 1024;
        $kualitas = self::KUALITAS_AWAL;
        $isi = '';

        while (true) {
            ob_start();
            imagejpeg($kanvas, null, $kualitas);
            $isi = (string) ob_get_clean();

            if (strlen($isi) <= $maksBita || $kualitas <= self::KUALITAS_MINIMAL) {
                break;
            }

            $kualitas -= 7;
        }

        $lokasi = $folder . '/' . now()->format('Ymd') . '-' . Str::random(20) . '.jpg';
        Storage::disk('public')->put($lokasi, $isi);

        return $lokasi;
    }

    private function sesuaikanDimensi(int $lebar, int $tinggi): array
    {
        $maks = self::MAX_DIMENSI;

        if ($lebar <= $maks && $tinggi <= $maks) {
            return [$lebar, $tinggi];
        }

        $rasio = min($maks / $lebar, $maks / $tinggi);

        return [
            max(1, (int) round($lebar * $rasio)),
            max(1, (int) round($tinggi * $rasio)),
        ];
    }
}
