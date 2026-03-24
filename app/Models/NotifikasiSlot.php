<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel notifikasi_slot.
 * Menyimpan data user yang mendaftar notifikasi slot parkir kosong.
 *
 * Status:
 * - 'menunggu' : user sudah daftar, email belum dikirim
 * - 'terkirim' : email sudah dikirim ke user
 */
class NotifikasiSlot extends Model
{
    protected $table = 'notifikasi_slot';

    protected $fillable = [
        'user_id',
        'zona_id',
        'status',
        'terkirim_at'
    ];

    protected $casts = [
        'terkirim_at' => 'datetime',
    ];

    /**
     * User yang mendaftar notifikasi.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Zona parkir yang didaftarkan user untuk notifikasi.
     */
    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }
}
