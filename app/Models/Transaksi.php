<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $fillable = ['id_user', 'zona_id', 'waktu_masuk', 'waktu_keluar', 'status_transaksi'];

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }
}
