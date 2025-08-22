<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Penjualan #{{ $penjualan->id }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    /* Variabel Warna dan Font yang Diperbarui */
    :root {
        --text-dark: #2c3e50; /* Teks Utama yang lebih lembut */
        --text-light: #7f8c8d; /* Teks sekunder/muted */
        --border: #e8e8e8; /* Garis tepi yang lebih halus */
        --bg-light: #f9f9f9; /* Latar belakang untuk elemen tertentu */
        --bg-white: #ffffff; /* Latar belakang putih */
        --primary-color: #3498db; /* Warna aksen biru yang cerah */
    }

    /* Reset dan Font Dasar */
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        color: var(--text-dark);
        background: var(--bg-light);
    }

    /* Halaman dan Kontainer Utama */
    .sheet {
        max-width: 800px;
        margin: 40px auto;
        padding: 40px;
        background: var(--bg-white);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* Header Dokumen */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .header-info h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
        color: var(--primary-color);
    }

    .header-info p {
        margin: 4px 0 0;
        font-size: 14px;
        color: var(--text-light);
    }

    .meta {
        text-align: right;
        font-size: 14px;
        color: var(--text-light);
    }

    .meta div {
        margin-bottom: 4px;
    }
    
    .meta strong {
        color: var(--text-dark);
    }

    /* Garis Pembatas */
    .divider {
        height: 1px;
        background: var(--border);
        margin: 24px 0;
    }

    /* Tabel Produk */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        font-size: 14px;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    th {
        background: var(--bg-light);
        color: var(--text-dark);
        text-align: left;
        font-weight: 500;
    }

    tr:last-child td {
        border-bottom: none;
    }

    td.qty, td.price, td.subtotal {
        text-align: right;
    }
    
    .totals-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
        border-top: 1px solid var(--border); /* Garis atas untuk bagian total */
        padding-top: 24px; /* Jarak antara garis dan tabel total */
    }

    /* Bagian Total */
    .totals {
        width: 100%;
        max-width: 300px;
    }

    .totals td {
        padding: 12px 16px;
        font-size: 15px;
        border: none;
    }

    .totals .label {
        text-align: right;
        font-weight: 500;
        color: var(--text-light);
    }

    .totals .value {
        text-align: right;
        font-weight: 600;
    }
    
    .totals tr:last-child {
        border-top: 2px solid var(--border);
    }
    
    .totals tr:last-child .label,
    .totals tr:last-child .value {
        font-size: 18px;
        color: var(--text-dark);
    }

    /* Catatan Kaki */
    .note {
        margin-top: 24px;
        font-size: 12px;
        color: var(--text-light);
        text-align: center;
        line-height: 1.6;
    }

    /* Aksi dan Tombol */
    .actions {
        margin: 24px auto 0;
        text-align: center;
    }

    .btn {
        display: inline-block;
        padding: 12px 24px;
        background-color: var(--primary-color);
        color: #fff;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #2980b9;
    }

    /* CSS Khusus untuk Print */
    @media print {
        body {
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sheet {
            box-shadow: none;
            border: none;
            margin: 0;
            padding: 0;
            border-radius: 0;
        }

        .actions {
            display: none !important;
        }

        @page {
            size: A4;
            margin: 20mm;
        }
    }
</style>
</head>
<body>

<div class="sheet">
    <div class="header">
        <div class="header-info">
            <h1 class="title">Nota Penjualan</h1>
            <p>Toko ABCDEFG<br>
            Jl. Contoh No. 123 • 0812-0000-0000</p>
        </div>
        <div class="meta">
            <div><strong>ID:</strong> #{{ $penjualan->id }}</div>
            <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d M Y H:i') }}</div>
        </div>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="width:36px;">No</th>
                <th>Produk</th>
                <th style="width:80px; text-align:right;">Qty</th>
                <th style="width:120px; text-align:right;">Harga</th>
                <th style="width:140px; text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1; @endphp
            @foreach($penjualan->details as $detail)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                    <td class="qty">{{ $detail->jumlah }}</td>
                    <td class="price">Rp {{ number_format($detail->produk->harga ?? 0, 0, ',', '.') }}</td>
                    <td class="subtotal">Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals">
            <tr>
                <td class="label"><strong>Total</strong></td>
                <td class="value"><strong>Rp {{ number_format($penjualan->total ?? 0, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="note">
        * Simpan nota ini sebagai bukti transaksi. Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada perjanjian.
    </div>

    <div class="actions">
        <a href="#" class="btn" onclick="window.print(); return false;">Cetak Nota</a>
    </div>
</div>

</body>
</html>
