@extends('layouts.main')

@section('title', 'History Penjualan')

@section('content')
<!-- Main Content (History Table) -->
<main class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="px-6 py-6 border-b border-gray-200 bg-gray-50 md:flex md:justify-between md:items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">History Penjualan</h2>
                    <p class="mt-1 text-sm text-gray-600">Daftar riwayat transaksi penjualan Anda.</p>
                </div>
                <!-- Optional: Add a search bar or filter here -->
            </div>
            <!-- End Header Section -->

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Kode Transaksi
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Tanggal Beli
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Tanggal Batal
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Produk
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Jumlah
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($history as $id_penjualan => $items)
                            @php
                                $firstItem = $items->first();
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 text-center">
                                    {{ $firstItem->kode_transaksi }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $firstItem->tanggal ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">
                                    {{ $firstItem->tanggal_batal ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                        @foreach($items as $item)
                                            <li>{{ optional($item->produk)->nama_produk ?? '-' }} (x{{ $item->jumlah }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <ul class="list-inside text-sm text-gray-700 space-y-1">
                                        @foreach($items as $item)
                                            <li>x{{ $item->jumlah }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($firstItem->status === 'SUCCESS')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            SUCCESS
                                        </span>
                                    @elseif($firstItem->status === 'CANCELED')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            CANCELED
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            {{ $firstItem->status }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-lg">
                                    Belum ada history.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
@endsection