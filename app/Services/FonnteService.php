<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    protected $token;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
    }

    public function sendWhatsapp($to, $message)
    {
        $url = 'https://api.fonnte.com/send';

        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->asForm()->post($url, [
            'target' => $to,
            'message' => $message,
        ]);

        return $response->json(); // atau ->body() jika ingin raw response
    }
}
