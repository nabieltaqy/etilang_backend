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

        //return token for user || need argument for token name
        return response()->json(
            [
                'message' => 'Enter OTP for 2FA',
                // 'user' => $user,
                'token' => $user->createToken('temp_token')->plainTextToken
            ]
        );
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
