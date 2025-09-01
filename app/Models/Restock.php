<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restock extends Model
{
    protected $table = 'restock';
    protected $fillable = [
        'id_supplier',
        'tanggal',
        'keterangan'
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function details() {
        return $this->hasMany(RestockDetail::class, 'id_restock');
    }
}
