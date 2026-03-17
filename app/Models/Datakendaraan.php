<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datakendaraan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'datakendaraan';
    protected $primaryKey = 'id_kendaraan';
    protected $fillable = [
        'id_user', 'jenis_kendaraan1', 'no_plat1', 'foto_kendaraan1', 'qr_code1', 'status1'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    
}
