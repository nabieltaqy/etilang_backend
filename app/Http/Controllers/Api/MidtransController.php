<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
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
            // 'amount' => 'required|numeric',
        ]);

        $ticket = Ticket::with(['violation.violationType'])->find($request->ticket_id);
        $max_fine = $ticket->violation->violationType->max_fine;

        // Cek apakah tiket sudah memiliki transaksi sebelumnya
        $existingTransaction = Ticket::find($request->ticket_id)->transaction();

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
                'gross_amount' => $max_fine, // Jumlah total yang harus dibayar
            ],
            'ticket_id' => $request->ticket_id,
            'credit_card' => [
                'secure' => true, // Menggunakan 3D Secure
            ],
        ];

        // Kirim request untuk transaksi ke Midtrans
        $response = $this->midtransService->createTransaction($transactionDetails);

        Activity::create([
            'ticket_id' => $request->ticket_id,
            'name' => 'Transaksi Dibuat',
            'description' => 'Transaksi untuk tiket ID ' . $request->ticket_id . ' telah dibuat.',
        ]);

        // Cek jika ada error dalam response
        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 400);
        }

        // Mengembalikan snap_token atau informasi transaksi lainnya
        return response()->json(['snap_token' => $response]);
    }
}
