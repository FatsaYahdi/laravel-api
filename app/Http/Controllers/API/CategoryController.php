<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = Category::all();

        return response()->json([
            'status' => 'sukses',
            'message' => 'Menampilkan Semua Kategori',
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
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
            $data = Category::create(array_merge($validatedData, ['created_by' => auth()->user()->name]));
            return response()->json([
                'status' => 'sukses',
                'message' => 'Kategori Berhasil Di buat',
                'data' => $data
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'gagal',
                'errors' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'gagal',
                'error' => 'Terjadi kesalahan',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'string|min:3',
            'description' => 'nullable|string|min:3'
        ],[
            'name.string' => 'Nama harus string',
            'name.min' => 'Nama harus 3 karakter atau lebih',
            'description.string' => 'Deskripsi harus berupa string',
            'description.min' => 'Deskripsi harus 3 karakter atau lebih',
        ]);
        try {
            $category->name = $validatedData['name'] ?? $category->name;
            $category->description = $validatedData['description'] ?? $category->description;
            $category->save();

            return response()->json([
                'status' => 'sukses',
                'message' => 'Post Berhasil Di Perbaharui.',
                'data' => $category
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->json([
                'status' => 'sukses',
                'message' => 'Kategori Berhasil Di Hapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'gagal',
                'message' => $e
            ],422);
        }
    }
    public function list() {
        $data = Category::where('created_by', auth()->user()->name)->get();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Menampilkan Kategori Yang Di Buat',
            'data' => $data,
        ]);
    }
}
