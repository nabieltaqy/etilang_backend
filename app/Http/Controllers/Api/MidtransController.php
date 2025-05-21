<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Notification;


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
            // 'amount' => 'required|numeric',
            'type'      => 'required',
        ]);

        $type   = $request->type;

        $ticket   = Ticket::with(['violation.violationType'])->find($request->ticket_id);
        $max_fine = $ticket->violation->violationType->max_fine;

        // Cek apakah tiket sudah memiliki transaksi sebelumnya
        $existingTransaction = Ticket::find($request->ticket_id)->transaction();

        // Jika ada transaksi yang sudah ada, hapus transaksi lama
        if ($existingTransaction) {
            // Hapus transaksi lama (misalnya, hapus berdasarkan order_id atau ID transaksi)
            $existingTransaction->delete();
        }

        // Membuat order_id dan transaction_details
        $orderId            = 'ORDER-' . Str::substr($request->ticket_id, 0, 8) . '-' . time(); // Generate order_id yang unik
        $transactionDetails = [
            'transaction_details' => [
                'order_id'     => $orderId,
                // 'gross_amount' => $max_fine, // Jumlah total yang harus dibayar
                'gross_amount' => 10, // Jumlah total yang harus dibayar
            ],
            'ticket_id'           => $request->ticket_id,
            'credit_card'         => [
                'secure' => true, // Menggunakan 3D Secure
            ],
        ];


        // Kirim request untuk transaksi ke Midtrans
        $response = $this->midtransService->createTransaction($transactionDetails);

        Activity::create([
            'ticket_id'   => $request->ticket_id,
            'name'        => 'Transaksi Dibuat',
            'description' => 'Transaksi ' . $type . ' untuk tiket ID ' . $request->ticket_id . ' telah dibuat.',
        ]);

        // Cek jika ada error dalam response
        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 400);
        }

        // Mengembalikan snap_token atau informasi transaksi lainnya
        return response()->json(['snap_token' => $response]);
    }

    public function callback(Request $request)
{
    try {
        // Ambil data dari body request
        $notification = json_decode($request->getContent());

        $transactionStatus = $notification->transaction_status;
        $paymentType       = $notification->payment_type;
        $fraudStatus       = $notification->fraud_status ?? null;
        $orderId           = $notification->order_id;
        $transactionId     = $notification->transaction_id ?? null;

        // Cari transaksi di database
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Update transaksi
        $transaction->status         = $transactionStatus;
        $transaction->payment_method = $paymentType;
        $transaction->save();

        // Ubah status tiket jika pembayaran sukses
        if (
            $transactionStatus === 'settlement' ||
            ($transactionStatus === 'capture' && $paymentType === 'credit_card' && $fraudStatus === 'accept')
        ) {
            $ticket = Ticket::find($transaction->ticket_id);
            if ($ticket) {
                $ticket->status = 'Sudah Bayar';
                $ticket->save();
            }
        }

        // Catat aktivitas
        Activity::create([
            'ticket_id'   => $transaction->ticket_id,
            'name'        => 'Transaksi Diterima',
            'description' => 'Status: ' . $transactionStatus,
        ]);

        return response()->json(['message' => 'Callback diproses'], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Terjadi kesalahan saat memproses callback',
            'debug' => $e->getMessage()
        ], 500);
    }
}
}
