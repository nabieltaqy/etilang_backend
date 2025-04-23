<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FonnteService;
use App\Services\SMS8Service;
use App\Services\EmailService;

class SendAllController extends Controller
{
    protected $fonnte;
    protected $sms;
    protected $email;

    public function __construct(FonnteService $fonnte, SMS8Service $sms, EmailService $email)
    {
        $this->fonnte = $fonnte;
        $this->sms = $sms;
        $this->email = $email;
    }

    public function sendAll(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $whatsappResult = $this->fonnte->sendWhatsapp($request->to, $request->message);
        $smsResult = $this->sms->sendSMS($request->to, $request->message);
        $emailResult = $this->email->send($request->email, $request->message);
        return response()->json([
            'whatsapp' => [
                'status' => $whatsappResult['status'] ?? false,
                'message' => $whatsappResult['detail'] ?? 'unknown',
            ],
            'sms' => [
                'status' => $smsResult['success'] ?? false,
                'message' => $smsResult['data']['messages'][0]['status'] ?? 'unknown',
            ],
            'email' => [
                'status' => $emailResult['status'] === 'sent',
                'message' => $emailResult['status'] ?? 'failed',
            ]
        ]);
    }
}
