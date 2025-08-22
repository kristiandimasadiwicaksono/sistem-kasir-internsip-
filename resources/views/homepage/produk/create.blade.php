@extends('layouts.main')

@section('title','Tambah Produk')

@section('content')
<div class="max-w-2xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
  <!-- Card -->
  <div class="bg-white rounded-xl shadow-xs p-4 sm:p-7">
    <div class="text-center mb-8">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
        Tambah Data Produk
      </h2>
      <p class="text-sm text-gray-600">
        Harap isi data dengan benar!
      </p>
    </div>

    <form method="POST" action="{{ route('produk.store') }}">
        @csrf
      <!-- Section -->
      <div class="space-y-5">
        
        <!-- Nama Produk -->
        <div>
          <label for="nama_produk" class="block text-sm font-medium text-gray-700">Nama Produk</label>
          <input id="nama_produk" name="nama_produk" type="text" 
            value="{{ old('nama_produk') }}" 
            class="mt-1 block w-full border border-gray-200 shadow-2xs rounded-lg py-2 px-3 sm:text-sm focus:border-blue-500 focus:ring-blue-500" 
            placeholder="Masukkan nama produk" required>
          @error('nama_produk')
            <div class="mt-1 text-xs text-red-500">Nama produk sudah terdaftar</div>
          @enderror
        </div>

        <!-- Stok -->
        <div>
          <label for="stok" class="block text-sm font-medium text-gray-700">Stok</label>
          <input id="stok" name="stok" type="number" min="0"
            value="{{ old('stok') }}" 
            class="mt-1 block w-full border border-gray-200 shadow-2xs rounded-lg py-2 px-3 sm:text-sm focus:border-blue-500 focus:ring-blue-500" 
            placeholder="Masukkan jumlah stok" required>
          @error('stok')
            <div class="mt-1 text-xs text-red-500">Stok tidak valid</div>
          @enderror
        </div>

        <!-- Harga -->
        <div>
          <label for="harga" class="block text-sm font-medium text-gray-700">Harga</label>
          <input id="harga" name="harga" type="number" min="0"
            value="{{ old('harga') }}" 
            class="mt-1 block w-full border border-gray-200 shadow-2xs rounded-lg py-2 px-3 sm:text-sm focus:border-blue-500 focus:ring-blue-500" 
            placeholder="Masukkan harga" required>
          @error('harga')
            <div class="mt-1 text-xs text-red-500">Harga tidak valid</div>
          @enderror
        </div>

      </div>
      <!-- End Section -->

      <!-- Buttons -->
      <div class="mt-6 flex justify-end gap-x-2">
        <a href="{{ route('produk.index') }}" 
          class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50">
          Cancel
        </a>
        <button type="submit" 
          class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700">
          Submit
        </button>
      </div>
      <!-- End Buttons -->
    </form>
  </div>
  <!-- End Card -->
</div>

@endsection