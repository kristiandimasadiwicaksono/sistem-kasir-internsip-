@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-100 rounded-2xl shadow-xl p-8 md:p-12 border border-blue-200">
            <h1 class="text-4xl font-extrabold text-blue-900">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="mt-3 text-lg text-gray-700 max-w-2xl">
                Laman ini memberikan ringkasan singkat tentang penjualan dan produk Anda. Mari kita pantau kinerja toko Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6 flex items-center justify-between border-b-4 border-blue-500 transition-transform transform hover:scale-105 hover:shadow-2xl">
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-gray-500">Penjualan Hari Ini</p>
                    <span class="text-3xl font-bold text-gray-800">12</span>
                </div>
                <div class="p-3 bg-blue-100 text-blue-500 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m-6 0v6a2 2 0 002 2h2a2 2 0 002-2v-6m-4-2h6a2 2 0 002-2V7a2 2 0 00-2-2h-6a2 2 0 00-2 2v5a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 flex items-center justify-between border-b-4 border-indigo-500 transition-transform transform hover:scale-105 hover:shadow-2xl">
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-gray-500">Total Produk</p>
                    <span class="text-3xl font-bold text-gray-800">120</span>
                </div>
                <div class="p-3 bg-indigo-100 text-indigo-500 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v-1a4 4 0 014-4h4a4 4 0 014 4v1m-6 2l-6-6m6 6l6-6m-12 6a4 4 0 01-4-4v-1a4 4 0 014-4h4a4 4 0 014 4v1a4 4 0 01-4 4H8z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 flex items-center justify-between border-b-4 border-green-500 transition-transform transform hover:scale-105 hover:shadow-2xl">
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-gray-500">Penjualan Bulan Ini</p>
                    <span class="text-3xl font-bold text-gray-800">Rp 5.250.000</span>
                </div>
                <div class="p-3 bg-green-100 text-green-500 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2V8z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6a2 2 0 00-2 2v1a2 2 0 002 2 2 2 0 002-2V8a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 flex items-center justify-between border-b-4 border-yellow-500 transition-transform transform hover:scale-105 hover:shadow-2xl">
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-gray-500">Total Transaksi</p>
                    <span class="text-3xl font-bold text-gray-800">350</span>
                </div>
                <div class="p-3 bg-yellow-100 text-yellow-500 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v10a.75.75 0 01-.75.75h-2.25a.75.75 0 01-.75-.75m-6-13a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v10a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75m-3-13a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v10a.75.75 0 01-.75.75H3.75a.75.75 0 01-.75-.75"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Grafik Penjualan Bulanan</h2>
                <div class="h-64 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 text-sm">
                    Grafik akan muncul di sini (misalnya Chart.js atau ApexCharts)
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Produk Terlaris</h2>
                <ul class="divide-y divide-gray-200">
                    <li class="py-3 flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Produk A</span>
                        <span class="text-xs font-semibold px-2 py-1 bg-gray-200 rounded-full">50 Terjual</span>
                    </li>
                    <li class="py-3 flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Produk B</span>
                        <span class="text-xs font-semibold px-2 py-1 bg-gray-200 rounded-full">45 Terjual</span>
                    </li>
                    <li class="py-3 flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Produk C</span>
                        <span class="text-xs font-semibold px-2 py-1 bg-gray-200 rounded-full">30 Terjual</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800">Aktivitas Penjualan Terbaru</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Transaksi ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #20230822-001
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                22 Agustus 2025, 09:30 AM
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Selesai
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                Rp 500.000
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #20230822-002
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                22 Agustus 2025, 09:45 AM
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Dibatalkan
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                Rp 250.000
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-6 text-center">
                <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                    Lihat Semua Penjualan
                    <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11 3a1 1 0 100 2h5.586l-7.293 7.293a1 1 0 101.414 1.414L17 6.414V12a1 1 0 102 0V4a1 1 0 00-1-1h-8z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endsection