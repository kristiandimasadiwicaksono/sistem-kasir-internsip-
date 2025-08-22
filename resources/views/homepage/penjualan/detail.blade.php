@extends('layouts.main')

@section('title', 'Detail Penjualan #' . ($penjualan->id ?? ''))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-xl p-6 md:p-10">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8">
            <div class="mb-4 sm:mb-0">
                <h2 class="text-3xl font-bold text-gray-800">Detail Penjualan</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap tentang transaksi ini.</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('penjualan.print', $penjualan->id) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg text-sm transition duration-300 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6m4-12H9m5-4v2m-6 0h.01M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Cetak
                </a>

                <a href="{{ route('penjualan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg text-sm transition duration-300 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Meta -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 bg-gray-50 p-6 rounded-xl border border-gray-100">
            <div class="flex items-center">
                <div class="text-gray-500 text-sm">ID Penjualan</div>
                <div class="font-bold text-gray-900 ml-2">#{{ $penjualan->id ?? '-' }}</div>
            </div>
            <div class="flex items-center">
                <div class="text-gray-500 text-sm">Tanggal</div>
                <div class="font-bold text-gray-900 ml-2">{{ isset($penjualan->tanggal) ? \Carbon\Carbon::parse($penjualan->tanggal)->format('d F Y H:i') : '-' }}</div>
            </div>
            <div class="flex items-center">
                <div class="text-gray-500 text-sm">Total</div>
                <div class="font-bold text-green-600 ml-2 text-xl">Rp {{ number_format($penjualan->total ?? ($penjualan->details->sum('subtotal') ?? 0),0,',','.') }}</div>
            </div>
        </div>

        <!-- Actions: tambah item -->
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Produk</h3>
            <a href="{{ route('penjualan.detail.create', $penjualan->id) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg text-sm transition duration-300 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Item
            </a>
        </div>

        <!-- Table detail -->
        <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($penjualan->details ?? [] as $index => $detail)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ optional($detail->produk)->nama_produk ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-right">Rp {{ number_format($detail->harga ?? optional($detail->produk)->harga ?? 0,0,',','.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-center">{{ $detail->jumlah ?? 0 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-right">Rp {{ number_format($detail->subtotal ?? (($detail->harga ?? optional($detail->produk)->harga ?? 0) * ($detail->jumlah ?? 0)),0,',','.') }}</td>
                            <td class="px-6 py-4 text-sm text-center space-x-2">
                                <a href="{{ route('penjualan.detail.edit', [$penjualan->id, $detail->id]) }}" class="text-yellow-600 hover:text-yellow-800 transition-colors">Edit</a>
                                <form action="{{ url('penjualan/'.$penjualan->id.'/detail/'.$detail->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus item ini? Transaksi tidak dapat dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-gray-500 font-medium">Belum ada item pada penjualan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
