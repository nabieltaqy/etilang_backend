<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function sendWhatsAppMessage($to, $message)
    {
        return $this->twilio->messages->create(
            "whatsapp:" . $to,
            [
                "from" => env('TWILIO_WHATSAPP_FROM'),
                "body" => $message
            ]
        );
    }
}
