<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed',
            'theme_mode' => 'string',
            'native_language' => 'string',
        ]);

        $user = User::create(
            [
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
                'theme_mode' => $fields['theme_mode'] ?? 'System',
                'native_language' => $fields['native_language'] ?? 'en',
            ]
        );

        $token = $user->createToken('laravel-sanctum-api')->plainTextToken;

        $response = [
            'token' => $token,
        ];

        /** @var Request $user */
        $response = array_merge($response, (new UserResource($user))->toArray($user));

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

        $response = array_merge($response, (new UserResource($user))->toArray($user));

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'message' => 'Logged out successfully'
        ];
    }

    public function removeAccount(Request $request)
    {
        $user = Auth::user();

        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password) || $fields['email'] != $user->email) {
            return response(['message' => 'Invalid e-mail or password', 'success' => false], 401);
        }

        $user->delete();

        return response(['message' => 'User deleted successfully.', 'success' => true]);
    }
}
