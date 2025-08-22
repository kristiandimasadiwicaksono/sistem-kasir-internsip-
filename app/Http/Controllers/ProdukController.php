<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index() {
        // Menggunakan paginate() untuk mengambil data dengan paginasi
        // Angka 5 menunjukkan jumlah item per halaman.
        $produk = Produk::paginate(5); 

        return view('homepage.produk.index', compact('produk'));
    }

    public function create() {
        return view('homepage.produk.create');
    }

    public function store(Request $request) {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'harga' => $request->harga,
            'stok' => $request->stok,
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id) {
        $produk = Produk::findorFail($id);

        return view('homepage.produk.edit', compact('produk'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        $produk = Produk::findOrFail($id);

        $produk->nama_produk = $request->nama_produk;
        $produk->harga = $request->harga;
        $produk->stok = $request->stok;
        $produk->save();

        return redirect()->route('produk.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id) {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Data berhasil dihapus');
    }
}
