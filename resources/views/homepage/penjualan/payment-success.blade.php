@extends('layouts.main')

@section('title', 'Pembayaran Berhasil')

@section('content')
<style>
    @keyframes spin-border {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .animate-spin-border {
        animation: spin-border 2s linear infinite;
    }
</style>
<div class="max-w-xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div id="success-card" class="bg-white rounded-3xl shadow-lg border border-gray-200 p-6 sm:p-10 transition-transform duration-1000 transform -translate-y-10 opacity-0">

        <!-- Bagian atas dengan ikon dan judul -->
        <div class="flex flex-col items-center justify-center text-center mb-6">
            <div id="success-icon" class="relative w-12 h-12 mb-3">
                <!-- Outer spinning circle (the border) -->
                <div class="absolute inset-0 rounded-full bg-green-100 animate-spin-border"></div>
                <!-- Inner static circle (to cover the center of the outer spinner) -->
                <div class="absolute inset-1 rounded-full bg-white flex items-center justify-center">
                    <!-- Ikon SVG untuk indikator berhasil -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-extrabold text-green-700 mb-1">Pembayaran Berhasil!</h1>
            <p class="text-base text-gray-600">Transaksi Anda telah selesai dengan sukses.</p>
        </div>

        <!-- Kartu Ringkasan Transaksi -->
        <div class="bg-gray-50 rounded-2xl p-5 mb-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Ringkasan Transaksi</h2>
            <div class="grid grid-cols-2 gap-y-2 text-gray-600 text-sm">
                <p class="font-semibold">Kode Transaksi</p>
                <p class="text-right">{{ $penjualan->kode_transaksi }}</p>

                <p class="font-semibold">Tanggal Transaksi</p>
                <p class="text-right">{{ \Carbon\Carbon::parse($penjualan->created_at)->translatedFormat('d F Y, H:i') }}</p>

                <p class="font-semibold">Total Pembayaran</p>
                <p class="text-right font-bold text-gray-900">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</p>
                
                @if(!is_null($kembalian))
                <p class="font-semibold text-green-600">Kembalian</p>
                <p class="text-right font-bold text-green-600">Rp {{ number_format($kembalian, 0, ',', '.') }}</p>
                @endif
            </div>
        </div>

        <!-- Rincian Item yang Dibeli -->
        <h2 class="text-lg font-bold text-gray-800 mb-3">Rincian Item</h2>
        <div class="space-y-3 mb-6">
            @foreach($penjualan->details as $detail)
            <div class="flex justify-between items-center border-b pb-2 last:border-b-0 last:pb-0">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ $detail->produk->nama_produk }}</p>
                    <p class="text-sm text-gray-500">{{ $detail->jumlah }} x Rp {{ number_format($detail->produk->harga, 0, ',', '.') }}</p>
                </div>
                <p class="font-bold text-right text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-center">
            <button id="btn-ok"
                class="px-6 py-3 bg-blue-600 text-white rounded-full text-base font-semibold hover:bg-blue-700 transition shadow-lg animate-pulse">
                Selesai & Cetak Struk
            </button>
        </div>
        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const successCard = document.getElementById('success-card');
        successCard.classList.remove('opacity-0', '-translate-y-10');
        successCard.classList.add('opacity-100', 'translate-y-0');
    });

    document.getElementById('btn-ok').addEventListener('click', function() {
        window.open("{{ route('penjualan.print', $penjualan->id) }}", '_blank');
        window.location.href = "{{ route('penjualan.index') }}";
    });
</script>
@endsection
