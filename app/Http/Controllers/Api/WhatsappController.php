<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TwilioService;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function sendMessage(Request $request)
    {

        $to = $request->to;
        $message = $request->message;
        // $to = "+6285156644369";
        // $message = "Halo, ini pesan dari Laravel menggunakan Twilio! 🚀";

        $this->twilioService->sendWhatsAppMessage($to, $message);

        return response()->json(["message" => "WhatsApp sent!"]);
    }
}
