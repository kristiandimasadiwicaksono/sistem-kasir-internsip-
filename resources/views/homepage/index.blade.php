@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-8 pb-8 sm:px-6 lg:px-8">
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow p-6 md:p-8">
                <h1 class="text-3xl font-bold text-gray-900">Selamat Datang, {{ Auth::user()->name }}!</h1>
                <p class="mt-2 text-base text-gray-600">
                    Berikut ringkasan aktivitas toko Anda.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Penjualan Hari Ini -->
                <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between border-b-2 border-blue-500 transition-transform transform hover:scale-105">
                    <div class="space-y-0.5">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Penjualan Hari Ini</p>
                        <span class="text-2xl font-bold text-gray-800">{{ $penjualanHariIni ?? 0 }}</span>
                    </div>
                    <div class="p-2 bg-blue-100 text-blue-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path d="M6 2a2 2 0 0 0-2 2v18l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V4a2 2 0 0 0-2-2H6z"/>
                        </svg>
                    </div>
                </div>

                <!-- Total Produk -->
                <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between border-b-2 border-indigo-500 transition-transform transform hover:scale-105">
                    <div class="space-y-0.5">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Produk</p>
                        <span class="text-2xl font-bold text-gray-800">{{ $totalProduk ?? 0 }}</span>
                    </div>
                    <div class="p-2 bg-indigo-100 text-indigo-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path d="M21 7.5l-9-4.5-9 4.5V18l9 4.5 9-4.5V7.5z"/>
                        </svg>
                    </div>
                </div>

                <!-- Penjualan Bulan Ini -->
                <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between border-b-2 border-green-500 transition-transform transform hover:scale-105">
                    <div class="space-y-0.5">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Penjualan Bulan Ini</p>
                        <span class="text-2xl font-bold text-gray-800">RP {{ number_format($penjualanBulanIni ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2 bg-green-100 text-green-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path d="M3 17h2v-6H3v6zm4 0h2V7H7v10zm4 0h2V3h-2v14zm4 0h2v-4h-2v4zm4 0h2V9h-2v8z"/>
                        </svg>
                    </div>
                </div>

                <!-- Total Transaksi -->
                <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between border-b-2 border-yellow-500 transition-transform transform hover:scale-105">
                    <div class="space-y-0.5">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Transaksi</p>
                        <span class="text-2xl font-bold text-gray-800">{{ $totalTransaksi }}</span>
                    </div>
                    <div class="p-2 bg-yellow-100 text-yellow-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6zm10 3a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Penjualan Terbaru</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Transaksi ID
                                </th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($aktivitasTerbaru as $trx)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $trx->kode_transaksi ?? $trx->id }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d F Y, H:i A') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($trx->status === 'SUCCESS')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Dibatalkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-bold">
                                        Rp {{ number_format($trx->penjualan->total ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">
                                        Belum ada aktivitas terbaru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('penjualan.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold text-sm transition-colors">
                        Lihat Semua Penjualan
                        <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M11 3a1 1 0 100 2h5.586l-7.293 7.293a1 1 0 101.414 1.414L17 6.414V12a1 1 0 102 0V4a1 1 0 00-1-1h-8z"/>
                        </svg>
                    </a>
                </div>
            </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Produk Terlaris</h2>
                    <ul class="divide-y divide-gray-200">
                        @foreach($topProduk as $produk)
                            <li class="py-2 flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">{{ $produk->nama_produk }}</span>
                                <span class="text-xs font-semibold px-2 py-1 bg-gray-200 rounded-full">{{ $produk->total_terjual}}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
