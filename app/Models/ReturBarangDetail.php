<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturBarangDetail extends Model
{
    protected $table = 'retur_barang_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_retur',
        'id_produk',
        'jumlah_retur'
    ];

    public function retur() {
        return $this->belongsTo(ReturBarang::class, 'id_retur');
    }

    public function produk() {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
