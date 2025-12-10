<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validData = $request->validate([
            'name' => 'sometimes|string|max:255|min:3',
            'email' => ['sometimes', 'string', Rule::unique('users')],
            'password' => 'sometimes|confirmed|string|min:8',
        ]);

        $user = User::create($validData);
        $token = auth('api')->login($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        if (! ($token = auth('api')->attempt($credentials))) {
            return response()->json([
                'msg' => 'Wrong Credentials',
            ], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function me()
    {
        return auth('api')->user();
    }

    public function refresh()
    {
        return response()->json([
            'access_token' => auth('api')->refresh(),
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'msg' => 'User logged out successfully',
        ], 200);
    }
}
