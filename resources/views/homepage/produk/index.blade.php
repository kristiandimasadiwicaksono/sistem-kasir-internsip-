@extends('layouts.main')

@section('title', 'Data Produk')

@section('content')
<main class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden">

            <!-- Header Section -->
            <div class="px-6 py-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    
                    <!-- Left: Title + Search -->
                    <div class="flex-1">
                        <h2 class="text-2xl font-extrabold text-gray-900">📦 Data Produk</h2>
                        <p class="mt-1 text-sm text-gray-600">Kelola daftar produk dengan mudah dan cepat.</p>

                        <!-- 🔍 Search + Sort -->
                        <form method="GET" action="{{ route('produk.index') }}" 
                              class="flex flex-wrap items-center gap-2 mt-4">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari produk..."
                                class="px-4 py-2 text-sm border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">

                            <select name="sort" onchange="this.form.submit()"
                                class="px-4 py-2 text-sm border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                                <option value="">Urutkan</option>
                                <option value="asc" {{ request('sort')=='asc' ? 'selected' : '' }}>A → Z</option>
                                <option value="desc" {{ request('sort')=='desc' ? 'selected' : '' }}>Z → A</option>
                            </select>

                            <button type="submit"
                                class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                Cari
                            </button>
                        </form>
                    </div>

                    <!-- Right: Buttons -->
                    @if(Auth::user()->role === 'admin')
                    <div class="flex items-center gap-3">
                        <a href="{{ route('produk.import') }}"
                           class="py-2.5 px-4 text-sm rounded-lg bg-green-600 text-white shadow hover:bg-green-700 transition">
                            Import Data
                        </a>
                        <a href="{{ route('produk.create') }}"
                           class="py-2.5 px-4 inline-flex items-center gap-2 text-sm font-semibold rounded-lg bg-blue-600 text-white shadow hover:bg-blue-700 transition">
                            <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5v14"/>
                            </svg>
                            Tambah Data
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nama Produk</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Stok</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Harga</th>
                            @if(Auth::user()->role === 'admin')
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($produk as $item)
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 text-center">
                                {{ $loop->iteration + ($produk->currentPage() - 1) * $produk->perPage() }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $item->nama_produk }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-center">{{ $item->stok }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-center">
                                {{ 'Rp ' . number_format($item->harga, 0, ',', '.') }}
                            </td>
                            @if(Auth::user()->role === 'admin')
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('produk.edit', $item->id) }}"
                                       class="px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('produk.destroy', $item->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 text-sm font-medium text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-lg">Belum ada produk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 bg-gray-50">
                <div>
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $produk->firstItem() }}</span> 
                        sampai <span class="font-semibold">{{ $produk->lastItem() }}</span> 
                        dari <span class="font-semibold">{{ $produk->total() }}</span> data
                    </p>
                </div>

                <!-- Pagination -->
                @if ($produk->hasPages())
                <nav class="flex items-center gap-1">
                    <a href="{{ $produk->previousPageUrl() }}" 
                       class="px-3 py-2 text-sm border rounded-l-md {{ $produk->onFirstPage() ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-blue-50' }}">
                        ‹
                    </a>
                    @foreach ($produk->getUrlRange(1, $produk->lastPage()) as $page => $url)
                        <a href="{{ $url }}" 
                           class="px-3 py-2 text-sm border {{ $page == $produk->currentPage() ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 hover:bg-blue-50' }}">
                            {{ $page }}
                        </a>
                    @endforeach
                    <a href="{{ $produk->nextPageUrl() }}" 
                       class="px-3 py-2 text-sm border rounded-r-md {{ $produk->onLastPage() ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-blue-50' }}">
                        ›
                    </a>
                </nav>
                @endif
            </div>
            <!-- End Footer -->

        </div>
    </div>
</main>
@endsection
