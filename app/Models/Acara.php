<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acara extends Model
{
    use SoftDeletes;

    protected $table = 'acara';

    protected $fillable = [
        'nama',
        'tahun',
        'status',
    ];

    public function anggaran(): HasMany
    {
        return $this->hasMany(AnggaranAcara::class, 'acara_id');
    }
}
