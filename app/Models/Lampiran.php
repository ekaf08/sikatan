<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lampiran extends Model
{
    use SoftDeletes;

    protected $table = 'lampiran';

    protected $fillable = [
        'transaksi_id',
        'lokasi_berkas',
        'tampil_publik',
    ];

    protected function casts(): array
    {
        return [
            'tampil_publik' => 'boolean',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
