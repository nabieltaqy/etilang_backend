<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use App\Services\FonnteService;
use App\Services\SMS8Service;
use App\Services\EmailService;
use App\Models\Notification;
use App\Models\Ticket;


class NotificationController extends Controller
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

    public function sendAll($id)
    {
        //get phone number and email from ticket
        $ticket = Ticket::with(['vehicle'])->findOrFail($id);
        $vehicleInfo = $ticket->vehicle;
        $owner_email = $vehicleInfo->owner_email;
        $owner_phone = $vehicleInfo->owner_phone;
        $message = "You have a new ticket with id: $ticket->id. Please check" . env('APP_URL') . "for more details. \n Korlantas Polri";

        $whatsappResult = $this->fonnte->sendWhatsapp($owner_phone, $message);
        $smsResult = $this->sms->sendSMS($owner_phone, $message);
        $emailResult = $this->email->send($owner_email, $message);

        // crete history notification
        Notification::updateOrCreate([
            'type' => 'whatsapp',
            'ticket_id' => $ticket->id,
            'is_sent' => $whatsappResult['status'] ?? false,
        ])->touch();

        Notification::updateOrCreate([
            'type' => 'sms',
            'ticket_id' => $ticket->id,
            'is_sent' => $smsResult['success'] ?? false,
        ])->touch();

        Notification::updateOrCreate([
            'type' => 'email',
            'ticket_id' => $ticket->id,
            'is_sent' => $emailResult['status'] === 'sent',
        ])->touch();

        // return response()->json([
        //     'whatsapp' => [
        //         'status' => $whatsappResult['status'] ?? false,
        //         'message' => $whatsappResult['detail'] ?? 'error',
        //     ],
        //     'sms' => [
        //         'status' => $smsResult['success'] ?? false,
        //         'message' => isset($smsResult['data']['messages'][0]['status'])
        //             ? $smsResult['data']['messages'][0]['status']
        //             : ($smsResult['error']['message'] ?? 'unknown'),
        //     ],
        //     'email' => [
        //         'status' => $emailResult['status'] === 'sent',
        //         'message' => $emailResult['status'] ?? 'error',
        //         // 'message' => $emailResult, //for debug
        //     ]
        // ]);
        return NotificationResource::collection($ticket->notifications)->groupBy(function ($item) {
    return $item->type;
});
    }

    public function sendSMS($id)
    {
        //get phone number and email from ticket
        $ticket = Ticket::with(['vehicle'])->findOrFail($id);
        $vehicleInfo = $ticket->vehicle;
        $owner_phone = $vehicleInfo->owner_phone;
        $message = "You have a new ticket with id: $ticket->id. Please check" . env('APP_URL') . "for more details. \n Korlantas Polri";

        

        $smsResult = $this->sms->sendSMS($owner_phone, $message);

        // crete history notification
        Notification::updateOrCreate([
            'type' => 'sms',
            'ticket_id' => $ticket->id,
            'is_sent' => $smsResult['success'] ?? false,
        ])->touch();

        return response()->json([
            'sms' => [
                'status' => $smsResult['success'] ?? false,
                'message' => isset($smsResult['data']['messages'][0]['status'])
                    ? $smsResult['data']['messages'][0]['status']
                    : ($smsResult['error']['message'] ?? 'unknown'),
            ],
        ]);
    }

    public function sendWhatsApp($id)
    {
        //get phone number and email from ticket
        $ticket = Ticket::with(['vehicle'])->findOrFail($id);
        $vehicleInfo = $ticket->vehicle;
        $owner_phone = $vehicleInfo->owner_phone;
        $message = "You have a new ticket with id: $ticket->id. Please check" . env('APP_URL') . "for more details. \n Korlantas Polri";

        $whatsappResult = $this->fonnte->sendWhatsapp($owner_phone, $message);

        // crete history notification
        Notification::updateOrCreate([
            'type' => 'whatsapp',
            'ticket_id' => $ticket->id,
            'is_sent' => $whatsappResult['status'] ?? false,
        ])->touch();

        return response()->json([
            'whatsapp' => [
                'status' => $whatsappResult['status'] ?? false,
                'message' => $whatsappResult['detail'] ?? 'error',
            ],
        ]);
    }

    public function sendEmail($id)
    {
//get phone number and email from ticket
        $ticket = Ticket::with(['vehicle'])->findOrFail($id);
        $vehicleInfo = $ticket->vehicle;
        $owner_email = $vehicleInfo->owner_email;
        $message = "You have a new ticket with id: $ticket->id. Please check" . env('APP_URL') . "for more details. \n Korlantas Polri";

        $emailResult = $this->email->send($owner_email, $message);

        // crete history notification
        Notification::updateOrCreate([
            'type' => 'email',
            'ticket_id' => $ticket->id,
            'is_sent' => $emailResult['status'] === 'sent',
        ])->touch();

        return response()->json([
            'email' => [
                'status' => $emailResult['status'] === 'sent',
                'message' => $emailResult['status'] ?? 'error',
            ]
        ]);
    }

    public function appealVerification($id)
    {
        //get phone number and email from ticket
        $ticket = Ticket::with(['vehicle', 'vehicle'])->findOrFail($id);
        $vehicleInfo = $ticket->vehicle;
        $owner_email = $vehicleInfo->owner_email;
        $owner_phone = $vehicleInfo->owner_phone;
        $message = "Your appeal for ticket id: $ticket->id has been verified. Your Ticket has been canceled. \n Korlantas Polri";

        $whatsappResult = $this->fonnte->sendWhatsapp($owner_phone, $message);
        $smsResult = $this->sms->sendSMS($owner_phone, $message);
        $emailResult = $this->email->send($owner_email, $message);

        // crete history notification
        Notification::updateOrCreate([
            'type' => 'whatsapp',
            'ticket_id' => $ticket->id,
            'is_sent' => $whatsappResult['status'] ?? false,
        ])->touch();

        Notification::updateOrCreate([
            'type' => 'sms',
            'ticket_id' => $ticket->id,
            'is_sent' => $smsResult['success'] ?? false,
        ])->touch();

        Notification::updateOrCreate([
            'type' => 'email',
            'ticket_id' => $ticket->id,
            'is_sent' => $emailResult['status'] === 'sent',
        ])->touch();

        return response()->json([
            'whatsapp' => [
                'status' => $whatsappResult['status'] ?? false,
                'message' => $whatsappResult['detail'] ?? 'error',
            ],
            'sms' => [
                'status' => $smsResult['success'] ?? false,
                'message' => isset($smsResult['data']['messages'][0]['status'])
                    ? $smsResult['data']['messages'][0]['status']
                    : ($smsResult['error']['message'] ?? 'unknown'),
            ],
            'email' => [
                'status' => $emailResult['status'] === 'sent',
                'message' => $emailResult['status'] ?? 'error',
            ]
        ]);
    }
}
