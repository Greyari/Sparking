<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;
    protected $table = 'slot';
    protected $fillable = [
        'subzona_id',
        'nomor_slot',
        'keterangan',
        'x1','y1','x2','y2','x3','y3','x4','y4'
    ];

    public function subzona()
    {
        return $this->belongsTo(SubZona::class, 'subzona_id');
    }
}
