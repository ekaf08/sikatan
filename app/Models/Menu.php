<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'menu';

    protected $fillable = [
        'induk_id',
        'judul',
        'slug',
        'tipe',
        'route_name',
        'url',
        'route_params',
        'ikon',
        'target',
        'area',
        'bagian',
        'urutan',
        'peran',
        'aktif',
        'tampil_di_mobile',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'route_params' => 'array',
            'urutan' => 'integer',
            'aktif' => 'boolean',
            'tampil_di_mobile' => 'boolean',
        ];
    }

    public function induk(): BelongsTo
    {
        return $this->belongsTo(self::class, 'induk_id');
    }

    public function anak(): HasMany
    {
        return $this->hasMany(self::class, 'induk_id')->orderBy('urutan');
    }

    public function scopeArea($query, string $area)
    {
        return $query->where('area', $area);
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeAkar($query)
    {
        return $query->whereNull('induk_id');
    }
}
