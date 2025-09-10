<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\ProdukImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProdukController extends Controller
{
    public function index(Request $request) {
        $query = Produk::query();

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        if ($request->sort === 'asc') {
            $query->orderBy('nama_produk', 'asc');
        } elseif ($request->sort === 'desc') {
            $query->orderBy('nama_produk','desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $produk = $query->paginate(5)->withQueryString();

        return view('homepage.produk.index', compact('produk'));
    }

    public function create() {
        return view('homepage.produk.create');
    }

    public function store(Request $request) {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'harga' => 'required|numeric',
        ]);

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'harga' => $request->harga,
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
        ]);

        $produk = Produk::findOrFail($id);

        $produk->nama_produk = $request->nama_produk;
        $produk->harga = $request->harga;
        $produk->save();

        return redirect()->route('produk.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id) {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Data berhasil dihapus');
    }

    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        Excel::import(new ProdukImport, $request->file('file'));

        return redirect()->route('produk.index')
                         ->with('success', 'Data produk berhasil di import!');
    }
}
