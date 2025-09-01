<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Menampilkan formulir untuk membuat supplier baru.
     * Ini adalah metode yang akan menampilkan tampilan homepage/supplier/create.blade.php.
     */
    public function create(): View
    {
        // Mengarahkan ke view yang benar sesuai dengan jalur file: homepage/supplier/create.blade.php
        return view('homepage.supplier.create');
    }

    /**
     * Menyimpan data supplier baru ke database.
     * Metode ini akan dipanggil saat formulir disubmit (POST).
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk dari form
        $validatedData = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'nama_kontak' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:255',
        ]);

        // Membuat instance model Supplier baru dan mengisinya dengan data yang divalidasi
        $supplier = new Supplier();
        $supplier->nama_supplier = $validatedData['nama_supplier'];
        $supplier->nama_kontak = $validatedData['nama_kontak'];
        $supplier->telepon = $validatedData['telepon'];
        $supplier->alamat = $validatedData['alamat'];
        $supplier->email = $validatedData['email'];
        $supplier->save();

        // Redirect pengguna kembali ke halaman daftar supplier dengan pesan sukses
        return Redirect::route('suppliers.index')->with('success', 'Data supplier berhasil disimpan.');
    }

    // Metode lain seperti index, show, edit, update, dan destroy dapat ditambahkan di sini.
}
