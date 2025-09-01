<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $fillable = ['nama', 'kontak', 'alamat'];

    public function restocks() {
        return $this->hasMany(Restock::class, 'id_supplier');
    }
}
