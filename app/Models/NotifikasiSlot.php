<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifikasiSlot extends Model
{
    protected $table = 'notifikasi_slot';

    protected $fillable = [
        'user_id',
        'zona_id',
        'status',
        'terkirim_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
