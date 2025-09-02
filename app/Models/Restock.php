<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restock extends Model
{
    protected $table = 'restock';
    protected $fillable = [
        'id_supplier',
        'tanggal',
        'status_pembayaran',
    ];

    public $timestamps = false;

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function details() {
        return $this->hasMany(RestockDetail::class, 'id_restock');
    }
}
