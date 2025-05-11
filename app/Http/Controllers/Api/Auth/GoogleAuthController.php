<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use App\Http\Resources\UserResource;

//qr code generator
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class GoogleAuthController extends Controller
{
    public function show2faRegistration()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();
        //generate QR code
        $QR_Image = $google2fa->getQRCodeUrl(
            'Sistem E-Tilang',
            $user['email'],
            $user['google2fa_secret'],
        );

        //Make QR code image from URL OTPAuth
        $qrCode = new QrCode($QR_Image);
        $writer = new PngWriter();

        // Generate the QR code and get it as a string
        // $qrCodeImage = $writer->write($qrCode);
        $result = $writer->write($qrCode);

        // Convert to base64 to display in the view
        // $qrCodeImageBase64 = base64_encode($qrCodeImage->getString());

        // is 2fa enabled
        $user->is_2fa_enabled = true;
        $user->save();

        // return image png
        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);

        // return response()->json([
        //     'user' => $user,
        //     'message' => 'need to register 2fa',
        //     'QR_Image' => $qrCodeImageBase64,
        // ]);
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'otp' => 'required|min:6|max:6|',
        ]);

        $user = Auth::user();

        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user['google2fa_secret'], $request->otp);

        if ($valid) {
            $request->user()->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->is_2fa_enabled = true;
            $user->save();
            return response()->json([
                'is_2fa_verified' => true,
                // 'message' => '2fa enabled',
                'user' => new UserResource($user),
                'token' => $token,
            ], 200);
        }
        return response()->json([
            'is_2fa_verified' => false,
            'message' => 'OTP is invalid',
        ], 401);
    }

    public function disable2fa($id){
        $user = User::findOrFail($id);
        if ($user) {
            $user->is_2fa_enabled = false;
            $user->save();
        }

        return response()->json([
            'message' => '2fa disabled',
        ], 200);
    }
}
