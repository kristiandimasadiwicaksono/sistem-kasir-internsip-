<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan</title>
    <style>
        @media print {
            body {
                width: 80mm;
                font-family: monospace;
                font-size: 12px;
            }
            .no-print { display: none; }
        }
        body {
            font-family: monospace;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .border-top { border-top: 1px dashed #000; margin: 5px 0; }
        .flex { display: flex; justify-content: space-between; }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.close(); }">
    <div class="text-center">
        <h2>Toko Anda</h2>
        <p>Jl. Contoh No. 123, Kota</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="border-top"></div>
    <p>Tanggal: {{ $penjualan->tanggal }}</p>
    <p>Kode: {{ $penjualan->kode_transaksi }}</p>
    <div class="border-top"></div>

    @foreach($penjualan->details as $item)
        <div>
            <p>{{ $item->produk->nama_produk }}</p>
            <div class="flex">
                <span>{{ $item->jumlah }} x {{ number_format($item->produk->harga,0,',','.') }}</span>
                <span>{{ number_format($item->subtotal,0,',','.') }}</span>
            </div>
        </div>
    @endforeach

    <div class="border-top"></div>
    <div class="flex">
        <strong>Total</strong>
        <strong>{{ number_format($penjualan->total,0,',','.') }}</strong>
    </div>
    <p>Status: {{ $penjualan->status }}</p>
    <div class="border-top"></div>

    <div class="text-center">
        <p>Terima Kasih!</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
    </div>
</body>
</html>
