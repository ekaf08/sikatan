<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'order_id',
        'provider',
        'channel',
        'nominal',
        'status',
        'transaksi_id',
        'nama_donatur',
        'donatur_anonim',
        'tujuan',
        'catatan_publik',
        'email',
        'no_hp',
        'snap_token',
        'redirect_url',
        'payload_request',
        'payload_notifikasi',
        'meta',
        'dibayar_pada',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'donatur_anonim' => 'boolean',
            'payload_request' => 'array',
            'payload_notifikasi' => 'array',
            'meta' => 'array',
            'dibayar_pada' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
