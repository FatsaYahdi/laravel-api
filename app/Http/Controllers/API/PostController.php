<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /*
     * Display a listing of the resource.
     *
     *
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json([
            'posts' => $posts,
        ]);
    }

    /*
     * Store a newly created resource in storage.
     *
     *
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|min:3|unique:posts,title',
                'content' => 'required|string|max:255'
            ],[
                'title.required' => 'Title harus di isi.',
                'title.string' => 'Title harus berupa string.',
                'title.min' => 'Title harus memiliki 3 karakter atau lebih.',
                'title.unique' => 'Title Sudah di pakai.',

                'content.required' => 'Content harus di isi.',
                'content.string' => 'Content harus berupa string.',
                'content.max' => 'Content terlalu panjang. Maksimal 255 karakter.'
            ]);
            $post = Post::create(array_merge($validatedData, ['user_id' => auth()->user()->id]));
            return response()->json([
                'message' => 'Post Berhasil Di Buat.',
                'post' => $post
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
     * Display the specified resource.
     *
     *
     */
    public function show(Post $post)
    {
        $post->views++;
        $post->save();
        return response()->json([
            'post' => $post
        ]);
    }

    /*
     * Update the specified resource in storage.
     *
     *
     */
    public function update(Request $request, Post $post)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'nullable|string|min:3',
                'content' => 'nullable|string|max:255'
            ],[
                'title.string' => 'Title harus berupa string.',
                'title.min' => 'Title harus lebih dari 3 karakter atau lebih.',

                'content.string' => 'Content harus berupa string.',
                'content.max' => 'Content terlalu panjang. Maksimal 255 karakter.'
            ]);

            $post->title = $validatedData['title'] ?? $post->title;
            $post->content = $validatedData['content'] ?? $post->content;
            $post->user_id = $request->user()->id;
            $post->save();

            return response()->json([
                'message' => 'Post Berhasil Di Perbaharui.',
                'post' => $post
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->getMessages();
            return response()->json([
                'errors' => $errors,
            ], 422);
        }
    }

    /*
     * Remove the specified resource from storage.
     *
     *
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'message' => 'Post Berhasil Di Hapus.',
            'data' => $post
        ]);
    }
}