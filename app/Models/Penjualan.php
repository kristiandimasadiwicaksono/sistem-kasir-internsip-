<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $fillable = [
        'tanggal',
        'total'
    ];
    public $timestamps = false;

    public function details() {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id');
    }
}
