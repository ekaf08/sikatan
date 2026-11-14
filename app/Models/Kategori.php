<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = [
        'jenis',
        'nama',
        'nominal_standar',
        'sensitif',
    ];

    protected function casts(): array
    {
        return [
            'nominal_standar' => 'decimal:2',
            'sensitif' => 'boolean',
        ];
    }
}
