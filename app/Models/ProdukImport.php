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
            'stok'          => 0,   
            'harga'         => $row['harga']
        ]);
    }

    public function rules() {
        return [
            '*.nama_produk' => 'required|string|max:100',
            '*.harga'       => 'required|numeric|min:0'
        ];
    }

    public function customValidationMessages() {
        return [
            '*.nama_produk.required' => 'Nama produk wajib diisi',
            '*.harga.required' => 'Harga wajib diisi',
            '*.harga.numeric' => 'Harga harus berupa angka',
        ];
    }
}
