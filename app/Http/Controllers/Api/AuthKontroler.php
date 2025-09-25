<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthKontroler extends Controller
{
    /** POST /api/v1/auth/register */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|max:255|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    /** POST /api/v1/auth/login */
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            
            return response()->json(['message' => 'Pogrešan email ili lozinka.'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    /** GET /api/v1/auth/me (auth:sanctum) */
    public function me(Request $request)
    {
       
        return response()->json([
            'data' => $request->user(),
        ]);
    }

    /** POST /api/v1/auth/logout (auth:sanctum) */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        
        return response()->json(['message' => 'uspesna odjava']);
    }
}
