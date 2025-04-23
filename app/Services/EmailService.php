<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendViolationVerification;

class EmailService
{
    public function send($to, $test)
    {
        try {
            Mail::to($to)->send(new SendViolationVerification($test));
            return ['status' => 'sent'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
