<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;

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

         $token = $user->createToken('temp_token')->plainTextToken; // harusnya temp token

        if ($user->is_2fa_enabled==true) {
            $message = 'You Need to Verify 2FA';

        } else {
            $message = 'You Need to Register 2FA';
        }
        //return token for user || need argument for token name
                return response()->json(
            [
                'message' => $message,
                'is_2fa_enabled' => $user->is_2fa_enabled,
                'token' => $token,
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

    function getUserData(Request $request)
    {
        return response()->json(
            [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'is_2fa_enabled' => $request->user()->is_2fa_enabled,
                ],
            ]
        );
    }
}
