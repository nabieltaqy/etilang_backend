<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FonnteService;

class WhatsappController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    public function sendWhatsApp(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = $this->fonnteService->sendWhatsapp($request->to, $request->message);

        return response()->json($response);
    }
}
