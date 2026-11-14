<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogAudit extends Model
{
    use SoftDeletes;

    protected $table = 'log_audit';

    public $timestamps = false;

    protected $fillable = [
        'pengguna_id',
        'nama_tabel',
        'id_data',
        'aksi',
        'nilai_lama',
        'nilai_baru',
        'waktu',
    ];

    protected function casts(): array
    {
        return [
            'nilai_lama' => 'array',
            'nilai_baru' => 'array',
            'waktu' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
