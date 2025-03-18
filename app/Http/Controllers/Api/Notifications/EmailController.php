<?php

namespace App\Http\Controllers\Api\Notifications;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\SendViolationVerification;


class EmailController extends Controller
{
    public function send(Request $request) {
        $email = $request->email;
        $test = "12345";

        
        if (Mail::to($email)->send(new SendViolationVerification($test))){
            return response()->json([
                'message' => 'Email sent'
            ]);
        }

        return response()->json([
            'message' => 'Email failed to send'
        ]);
    }
}
