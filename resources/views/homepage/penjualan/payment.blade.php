@extends('layouts.main')

@section('title','Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-10">
        
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            Pembayaran
        </h2>

        {{-- Detail Transaksi --}}
        <div class="mb-6">
            <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-600 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Produk</th>
                        <th class="px-4 py-2 text-center">Jumlah</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($penjualan->details as $detail)
                        <tr>
                            <td class="px-4 py-2">{{ $detail->produk->nama_produk }}</td>
                            <td class="px-4 py-2 text-center">{{ $detail->jumlah }}</td>
                            <td class="px-4 py-2 text-right">
                                Rp {{ number_format($detail->subtotal,0,',','.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Total --}}
        <div class="flex justify-between items-center bg-gray-50 rounded-lg p-4 mb-6">
            <span class="text-lg font-semibold text-gray-700">Total</span>
            <span class="text-xl font-bold text-blue-600">
                Rp {{ number_format($penjualan->total,0,',','.') }}
            </span>
        </div>

        {{-- Pilih metode pembayaran --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
            <select id="metode" class="w-full border rounded-lg py-2.5 px-4">
                <option value="cash">Cash</option>
                <option value="cashless">Cashless (QRIS / e-wallet)</option>
            </select>
        </div>

        {{-- Form Cash --}}
        <form id="form-cash" method="POST" action="{{ route('payment.cash', $penjualan->id) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Uang Dibayar</label>
                <input type="number" name="dibayar" id="dibayar" min="0"
                       class="w-full border rounded-lg py-2.5 px-4"
                       placeholder="Masukkan nominal pembayaran">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kembalian</label>
                <input type="text" id="kembalian" readonly
                       class="w-full border rounded-lg py-2.5 px-4 bg-gray-100">
            </div>
            <button type="submit"
                class="w-full py-2.5 px-4 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700">
                Bayar Cash
            </button>
        </form>

        {{-- Tombol Cashless --}}
        <div id="form-cashless" class="hidden">
            <button id="pay-button"
                class="w-full py-2.5 px-4 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700">
                Bayar Online
            </button>
        </div>
    </div>
</div>

@php
    $snapUrl = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

{{-- Midtrans Script --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
        
<script>
    const metode = document.getElementById('metode');
    const formCash = document.getElementById('form-cash');
    const formCashless = document.getElementById('form-cashless');

    // Ganti tampilan form sesuai metode
    metode.addEventListener('change', function() {
        if (this.value === 'cash') {
            formCash.classList.remove('hidden');
            formCashless.classList.add('hidden');
        } else {
            formCash.classList.add('hidden');
            formCashless.classList.remove('hidden');
        }
    });

    // Hitung kembalian realtime
    const inputDibayar = document.getElementById('dibayar');
    const inputKembalian = document.getElementById('kembalian');
    const total = {{ $penjualan->total }};

    inputDibayar.addEventListener('input', function() {
        let bayar = parseInt(this.value) || 0;
        let kembali = bayar - total;
        inputKembalian.value = kembali >= 0 ? "Rp " + kembali.toLocaleString() : "-";
    });

    // Snap popup (Cashless)
// Di dalam script tag di payment.blade.php

document.getElementById('pay-button').addEventListener('click', function (e) {
    e.preventDefault();

    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            console.log('Cashless Success:', result);

            window.open("{{ route('penjualan.print', $penjualan->id) }}", '_blank');
            window.location.href = "{{ route('penjualan.index') }}";
        },
        onPending: function(result) {
            console.log('Pending:', result);
            alert("Pembayaran sedang diproses, silakan tunggu.");
        },
        onError: function(result) {
            console.log('Error:', result);
            alert("Pembayaran gagal, silakan coba lagi.");
        },
        onClose: function() {
            alert('Popup ditutup tanpa menyelesaikan pembayaran.');
        }
    });
});

    formCash.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = formCash.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses...';

        const formData = new FormData(formCash);
        const actionUrl = formCash.getAttribute('action');

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // ✅ Buka tab print
                window.open(data.print_url, '_blank');
                // ✅ Redirect halaman pembayaran ke index
                window.location.href = data.redirect_url;
            } else {
                alert(data.error || 'Gagal memproses pembayaran.');
            }
        })
        .catch(error => {
            console.error(error);
            alert('Terjadi kesalahan pada server.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Bayar Cash';
        });
    });
</script>

@endsection
