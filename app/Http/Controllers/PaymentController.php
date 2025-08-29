<?php

namespace App\Http\Controllers;

use App\Models\HistoryPenjualan;
use App\Models\Penjualan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

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
        
        // Cek apakah sudah dibayar sebelumnya
        if ($penjualan->status === 'SUCCESS') {
            return response()->json(['error' => 'Transaksi ini sudah berhasil dibayar'], 400);
        }
        
        $dibayar = $request->dibayar;

        if ($dibayar < $penjualan->total) {
            return response()->json(['error' => 'Uang kurang'], 400);
        }

        DB::transaction(function () use ($penjualan) {
            // Update status menjadi SUCCESS
            $penjualan->update(['status' => 'SUCCESS']);

            // Update status history
            HistoryPenjualan::where('id_penjualan', $penjualan->id)
                ->update(['status' => 'SUCCESS']);

            // KURANGI STOK SETELAH PEMBAYARAN CASH BERHASIL
            foreach ($penjualan->details as $detail) {
                $produk = $detail->produk;
                if ($produk) {
                    $produk->decrement('stok', $detail->jumlah);
                }
            }
        });

        return response()->json([
            'success' => true,
            'print_url' => route('penjualan.print', $penjualan->id),
            'redirect_url' => route('penjualan.index')
        ]);
    }

    public function cashlessSuccess($id) {
        $penjualan = Penjualan::findOrFail($id);

        DB::transaction(function () use ($penjualan) {
            // Update status menjadi SUCCESS
            $penjualan->update(['status' => 'SUCCESS']);
            
            // Update status history
            HistoryPenjualan::where('id_penjualan', $penjualan->id)
                ->update(['status' => 'SUCCESS']);

            // KURANGI STOK SETELAH PEMBAYARAN CASHLESS BERHASIL
            foreach ($penjualan->details as $detail) {
                $produk = $detail->produk;
                if ($produk) {
                    $produk->decrement('stok', $detail->jumlah);
                }
            }
        });

        return response()->json([
            'success' => true,
            'print_url' => route('penjualan.print', $penjualan->id),
            'redirect_url' => route('penjualan.index')
        ]);
    }
}