<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create(
            [
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]
        );

        $token = $user->createToken('laravel-sanctum-api')->plainTextToken;

        $response = [
            'token' => $token,
        ];

        $response =  array_merge($response, (new UserResource($user))->toArray($user));

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::whereEmail($fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Invalid e-mail or password'], 401);
        }

        $token = $user->createToken('laravel-sanctum-api')->plainTextToken;

        $response = [
            'token' => $token,
        ];

        $response =  array_merge($response, (new UserResource($user))->toArray($user));

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'message' => 'Logged out successfully'
        ];
    }
}
