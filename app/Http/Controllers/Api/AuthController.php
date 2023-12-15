<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $loginData = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $user = \App\Models\User::where('email', $request->email)->first();

    //     if (!$user) {
    //         return response([
    //             'message' => ['Email not found'],
    //         ], 404);
    //     }

    //     if (!Hash::check($request->password, $user->password)) {
    //         return response([
    //             'message' => ['Password is wrong'],
    //         ], 404);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response([
    //         'user' => $user,
    //         'token' => $token,
    //     ], 200);
    // }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $loginData['email'])->first();
        if (!$user) {
            return response([
                'message' => 'email tidak ada',
            ], 404);
        }
        if (!Hash::check($loginData['password'], $user->password)) {
            return response([
                'message' => 'password salah',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response([
            'user' => $user,
            'token' => $token,
        ], 200);
    }
}