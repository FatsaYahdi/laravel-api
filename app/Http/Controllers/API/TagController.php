<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();

        $response = [
            'status' => 'sukses',
            'message' => 'Menampilkan Semua Tag',
            'data' => $tags
        ];

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:3',
            'description' => 'nullable|string|min:3'
        ],[
            'name.required' => 'Nama harus di isi',
            'name.string' => 'Nama harus string',
            'name.min' => 'Nama harus 3 karakter atau lebih',
            'description.string' => 'Deskripsi harus berupa string',
            'description.min' => 'Deskripsi harus 3 karakter atau lebih',
        ]);
        
        try {
            $tag = Tag::create(array_merge($validatedData, ['created_by' => auth()->user()->name]));
            return response()->json([
                'status' => 'sukses',
                'message' => 'Tag Berhasil Di buat',
                'data' => $tag
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'gagal',
                'error' => 'Terjadi kesalahan',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, Tag $tag)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:3',
            'description' => 'nullable|string|min:3'
        ],[
            'name.required' => 'Nama harus di isi',
            'name.string' => 'Nama harus string',
            'name.min' => 'Nama harus 3 karakter atau lebih',
            'description.string' => 'Deskripsi harus berupa string',
            'description.min' => 'Deskripsi harus 3 karakter atau lebih',
        ]);
        try {
            $tag->name = $validatedData['name'] ?? $tag->name;
            $tag->description = $validatedData['description'] ?? $tag->description;
            $tag->save();

            return response()->json([
                'status' => 'sukses',
                'message' => 'Post Berhasil Di Perbaharui.',
                'data' => $tag
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
                'error' => 'Terjadi kesalahan',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json([
            'status' => 'sukses',
            'message' => 'Post Berhasil Di Hapus.',
        ]);
    }
    public function list() {
        $tags = Tag::where('created_by', auth()->user()->name)->get();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Menampilkan Tag Yang Di Buat',
            'data' => $tags,
        ]);
    }
}
