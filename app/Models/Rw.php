<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rw extends Model
{
    use SoftDeletes;

    protected $table = 'rw';

    protected $fillable = [
        'nama',
        'nama_ketua',
    ];

    public function rt(): HasMany
    {
        return $this->hasMany(Rt::class, 'rw_id');
    }
}
