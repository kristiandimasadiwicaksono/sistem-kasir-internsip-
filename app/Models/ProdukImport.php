<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new produk([
            'nama_produk'   => $row['nama_produk'],
            'harga'         => $row['harga']
        ]);
    }
}
