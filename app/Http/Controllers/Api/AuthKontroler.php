<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthKontroler extends Controller
{
        private function payload(Request $request): array
    {
        
        $data = $request->all();
        if (empty($data)) {
            $raw = json_decode($request->getContent() ?? '', true);
            if (is_array($raw)) {
                $data = $raw;
            }
        }
        return $data;
    }

    /** POST /api/v1/auth/register */
    public function register(Request $request)
    {
        $data = $this->payload($request);

        $v = Validator::make($data, [
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8'],
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => $v->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Registracija uspešna.',
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    /** POST /api/v1/auth/login */
    public function login(Request $request)
    {
        $data = $this->payload($request);

        $v = Validator::make($data, [
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => $v->errors(),
            ], 422);
        }

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => ['email' => ['Pogrešan email ili lozinka.']],
            ], 422);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Prijava uspešna.',
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    /** GET /api/v1/auth/me (auth:sanctum) */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /** POST /api/v1/auth/logout (auth:sanctum) */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Uspešna odjava.']);
    }
}
