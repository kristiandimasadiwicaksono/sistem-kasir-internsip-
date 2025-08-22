<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPenjualan extends Model
{
    protected $table = 'history_penjualan';
    protected $fillable = [
        'id_penjualan',
        'id_produk',
        'jumlah',
        'status',
        'tanggal',
        'tanggal_batal',
    ];
    public $timestamps = false;

    public function produk(){
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function penjualan() {
         return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id');
    }
}
