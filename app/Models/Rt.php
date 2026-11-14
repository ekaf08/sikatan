<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rt extends Model
{
    use SoftDeletes;

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
