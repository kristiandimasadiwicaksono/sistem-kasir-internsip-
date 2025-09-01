<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockDetail extends Model
{
    protected $table ='restock_detail';
    protected $fillable = [
        'id_restock',
        'id_produk',
        'jumlah'
    ];

    public function restock() {
        return $this->belongsTo(Restock::class, 'id_restock');
    }
    
    public function produk() {
    return $this->belongsTo(Produk::class, 'id_produk');
    }
}