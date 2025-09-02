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
    public function index()
    {
        $suppliers = Supplier::all();
        return view('homepage.supplier.index', compact('suppliers'));
    }

    public function create() {
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
            'nama' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        // Membuat instance model Supplier baru dan mengisinya dengan data yang divalidasi
        $supplier = new Supplier();
        $supplier->nama = $validatedData['nama'];
        $supplier->kontak = $validatedData['kontak'];
        $supplier->alamat = $validatedData['alamat'];
        $supplier->save();

        // Redirect pengguna kembali ke halaman daftar supplier dengan pesan sukses
        return Redirect::route('suppliers.index')->with('success', 'Data supplier berhasil disimpan.');
    }

    public function destroy($id) {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return Redirect::route('suppliers.index')->with('success', 'Data berhasil dihapus.');
    }

    // Metode lain seperti index, show, edit, update, dan destroy dapat ditambahkan di sini.
}
