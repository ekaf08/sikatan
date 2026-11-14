<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi';

    protected $fillable = [
        'pos_dana_id',
        'kategori_id',
        'rt_id',
        'acara_id',
        'dibuat_oleh',
        'jenis',
        'tanggal',
        'nominal',
        'jumlah_kk_bayar',
        'nama_donatur',
        'donatur_anonim',
        'tujuan',
        'catatan_internal',
        'catatan_publik',
        'alasan_selisih',
        'sembunyikan_lampiran',
        'status',
        'alasan_batal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'nominal' => 'decimal:2',
            'jumlah_kk_bayar' => 'integer',
            'donatur_anonim' => 'boolean',
            'sembunyikan_lampiran' => 'boolean',
        ];
    }

    public function posDana(): BelongsTo
    {
        return $this->belongsTo(PosDana::class, 'pos_dana_id')->withTrashed();
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id')->withTrashed();
    }

    public function acara(): BelongsTo
    {
        return $this->belongsTo(Acara::class, 'acara_id')->withTrashed();
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(Lampiran::class, 'transaksi_id');
    }
}
