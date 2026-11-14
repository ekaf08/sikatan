<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutasiDana extends Model
{
    use SoftDeletes;

    protected $table = 'mutasi_dana';

    protected $fillable = [
        'dari_pos_id',
        'ke_pos_id',
        'dibuat_oleh',
        'tanggal',
        'nominal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'nominal' => 'decimal:2',
        ];
    }

    public function dariPos(): BelongsTo
    {
        return $this->belongsTo(PosDana::class, 'dari_pos_id');
    }

    public function kePos(): BelongsTo
    {
        return $this->belongsTo(PosDana::class, 'ke_pos_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
