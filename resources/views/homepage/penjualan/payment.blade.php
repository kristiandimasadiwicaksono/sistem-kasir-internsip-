@extends('layouts.main')

@section('title', 'Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-2xl shadow-2xl p-5 md:p-8 border border-gray-100">

        <h2 class="text-2xl font-extrabold text-gray-900 mb-6 text-center">
            Proses Pembayaran
        </h2>
        
        <div class="mb-6 p-5 bg-gray-50 rounded-xl border border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Ringkasan Pesanan</h3>
                <a href="{{ route('payment-edit', $penjualan->id) }}"
                   class="inline-flex items-center px-3 py-1.5 bg-gray-200 text-gray-700 font-medium rounded-full text-sm transition-colors duration-300 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Edit
                </a>
            </div>

            <table class="w-full text-sm border-separate [border-spacing:0_8px]">
                <thead class="hidden">
                    <tr>
                        <th class="text-left">Produk</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($penjualan->details as $detail)
                    <tr class="hover:bg-gray-100 transition-colors">
                        <td class="py-2 px-4">{{ $detail->produk->nama_produk }}</td>
                        <td class="py-2 px-4 text-center text-gray-600">{{ $detail->jumlah }}x</td>
                        <td class="py-2 px-4 text-right font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center bg-blue-600 text-white rounded-xl p-5 mb-6 shadow-md">
            <span class="text-lg font-bold">Total </span>
            <span class="text-2xl font-extrabold">
                Rp {{ number_format($penjualan->total, 0, ',', '.') }}
            </span>
        </div>

        <div class="space-y-5">
            <div>
                <label for="metode" class="block text-sm font-medium text-gray-700 mb-2">Pilih Metode Pembayaran</label>
                <select id="metode" name="metode" class="w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="cash">Cash</option>
                    <option value="cashless">Cashless (QRIS / e-wallet)</option>
                </select>
            </div>

            <form id="form-cash" method="POST" action="{{ route('payment.cash', $penjualan->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label for="dibayar" class="block text-sm font-medium text-gray-700 mb-2">Uang Dibayar</label>
                    <input type="number" name="dibayar" id="dibayar" min="0"
                           class="w-full border border-gray-300 rounded-lg py-2.5 px-4 text-lg placeholder-gray-400 focus:ring-green-500 focus:border-green-500 transition-colors"
                           placeholder="Masukkan nominal uang yang dibayarkan">
                </div>
                <div>
                    <label for="kembalian" class="block text-sm font-medium text-gray-700 mb-2">Kembalian</label>
                    <input type="text" id="kembalian" readonly
                           class="w-full border-2 border-dashed border-gray-300 rounded-lg py-2.5 px-4 bg-gray-50 text-gray-700 font-bold text-lg">
                </div>
                <button type="submit"
                         class="w-full py-2.5 px-4 rounded-lg bg-green-600 text-white font-semibold text-lg hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    Bayar Tunai
                </button>
            </form>

            <div id="form-cashless" class="hidden">
                <button id="pay-button"
                         class="w-full py-2.5 px-4 rounded-lg bg-purple-600 text-white font-semibold text-lg hover:bg-purple-700 transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    Bayar dengan QRIS
                </button>
            </div>
        </div>

    </div>
</div>

@php
    $snapUrl = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<script src="{{ $snapUrl }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        
<script>
    const metode = document.getElementById('metode');
    const formCash = document.getElementById('form-cash');
    const formCashless = document.getElementById('form-cashless');
    const inputDibayar = document.getElementById('dibayar');
    const inputKembalian = document.getElementById('kembalian');
    const total = {{ $penjualan->total }};

    // Toggle form visibility
    metode.addEventListener('change', function() {
        formCash.classList.toggle('hidden', this.value !== 'cash');
        formCashless.classList.toggle('hidden', this.value === 'cash');
    });

    // Real-time change calculation
    inputDibayar.addEventListener('input', function() {
        const bayar = parseInt(this.value) || 0;
        const kembali = bayar - total;
        inputKembalian.value = kembali >= 0 ? "Rp " + kembali.toLocaleString('id-ID') : "Rp " + kembali.toLocaleString('id-ID');
        
        // Disable cash button if payment is insufficient
        const cashButton = formCash.querySelector('button[type="submit"]');
        cashButton.disabled = bayar < total;
    });

    // Cashless Payment Handler
    document.getElementById('pay-button').addEventListener('click', function (e) {
        e.preventDefault();
        
        const payButton = this;
        payButton.disabled = true;
        payButton.textContent = 'Memuat...';

        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                window.location.href = "{{ route('payment.success', ['id' => $penjualan->id]) }}";
            },
            onPending: function(result) {
                alert('Pembayaran sedang diproses. Silakan selesaikan pembayaran Anda.');
                payButton.disabled = false;
                payButton.textContent = 'Bayar dengan QRIS';
            },
            onError: function(result) {
                alert("Pembayaran gagal, silakan coba lagi.");
                payButton.disabled = false;
                payButton.textContent = 'Bayar dengan QRIS';
            },
            onClose: function() {
                payButton.disabled = false;
                payButton.textContent = 'Bayar dengan QRIS';
            }
        });
    });

    // Cash Payment Handler
    formCash.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = formCash.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses...';

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: new FormData(this)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => { throw new Error(errorData.error || 'Server error occurred'); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.href = "{{ route('payment.success', ['id' => $penjualan->id]) }}";
            } else {
                alert(data.error || 'Gagal memproses pembayaran.');
            }
        })
        .catch(error => {
            console.error(error);
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Bayar Tunai';
        });
    });
</script>
@endsection
