<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosDana extends Model
{
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
