<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use App\Models\Transaction;

class MidtransService
{
    protected $client;

    public function __construct()
    {
        // Inisialisasi client Guzzle
        $this->client = new Client();
    }

    /**
     * Kirim request ke Midtrans untuk membuat transaksi.
     *
     * @param array $transactionDetails
     * @return mixed
     */
    public function createTransaction(array $transactionDetails)
    {
        $url ='https://app.midtrans.com/snap/v1/transactions'; // Dapatkan URL dari konfigurasi Midtrans
        $serverKey = Config::get('midtrans.server_key');

        // Membuat body JSON untuk transaksi
        $body = [
            'transaction_details' => $transactionDetails['transaction_details'],
            // 'credit_card' => $transactionDetails['credit_card']
        ];

        // Kirim request POST ke Midtrans
        try {
            $response = $this->client->request('POST', $url, [
                'json' => $body,
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Ambil response dan simpan ke tabel transactions
            $responseData = json_decode($response->getBody()->getContents(), true);

            // Jika response berhasil, simpan transaksi ke database
            if (isset($responseData['token'])) {
                // Simpan transaksi ke database
                $transaction = Transaction::create([
                    'ticket_id' => $transactionDetails['ticket_id'],
                    'order_id' => $transactionDetails['transaction_details']['order_id'],
                    'amount' => $transactionDetails['transaction_details']['gross_amount'],
                    'type' => $transactionDetails['type'],
                    'payment_method' => null, 
                    'status' => 'pending', // Status sementara, akan diupdate setelah callback Midtrans
                ]);

                return $responseData; // Kembalikan snap token atau data transaksi lainnya
            }

            return ['error' => 'Failed to create transaction'];

        } catch (\Exception $e) {
            // Menangani error jika ada
            return ['error' => $e->getMessage()];
        }
    }
}
