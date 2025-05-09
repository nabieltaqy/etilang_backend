<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Midtrans\Notification;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Ticket;


class MidtransController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function createTransaction(Request $request)
    {
        // Validasi input
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'amount' => 'required|numeric',
        ]);

        // Cek apakah tiket sudah memiliki transaksi sebelumnya
        $existingTransaction = Ticket::find($request->ticket_id)->transactions()->first();

        // Jika ada transaksi yang sudah ada, hapus transaksi lama
        if ($existingTransaction) {
            // Hapus transaksi lama (misalnya, hapus berdasarkan order_id atau ID transaksi)
            $existingTransaction->delete();
        }

        // Membuat order_id dan transaction_details
        $orderId = 'ORDER-' . Str::substr($request->ticket_id, 0, 8) . '-' . time();  // Generate order_id yang unik
        $transactionDetails = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $request->amount,
            ],
            'ticket_id' => $request->ticket_id,
            'credit_card' => [
                'secure' => true, // Menggunakan 3D Secure
            ],
            // 'notification_url' => 'https://b991-103-233-100-204.ngrok-free.app/api/midtrans/callback',
        ];

        // Kirim request untuk transaksi ke Midtrans
        $response = $this->midtransService->createTransaction($transactionDetails);

        // Cek jika ada error dalam response
        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 400);
        }

        // Mengembalikan snap_token atau informasi transaksi lainnya
        return response()->json(['snap_token' => $response]);
    }


    // mengambil status transaksi dari Midtrans

    //     public function getTransactionStatus($orderId)
    // {
    //     try {
    //         // Mendapatkan status transaksi berdasarkan order_id
    //         $status = Transaction::status($orderId);

    //         // Mengembalikan status transaksi dalam bentuk array
    //         return response()->json(['status' => $status ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // Untuk menerima dan menangani callback dari Midtrans
    // public function handleCallback(Request $request)
    // {
    //     // Inisialisasi notifikasi dari Midtrans
    //     $notification = new Notification();

    //     // Ambil data dari notifikasi
    //     $status = $notification->transaction_status; // Status transaksi dari Midtrans
    //     $orderId = $notification->order_id;           // ID order dari transaksi
    //     $paymentType = $notification->payment_type;   // Jenis pembayaran (e.g., credit_card, bank_transfer, etc.)

    //     // Mencari transaksi berdasarkan order_id
    //     $transaction = Transaction::where('order_id', $orderId)->first();

    //     // Jika transaksi tidak ditemukan
    //     if (!$transaction) {
    //         return response()->json(['message' => 'Transaction not found'], 404);
    //     }

    //     // Update status transaksi berdasarkan status dari Midtrans
    //     switch ($status) {
    //         case 'capture':
    //         case 'settlement':
    //             // Pembayaran berhasil, update status menjadi 'success'
    //             $transaction->status = 'success';
    //             break;

    //         case 'pending':
    //             // Pembayaran sedang diproses, tetap dalam status 'pending'
    //             $transaction->status = 'pending';
    //             break;

    //         case 'cancel':
    //         case 'deny':
    //         case 'expire':
    //             // Pembayaran gagal, update status menjadi 'failed'
    //             $transaction->status = 'failed';
    //             break;

    //         default:
    //             // Status lainnya bisa ditangani sesuai kebutuhan
    //             break;
    //     }

    //     // Menyimpan informasi metode pembayaran
    //     $transaction->payment_method = $paymentType;

    //     // Simpan perubahan status transaksi
    //     $transaction->save();

    //     // Kembalikan respons sukses
    //     return response()->json(['message' => 'Transaction status updated']);
    // }
}
