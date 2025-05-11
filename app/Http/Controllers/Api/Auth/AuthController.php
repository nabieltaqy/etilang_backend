<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
     
        //delete all existing user token
        $user->tokens()->delete();

         $token = $user->createToken('auth_token')->plainTextToken;

        if ($user->is_2fa_enabled==true) {
            // if 2fa enabled, check if user has 2fa secret
        return response()->json(
            [
                'message' => 'Enter OTP for 2FA',
                'token' => $token,
            ]
        );
        } else {
            // if 2fa not enabled, create token and return
           
            return response()->json(
                [
                    'message' => 'You Need to Register 2FA',
                    'token' => $token,
                ]
            );
        }
        //return token for user || need argument for token name
        
    }

    function logout(Request $request)
    {

        // use this for Revoke all tokens...||delete active tokens for user
        // $user->tokens()->delete();

        //delete current login token for user (not all user tokens)
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(
            [
                'message' => 'Logout Succesful',
            ]
        );
    }
}
