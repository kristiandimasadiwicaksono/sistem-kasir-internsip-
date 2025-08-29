<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Produk;
use App\Models\Penjualan;
use Midtrans\Notification;
use Illuminate\Http\Request;
use App\Models\PenjualanDetail;
use App\Models\HistoryPenjualan;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;    
    }

    public function checkout($id){
        $penjualan = Penjualan::findOrFail($id);

        // Cek apakah penjualan sudah berhasil dibayar
        if ($penjualan->status === 'SUCCESS') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Transaksi ini sudah berhasil dibayar.');
        }

        // Selalu generate order_id baru untuk menghindari masalah "unparsable"
        $uniqueOrderId = $penjualan->kode_transaksi . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $uniqueOrderId,
                'gross_amount' => (int) $penjualan->total,
            ],
            'custom_field1' => $penjualan->kode_transaksi,
            'item_details' => [
                [
                    'id' => $penjualan->kode_transaksi,
                    'price' => (int) $penjualan->total,
                    'quantity' => 1,
                    'name' => 'Pembelian ' . $penjualan->kode_transaksi
                ]
            ]
        ];

        try {
            // Generate token baru dengan order_id yang selalu baru
            $snapToken = Snap::getSnapToken($params);
            
            // Update order_id terbaru ke database
            $penjualan->update(['midtrans_order_id' => $uniqueOrderId]);
            
        } catch (\Exception $e) {
            Log::error('Midtrans Error:', [
                'message' => $e->getMessage(),
                'params' => $params
            ]);
            
            return redirect()->back()->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
        }

        return view('homepage.penjualan.payment', compact('penjualan', 'snapToken'));
    }

    public function notification(Request $request){
        if ($request->isMethod('post')) {
            // Ambil raw body
            $payload = $request->getContent();
            $notification = json_decode($payload);

            // Kalau gagal decode JSON, fallback ke request all()
            if (!$notification) {
                $notification = (object) $request->all();
            }

            // Cek apakah ada order_id
            if (empty($notification->order_id)) {
                return response()->json(['message' => 'Invalid payload'], 400);
            }

            // Validasi signature
            $signature_key = hash('sha512', 
                $notification->order_id .
                $notification->status_code .
                $notification->gross_amount .
                Config::$serverKey
            );

            if ($signature_key != ($notification->signature_key ?? '')) {
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // ✅ Extract original kode_transaksi dari order_id yang unique
            $originalOrderId = $notification->custom_field1 ?? null;
            
            // Jika tidak ada custom_field1, coba extract dari order_id
            if (!$originalOrderId) {
                // Format: INV-00001-1693123456 -> ambil INV-00001
                $orderIdParts = explode('-', $notification->order_id);
                if (count($orderIdParts) >= 3) {
                    // Gabungkan kembali bagian awal (INV-00001)
                    array_pop($orderIdParts); // hapus timestamp
                    $originalOrderId = implode('-', $orderIdParts);
                } else {
                    $originalOrderId = $notification->order_id;
                }
            }

            // Cari penjualan berdasarkan original kode_transaksi
            $penjualan = Penjualan::where('kode_transaksi', $originalOrderId)->first();

            if ($penjualan) {
                DB::transaction(function () use ($penjualan, $notification) {
                    // ✅ Cek apakah transaksi sudah SUCCESS untuk mencegah double processing
                    if ($penjualan->status === 'SUCCESS') {
                        return; // Skip processing jika sudah berhasil
                    }

                    $transaction = $notification->transaction_status;

                    if (in_array($transaction, ['settlement', 'capture'])) {
                        // ✅ Update status penjualan menjadi SUCCESS
                        $penjualan->update(['status' => 'SUCCESS']);
                        
                        // ✅ Update history penjualan
                        HistoryPenjualan::where('id_penjualan', $penjualan->id)
                            ->update(['status' => 'SUCCESS']);

                        // ✅ BARU KURANGI STOK SETELAH PEMBAYARAN BERHASIL
                        foreach ($penjualan->details as $detail) {
                            $produk = $detail->produk;
                            if ($produk) {
                                $produk->decrement('stok', $detail->jumlah);
                            }
                        }

                    } elseif ($transaction === 'pending') {
                        $penjualan->update(['status' => 'PENDING']);
                        // ✅ PENDING = tidak kurangi stok
                        
                    } else {
                        // Status failed/cancelled/expired
                        $penjualan->update(['status' => 'CANCELLED']);
                        HistoryPenjualan::where('id_penjualan', $penjualan->id)
                            ->update([
                                'status' => 'CANCELED',
                                'tanggal_batal' => now(),
                            ]);
                        // ✅ CANCELLED = tidak kurangi stok
                    }
                });
            }
        }

        return response()->json(['message' => 'OK'], 200);
    }

    public function cash(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if ($penjualan->status === 'SUCCESS') {
            return response()->json(['error' => 'Transaksi ini sudah berhasil dibayar'], 400);
        }

        $dibayar = (int) $request->dibayar;
        if ($dibayar < $penjualan->total) {
            return response()->json(['error' => 'Uang kurang'], 400);
        }

        $kembalian = $dibayar - $penjualan->total;

        DB::transaction(function () use ($penjualan) {
            $penjualan->update(['status' => 'SUCCESS']);
            HistoryPenjualan::where('id_penjualan', $penjualan->id)->update(['status' => 'SUCCESS']);

            foreach ($penjualan->details as $detail) {
                $produk = $detail->produk;
                if ($produk) {
                    $produk->decrement('stok', $detail->jumlah);
                }
            }
        });

        return response()->json([
            'success' => true,
            'redirect_url' => route('payment.success', ['id' => $penjualan->id, 'kembalian' => $kembalian]),
        ]);
    }

    public function cashlessSuccess($id)
    {
        $penjualan = Penjualan::findOrFail($id);

        DB::transaction(function () use ($penjualan) {
            $penjualan->update(['status' => 'SUCCESS']);
            HistoryPenjualan::where('id_penjualan', $penjualan->id)->update(['status' => 'SUCCESS']);

            foreach ($penjualan->details as $detail) {
                $produk = $detail->produk;
                if ($produk) {
                    $produk->decrement('stok', $detail->jumlah);
                }
            }
        });

        return response()->json([
            'success' => true,
            'redirect_url' => route('payment.success', $penjualan->id),
        ]);
    }

    public function edit($id_penjualan) {
        $penjualan = Penjualan::with('details.produk')->findOrFail($id_penjualan);
        $produk    = Produk::orderBy('nama_produk')->get();

        return view('homepage.penjualan.payment-edit', compact('penjualan','produk'));
    }

    public function store(Request $request, $penjualanId)
    {
        $penjualan = Penjualan::with('details')->findOrFail($penjualanId);

        // Ambil only rows yang terisi (punya id_produk & jumlah)
        $raw = collect($request->input('products', []))
            ->filter(fn ($row) => !empty($row['id_produk']) && !empty($row['jumlah']))
            ->values();

        if ($raw->isEmpty()) {
            return back()->withErrors(['products' => 'Minimal 1 produk harus dipilih.'])->withInput();
        }

        // Gabungkan baris duplikat (produk sama dijumlahkan)
        $submitted = $raw->groupBy('id_produk')->map(function ($rows, $idProduk) {
            return [
                'id_produk' => (int)$idProduk,
                'jumlah'    => (int)collect($rows)->sum('jumlah'),
            ];
        })->values();

        // Validasi array
        $request->merge(['products' => $submitted->all()]);
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id_produk' => 'required|exists:produk,id',
            'products.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($penjualan, $submitted) {
            // Cek stok untuk tiap produk (total yang diminta pada penjualan ini)
            foreach ($submitted as $row) {
                $produk = Produk::findOrFail($row['id_produk']);

                // Jumlah baru yang ingin disimpan pada penjualan ini
                $jumlahBaru = (int)$row['jumlah'];

                if ($jumlahBaru > $produk->stok) {
                    abort(302, back()->withErrors([
                        'stok' => 'Jumlah "'.$produk->nama_produk.'" ('.$jumlahBaru.') melebihi stok ('.$produk->stok.').'
                    ])->withInput()->getTargetUrl());
                }
            }

            // Sinkronkan detail:
            // 1) Hapus detail yang tidak ada di submit (user sudah remove di UI)
            $submittedIds = $submitted->pluck('id_produk')->all();
            PenjualanDetail::where('id_penjualan', $penjualan->id)
                ->whereNotIn('id_produk', $submittedIds)
                ->delete();

            // 2) Upsert semua baris yang dikirim
            foreach ($submitted as $row) {
                $produk = Produk::findOrFail($row['id_produk']);

                $detail = PenjualanDetail::firstOrNew([
                    'id_penjualan' => $penjualan->id,
                    'id_produk'    => $produk->id,
                ]);

                $detail->jumlah   = (int)$row['jumlah'];
                $detail->subtotal = $detail->jumlah * (int)$produk->harga;
                $detail->save();
            }

            // 3) Recalculate total penjualan
            $total = PenjualanDetail::where('id_penjualan', $penjualan->id)->sum('subtotal');
            $penjualan->update(['total' => (int)$total]);
        });

        return redirect()->route('checkout', $penjualan->id)
            ->with('success', 'Produk berhasil disimpan.');
    }
    public function successPage($id, Request $request)
    {
        $penjualan = Penjualan::findOrFail($id);

        // Ambil nilai kembalian dari query string (hanya untuk cash)
        $kembalian = $request->query('kembalian', null);

        return view('homepage.penjualan.payment-success', compact('penjualan', 'kembalian'));
    }
}