<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;


class UserController extends Controller
{
    
    public function index()
    {
        $user = User::all();

        return UserResource::collection($user);
    }

    public function show($id)
    {
        $user = User::find($id);

        return new UserResource($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'nip' => 'required|numeric',
        ]);

        //create secret key for google2fa
        $google2fa = new Google2FA();
        $google2fa_secret = $google2fa->generateSecretKey();

        //merge secret key to request
        $request->merge(['google2fa_secret' => $google2fa_secret]);

        $user = User::create($request->all());

        // return new UserResource($user);
        return response()->json([
            'message' => 'User created successfully',
            'user' => new UserResource($user)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'nip' => 'required|numeric',
        ]);

        $user = User::find($id);
        $user->update($request->all());

        // return new UserResource($user);
        return response()->json([
            'message' => 'User updated successfully',
            'user' => new UserResource($user)
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
