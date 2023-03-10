<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8'
        ],[
            // name
            'name.required' => 'Nama harus di isi.',
            'name.string' => 'Nama harus berupa string.',
            'name.min' => 'Panjang Nama minimal 3 karakter.',
            'name.max' => 'Nama Tidak bisa lebih dari 255 karakter.',
            // email
            'email.required' => 'Email harus di isi.',
            'email.email' => 'Email harus berupa email.',
            'email.unique' => 'Email sudah dipakai.',
            'email.max' => 'Panjang Email Tidak bisa lebih dari 255 karakter.',
            // password
            'password.required' => 'Password Harus di isi.',
            'password.string' => 'Password Harus berupa string.',
            'password.min' => 'Password Harus 8 karakter atau lebih'
        ]);
        try {
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            if (Auth::loginUsingId($user->id)) {
                $token = $user->createToken('api-token')->plainTextToken;
                return response()->json([
                    'status' => 'sukses',
                    'message' => 'Registrasi Berhasil. Login Sukses.',
                    'token' => $token,
                ]);
            } else {
                return response()->json([
                    'status' => 'sukses',
                    'message' => 'Registrasi Berhasil. Login Gagal',
                ],200);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'gagal',
                'eror' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'gagal',
                'error' => 'Terjadi kesalahan',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
