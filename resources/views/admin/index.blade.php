@extends('layouts.main')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="mt-8 max-w-4xl mx-auto bg-white shadow rounded-xl p-6">
    <h2 class="text-2xl font-bold mb-4">Daftar Pengguna</h2>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2 text-left">Nama</th>
                <th class="border px-4 py-2 text-left">Email</th>
                <th class="border px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td class="border px-4 py-2">{{ $user->name }}</td>
                <td class="border px-4 py-2">{{ $user->email }}</td>
                <td class="border px-4 py-2 text-center">
                    <a href="{{ route('admin.edit', $user->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600">Edit</a>
                    <form action="{{ route('admin.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
