<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SMS8Service;

class SMSController extends Controller
{
    protected $smsService;

    public function __construct(SMS8Service $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendSMS(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = $this->smsService->sendSMS($request->to, $request->message);

        return response()->json($response);
    }
}
