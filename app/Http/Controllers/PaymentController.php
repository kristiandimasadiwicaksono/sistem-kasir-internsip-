<?php

namespace App\Http\Controllers;

use App\Models\HistoryPenjualan;
use App\Models\Penjualan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
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

        $params = [
            'transaction_details' => [
                'order_id' => $penjualan->kode_transaksi,
                'gross_amount' => $penjualan->total,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

    return view('homepage.penjualan.payment', compact('penjualan', 'snapToken'));
    }

// Di dalam class PaymentController

public function notification(Request $request)
{
    // Pastikan request method adalah POST
    if ($request->isMethod('post')) {
        // Ambil payload dari request
        $payload = $request->getContent();
        $notification = json_decode($payload);

        // Validasi signature key untuk keamanan
        $signature_key = hash('sha512', $notification->order_id . $notification->status_code . $notification->gross_amount . Config::$serverKey);
        
        if ($signature_key != $notification->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $penjualan = Penjualan::where('kode_transaksi', $notification->order_id)->first();

        if ($penjualan) {
            $transaction = $notification->transaction_status;
            
            if ($transaction == 'settlement' || $transaction == 'capture') {
                $penjualan->update(['status' => 'SUCCESS']);
                HistoryPenjualan::where('id_penjualan', $penjualan->id)
                    ->update(['status' => 'SUCCESS']);
                
                // Kurangi stok produk
                foreach ($penjualan->details as $detail) {
                    $produk = $detail->produk;
                    if ($produk) {
                        $produk->decrement('stok', $detail->jumlah);
                    }
                }

            } elseif ($transaction == 'pending') {
                $penjualan->update(['status' => 'PENDING']);

            } else { // Jika status lain (expire, deny, cancel, dll.)
                $penjualan->update(['status' => 'CANCELLED']);
                HistoryPenjualan::where('id_penjualan', $penjualan->id)
                    ->update([
                        'status' => 'CANCELED',
                        'tanggal_batal' => now(),
                    ]);
            }
        }
    }
    return response()->json(['message' => 'OK'], 200);
}

    public function cash(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $dibayar = $request->dibayar;

        if ($dibayar < $penjualan->total) {
            return response()->json(['error' => 'Uang kurang'], 400);
        }

        $penjualan->status = 'SUCCESS';
        $penjualan->save();


        HistoryPenjualan::where('id_penjualan', $penjualan->id)
            ->update(['status' => 'SUCCESS']);

        return response()->json([
            'success' => true,
            'print_url' => route('penjualan.print', $penjualan->id),
            'redirect_url' => route('penjualan.index')
        ]);
    }

    public function cashlessSuccess($id) {
    $penjualan = Penjualan::findOrFail($id);

    $penjualan->update(['status' => 'SUCCESS']);
    HistoryPenjualan::where('id_penjualan', $penjualan->id)
        ->update(['status' => 'SUCCESS']);

        return response()->json([
            'success' => true,
            'print_url' => route('penjualan.print', $penjualan->id),
            'redirect_url' => route('penjualan.index')
        ]);
    }
}
