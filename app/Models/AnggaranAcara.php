<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnggaranAcara extends Model
{
    use SoftDeletes;

    protected $table = 'anggaran_acara';

    protected $fillable = [
        'acara_id',
        'uraian',
        'nominal_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'nominal_anggaran' => 'decimal:2',
        ];
    }

    public function acara(): BelongsTo
    {
        return $this->belongsTo(Acara::class, 'acara_id');
    }
}
