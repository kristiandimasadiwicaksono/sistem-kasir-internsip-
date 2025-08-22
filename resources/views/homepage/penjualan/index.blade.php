@extends('layouts.main')

@section('title', 'Data Penjualan')

@section('content')
<!-- Main Content (Sales Table) -->
<main class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">

            <!-- Header Section -->
            <div class="px-6 py-6 border-b border-gray-200 bg-gray-50 md:flex md:justify-between md:items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Data Penjualan</h2>
                    <p class="mt-1 text-sm text-gray-600">Daftar transaksi penjualan.</p>
                </div>
                <div>
                    <a class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                        href="{{ route('penjualan.create') }}">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="M12 5v14"/>
                        </svg>
                        Tambah Data
                    </a>
                </div>
            </div>
            <!-- End Header Section -->

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                ID Penjualan
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Tanggal
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Total
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Produk
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($penjualan as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 text-center">
                                #{{ $item->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 text-center">
                                Rp {{ number_format($item->total ?? ($item->details->sum('subtotal') ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                    @foreach($item->details as $detail)
                                        <li>{{ optional($detail->produk)->nama_produk ?? '—' }} (x{{ $detail->jumlah }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-2">
                                    <!-- Detail -->
                                    <a href="{{ route('penjualan.detail', $item->id) }}"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-100 rounded-full hover:bg-blue-200 transition-colors">
                                        Detail
                                    </a>

                                    <!-- Cetak -->
                                    <a href="{{ route('penjualan.print', $item->id) }}" target="_blank"
                                        class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-600 bg-green-100 rounded-full hover:bg-green-200 transition-colors">
                                        Cetak
                                    </a>

                                    <!-- Hapus -->
                                    <form action="{{ route('penjualan.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 bg-red-100 rounded-full hover:bg-red-200 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-lg">
                                Belum ada penjualan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer Section -->
            <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200">
                <div>
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold text-gray-800">{{ $penjualan->firstItem() }}</span> sampai <span class="font-semibold text-gray-800">{{ $penjualan->lastItem() }}</span> dari <span class="font-semibold text-gray-800">{{ $penjualan->total() }}</span> hasil
                    </p>
                </div>

                <!-- Pagination -->
                @if ($penjualan->hasPages())
                <nav class="flex items-center -space-x-px">
                    {{-- Previous Page Link --}}
                    <a href="{{ $penjualan->previousPageUrl() }}" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @if($penjualan->onFirstPage()) opacity-50 cursor-not-allowed @endif"
                        @if($penjualan->onFirstPage()) disabled @endif>
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 010 1.06L9.56 10l3.23 3.71a.75.75 0 11-1.06 1.06l-3.75-4a.75.75 0 010-1.06l3.75-4a.75.75 0 011.06 0z" clip-rule="evenodd" />
                        </svg>
                    </a>

                    {{-- Pagination Links --}}
                    @foreach ($penjualan->getUrlRange(1, $penjualan->lastPage()) as $page => $url)
                        <a href="{{ $url }}" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @if($page == $penjualan->currentPage()) bg-blue-600 text-white border-blue-600 hover:bg-blue-600 z-10 @else bg-white text-gray-700 @endif">
                            {{ $page }}
                        </a>
                    @endforeach

                    {{-- Next Page Link --}}
                    <a href="{{ $penjualan->nextPageUrl() }}" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @if($penjualan->onLastPage()) opacity-50 cursor-not-allowed @endif"
                        @if($penjualan->onLastPage()) disabled @endif>
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 010-1.06L10.44 10 7.21 6.29a.75.75 0 111.06-1.06l3.75 4a.75.75 0 010 1.06l-3.75 4a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </nav>
                @endif
                <!-- End Pagination -->
            </div>
            <!-- End Footer Section -->

        </div>
    </div>
</main>
@endsection
