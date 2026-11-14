<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosDana extends Model
{
    use SoftDeletes;

    protected $table = 'pos_dana';

    protected $fillable = [
        'nama',
        'jenis',
        'keterangan',
        'saldo_awal',
        'tanggal_saldo_awal',
    ];

    protected function casts(): array
    {
        return [
            'saldo_awal' => 'decimal:2',
            'tanggal_saldo_awal' => 'date',
        ];
    }
}
