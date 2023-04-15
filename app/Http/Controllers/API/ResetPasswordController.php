<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Support\Facades\Validator;


class ResetPasswordController extends Controller
{
    public function token(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ],[
            'email.required' => 'Email harus di isi.',
            'email.email' => 'Email harus berupa valid email.'
        ]);
        $status = Password::sendResetLink($request->only('email'));
        if ($status == Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => 'sukses',
                'message' => 'Token telah di kirim ke email anda.'
            ]);
        }
        throw ValidationException::withMessages([
            'email' => 'Tidak bisa menemukan user dengan email tersebut.'
        ]);
    }
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'token.required' => 'Token harus di isi.',
            'email.required' => 'Email harus di isi.',
            'email.email' => 'Email harus berupa valid email.',
            'password.required' => 'Password harus di isi.'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
            'status' => 'gagal',
                'message' => $errors->first()
            ], 422);
        }

        $status = Password::reset(
            $request->only('email','password','token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                $user->tokens()->delete();
                event(new PasswordReset($user));
            }
        );
        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 'sukses',
                'message' => 'Password Berhasil Di Update.'
            ]);
        } else if ($status == Password::INVALID_TOKEN) {
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token tidak valid atau kadaluarsa.'
            ], 422);
        }
        return response()->json([
            'status' => 'gagal',
            'message' => "Kami tidak bisa menemukan user tersebut"
        ],500);
    }
}
