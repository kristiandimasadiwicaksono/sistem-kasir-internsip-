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
                <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between border-b-2 border-blue-500 transition-transform transform hover:scale-105">
                    <div class="space-y-0.5">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Penjualan Hari Ini</p>
                        <span class="text-2xl font-bold text-gray-800">{{ $penjualanHariIni ?? 0 }}</span>
                    </div>
                    <div class="p-2 bg-blue-100 text-blue-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m-6 0v6a2 2 0 002 2h2a2 2 0 002-2v-6m-4-2h6a2 2 0 002-2V7a2 2 0 00-2-2h-6a2 2 0 00-2 2v5a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between border-b-2 border-indigo-500 transition-transform transform hover:scale-105">
                    <div class="space-y-0.5">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Produk</p>
                        <span class="text-2xl font-bold text-gray-800">{{ $totalProduk ?? 0 }}</span>
                    </div>
                    <div class="p-2 bg-indigo-100 text-indigo-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v-1a4 4 0 014-4h4a4 4 0 014 4v1m-6 2l-6-6m6 6l6-6m-12 6a4 4 0 01-4-4v-1a4 4 0 014-4h4a4 4 0 014 4v1a4 4 0 01-4 4H8z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between border-b-2 border-green-500 transition-transform transform hover:scale-105">
                    <div class="space-y-0.5">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Penjualan Bulan Ini</p>
                        <span class="text-2xl font-bold text-gray-800">RP {{ number_format($penjualanBulanIni ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2 bg-green-100 text-green-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2V8z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6a2 2 0 00-2 2v1a2 2 0 002 2 2 2 0 002-2V8a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between border-b-2 border-yellow-500 transition-transform transform hover:scale-105">
                    <div class="space-y-0.5">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Transaksi</p>
                        <span class="text-2xl font-bold text-gray-800">{{ $totalTransaksi }}</span>
                    </div>
                    <div class="p-2 bg-yellow-100 text-yellow-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v10a.75.75 0 01-.75.75h-2.25a.75.75 0 01-.75-.75m-6-13a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v10a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75m-3-13a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v10a.75.75 0 01-.75.75H3.75a.75.75 0 01-.75-.75"/>
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
