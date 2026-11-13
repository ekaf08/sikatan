<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rt extends Model
{
    protected $table = 'rt';

    protected $fillable = [
        'rw_id',
        'nomor',
        'nama_penarik',
        'jumlah_kk',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_kk' => 'integer',
        ];
    }

    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class, 'rw_id');
    }
}
