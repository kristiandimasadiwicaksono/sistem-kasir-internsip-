<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturBarang extends Model
{
    protected $table = 'retur_barang';
    public $timestamps = false;

    protected $fillable = [
        'id_restock',
        'tanggal_retur',
        'alasan_retur',
        'catatan'
    ];

    public function details() {
        return $this->hasMany(ReturBarangDetail::class, 'id_retur');
    }

    public function restock() {
        return $this->belongsTo(Restock::class, 'id_restock');
    }
}
