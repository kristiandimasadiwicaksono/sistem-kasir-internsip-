@extends('layouts.main')

@section('title', 'Edit Supplier')

@section('content')
<div class="container mx-auto mt-10 px-4 md:px-8">
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Supplier</h1>

        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Supplier</label>
                <input type="text" name="nama" id="nama"
                       value="{{ old('nama', $supplier->nama) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                @error('nama')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="kontak" class="block text-sm font-medium text-gray-700">Kontak</label>
                <input type="text" name="kontak" id="kontak"
                       value="{{ old('kontak', $supplier->kontak) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-lg p-2">
                @error('kontak')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-lg p-2">{{ old('alamat', $supplier->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
