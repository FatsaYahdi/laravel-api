<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        $usersData = $users->items();
        $nextPageUrl = $users->nextPageUrl();
        $prevPageUrl = $users->previousPageUrl();

        $response = [
            'message' => 'Menampilkan Semua Pengguna',
            'data' => $usersData
        ];

        if (!is_null($nextPageUrl)) {
            $response['selanjutnya'] = $nextPageUrl;
        }

        if (!is_null($prevPageUrl)) {
            $response['sebelumnya'] = $prevPageUrl;
        }

        return response()->json($response);
    }

    public function show($id) {
        $user = User::findOrFail($id);
        return response()->json([
            'status' => 'sukses',
            'message' => 'Menampilkan Profile Pengguna',
            'data' => $user
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255|unique:users,name',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ],[
        // name
        'name.required' => 'Nama Harus di isi.',
        'name.string' => 'Nama Harus berupa string.',
        'name.min' => 'Nama Harus memiliki 3 karakter atau lebih.',
        'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
        'name.unique' => 'Nama sudah di pakai.',
        
        // email
        'email.required' => 'Email harus di isi.',
        'email.email' => 'Email harus berupa email.',
        'email.unique' => 'Email sudah di pakai.',

        // password
        'password.required' => 'Password harus di isi.',
        'password.min' => 'Password harus memiliki panjang 8 atau lebih.'
        ]);
        try {
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);
            User::create($data);

            return response()->json([
                'status' => 'sukses',
                'message' => 'Data berhasil di buat.',
            ],200);
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

    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();

            $validatedData = $request->validate([
                'name' => 'nullable|string|min:3|max:255|unique:users,name',
                'email' => 'nullable|string|email|unique:users,email,'.$user->id
            ],[
                // nama
                'name.string' => 'Nama Harus Berupa String.',
                'name.min' => 'Nama Harus 3 karakter atau lebih.',
                'name.max' => 'Nama tidak bisa lebih dari 255 karakter.',
                'name.unique' => 'Nama Sudah Di Pakai.',
                // email
                'email.string' => 'Email Harus berupa String.',
                'email.email' => 'Email Harus berupa email valid.',
                'email.unique' => 'Email sudah Di pakai.',
            ]);

            $user->name = $validatedData['name'] ?? $user->name;
            $user->email = $validatedData['email'] ?? $user->email;
            $user->save();

            return response()->json([
                'status' => 'sukses',
                'message' => 'Data berhasil di update',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->getMessages();
            return response()->json([
                'status' => 'gagal',
                'errors' => $errors,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'gagal',
                'Eror' => 'Eror'
            ], 500);
        }
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'status' => 'sukses',
            'message' => 'User berhasil di hapus.',
        ]);
    }
}
