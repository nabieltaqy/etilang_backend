<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

use GuzzleHttp\Client;

class SMS8Service {
    protected $apiKey;
    protected $devices;

    public function __construct()
    {
        $this->apiKey = env('SMS8_API_KEY'); // from env
        $this->devices = env('SMS8_DEVICES'); // Default device
    }

    public function sendSMS($to, $message)
    {
        $url = "https://app.sms8.io/services/send.php";

        $response = Http::get($url, [
            'key' => $this->apiKey,
            'number' => $to,
            'message' => $message,
            'devices' => $this->devices,
            'type' => 'sms',
            'prioritize' => 0
        ]);

        return $response->json();
    }
}
