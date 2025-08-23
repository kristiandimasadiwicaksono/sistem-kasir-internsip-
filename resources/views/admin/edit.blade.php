@extends('layouts.main')

@section('content')
<div class="mt-8 max-w-md mx-auto bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Admin</h2>

    <form action="{{ route('admin.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                   class="w-full border px-3 py-2 rounded" required>
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium">Password Baru (opsional)</label>
            <input type="password" name="password" class="w-full border px-3 py-2 rounded">
            @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded">
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
