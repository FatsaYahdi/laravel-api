<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Show Profile',
            'data' => $user
        ]);
    }

    public function update(Request $request) {
        $validatedData = $request->validate([
                'name' => 'nullable|string|min:3|max:255',
                'email' => 'nullable|string|email',
            ],[
                // nama
                'name.string' => 'Nama Harus Berupa String.',
                'name.min' => 'Nama Harus 3 karakter atau lebih.',
                'name.max' => 'Nama tidak bisa lebih dari 255 karakter.',
                // email
                'email.string' => 'Email Harus berupa String.',
                'email.email' => 'Email Harus berupa email valid.',
        ]);
        try {
            $user = $request->user();

            $user->name = $validatedData['name'] ?? $user->name;
            $user->email = $validatedData['email'] ?? $user->email;
            $user->save();

            return response()->json([
                'status' => 'sukses',
                'message' => 'Data Berhasil di Update',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->first();
            return response()->json([
                'status' => 'gagal',
                'errors' => $errors,
            ], 422);
        } 
    }
}
