<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;
    
    protected $table = 'penjualan';
    
    protected $fillable = [
        'kode_transaksi',
        'tanggal',
        'total',
        'status',
        'snap_token',
        'midtrans_order_id'
    ];
    
    public $timestamps = false;

    protected static function boot() {
        parent::boot();

        static::created(function ($penjualan) {
            $penjualan->kode_transaksi = 'INV-' . str_pad($penjualan->id, 5, '0', STR_PAD_LEFT);
            $penjualan->save();
        });
    }

    public function details() {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id');
    }
}