<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request) {
        try {
            $credential = $request->only('email','password');

            if (empty($credential['email']) && empty($credential['password'])) {
                throw new \Exception('Email dan Password harus di isi.');
            }

            if (empty($credential['email'])) {
                throw new \Exception('Email harus di isi.');
            }

            if (empty($credential['password'])) {
                throw new \Exception('Password harus di isi.');
            }

            if(auth()->attempt($credential)) {
                $user = $request->user();
                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'status' => 'sukses',
                    'message' => 'Berhasil Login',
                    'token' => $token,
                ]);
            } else {
                throw new \Exception("Invalid Credentials.");
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'gagal',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Berhasil Logout.',
        ]);
    }
}
