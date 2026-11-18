<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MenuLayanan
{
    /**
     * Ambil item menu area tertentu (akar/urut) beserta anak, difilter peran & status aktif.
     * Fallback aman: jika tabel belum ada/kosong/error, kembalikan koleksi kosong (view bisa fallback hardcode).
     *
     * @return Collection<int, Menu>
     */
    public function ambil(string $area, ?object $pengguna = null): Collection
    {
        try {
            if (!Schema::hasTable('menu')) {
                return collect();
            }

            $items = Menu::query()
                ->area($area)
                ->aktif()
                ->akar()
                ->with(['anak' => fn ($q) => $q->where('aktif', true)->orderBy('urutan')])
                ->orderBy('urutan')
                ->get();

            return $items
                ->filter(fn (Menu $m) => $this->bolehTampil($m, $pengguna))
                ->map(function (Menu $m) use ($pengguna) {
                    $m->setRelation('anak', $m->anak->filter(fn (Menu $a) => $this->bolehTampil($a, $pengguna))->values());
                    return $m;
                })
                ->values();
        } catch (Throwable $e) {
            return collect();
        }
    }

    public function bolehTampil(Menu $menu, ?object $pengguna): bool
    {
        $peran = $menu->peran ?: 'semua';
        $peran = trim($peran);

        if ($peran === '' || $peran === 'semua') {
            return true;
        }

        $daftar = array_map('trim', explode(',', $peran));

        if (in_array('tamu', $daftar, true) && !$pengguna) {
            return true;
        }
        if (in_array('auth', $daftar, true) && $pengguna) {
            return true;
        }
        if ($pengguna && isset($pengguna->role) && in_array($pengguna->role, $daftar, true)) {
            return true;
        }

        // jika hanya butuh tamu tapi user login, jangan tampilkan
        if ($daftar === ['tamu']) {
            return !$pengguna;
        }

        return false;
    }

    public function href(Menu $menu): string
    {
        if ($menu->tipe === 'route' && $menu->route_name) {
            try {
                return route($menu->route_name, $menu->route_params ?? []);
            } catch (Throwable $e) {
                return $menu->url ?: '#';
            }
        }

        return $menu->url ?: '#';
    }

    // public function aktif(Menu $menu): bool
    // {
    //     if ($menu->tipe === 'route' && $menu->route_name) {
    //         try {
    //             $patterns = [$menu->route_name];
    //             // kelompok route master-*
    //             if (str_starts_with($menu->route_name, 'master-')) {
    //                 $patterns[] = 'master-*';
    //             }
    //             return request()->routeIs($patterns);
    //         } catch (Throwable $e) {
    //             return false;
    //         }
    //     }
    //     if ($menu->tipe === 'tautan' && $menu->url) {
    //         // anchor pada halaman yang sama - tandai aktif hanya jika persis path publik utama
    //         $url = $menu->url;
    //         if (str_starts_with($url, '#')) {
    //             return request()->routeIs('publik') && $url === '#'; // none by default
    //         }
    //     }
    //     return false;
    // }

    // GPT
    public function aktif(Menu $menu): bool
    {
        if ($menu->tipe === 'route' && $menu->route_name) {
            try {
                return request()->routeIs($menu->route_name);
            } catch (Throwable $e) {
                return false;
            }
        }

        if ($menu->tipe === 'tautan' && $menu->url) {
            // Anchor pada halaman yang sama
            $url = $menu->url;

            if (str_starts_with($url, '#')) {
                return request()->routeIs('publik')
                    && $url === '#';
            }
        }

        return false;
    }
}
