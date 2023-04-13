<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $posts = Post::with('user:id,name')->with('tags:id,name')->paginate(9);
        $postsData = $posts->items();
        $nextPageUrl = $posts->nextPageUrl();
        $prevPageUrl = $posts->previousPageUrl();

        $response = [
            'status' => 'sukses',
            'message' => 'Menampilkan Semua Postingan',
            'posts' => $postsData
        ];

        if (!is_null($nextPageUrl)) {
            $response['selanjutnya'] = $nextPageUrl;
        }

        if (!is_null($prevPageUrl)) {
            $response['sebelumnya'] = $prevPageUrl;
        }

        return response()->json($response);
    }

    /*
     * Store a newly created resource in storage.
     *
     *
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|min:3|unique:posts,title',
            'content' => 'required|string|max:255',
            'image' => 'image|nullable'
        ],[
            'title.required' => 'Title harus di isi.',
            'title.string' => 'Title harus berupa string.',
            'title.min' => 'Title harus memiliki 3 karakter atau lebih.',
            'title.unique' => 'Title Sudah di pakai.',

            'content.required' => 'Content harus di isi.',
            'content.string' => 'Content harus berupa string.',
            'content.max' => 'Content terlalu panjang. Maksimal 255 karakter.'
        ]);
        try {
            $data = $validatedData;
            if($request->file('image')) {
                $fileName = $request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('public/images/posts', $fileName);
                $data['image'] = $fileName;
            }
            $post = Post::create(array_merge($data, ['user_id' => auth()->user()->id]));
            $tag = $request->input('tag_id');
            $post->tags()->attach($tag);
            return response()->json([
                'status' => 'sukses',
                'message' => 'Post Berhasil Di Buat.',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->first();
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

    /*
     * Display the specified resource.
     *
     *
     */
    public function show(Post $post)
    {
        $comments = Comment::where('post_id', $post->id)->with('user:id,name')->with('replies.user:id,name')->get();
        $posts = Post::with('tags:id,name')->with('user:id,name')->find($post->id);
        $post->views++;
        $post->save();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Showing Post',
            'post' => $posts,
            'comment' => $comments
        ]);
    }
    
    /*
     * Update the specified resource in storage.
     *
     *
     */
    public function update(Request $request, $post)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|min:3',
            'content' => 'nullable|string|max:255',
            'image' => 'nullable|image'
        ],[
            'title.string' => 'Title harus berupa string.',
            'title.min' => 'Title harus lebih dari 3 karakter atau lebih.',

            'content.string' => 'Content harus berupa string.',
            'content.max' => 'Content terlalu panjang. Maksimal 255 karakter.',
            'image.image' => 'Image harus berupa gambar'
        ]);
        try {
            $posts = Post::findOrFail($post);
            if ($posts->user_id != auth()->user()->id) {
                return response([
                    'status' => 'gagal',
                    'message' => 'Anda tidak bisa meng-update postingan orang lain'
                ], 403);
            }
            $posts->title = $validatedData['title'] ?? $posts->title;
            $posts->content = $validatedData['content'] ?? $posts->content;
            if($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $image->storeAs('public/images/posts',$imageName);
                if ($posts->image !== null) {
                    Storage::delete('public/images/posts/' . $posts->image);
                }
                $validatedData['image'] = $imageName;
            } else {
                $validatedData['image'] = $posts->image;
            }
            $posts->image = $validatedData['image'] ?? $posts->image;
            $posts->update();

            $tagId = $request->input('tag_id');
            $posts->tags()->sync($tagId);

            return response()->json([
                'status' => 'sukses',
                'message' => 'Post Berhasil Di Perbaharui.',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->getMessages();
            return response()->json([
                'status' => 'gagal',
                'errors' => $errors,
            ], 422);
        }
    }

    /*
     * Remove the specified resource from storage.
     *
     *
     */
    public function destroy($post)
    {
        $posts = Post::findOrFail($post);
        if ($posts->user_id != auth()->user()->id) {
            return response([
                'status' => 'gagal',
                'message' => 'Anda tidak bisa menghapus postingan orang lain'
            ], 403);
        }
        if ($posts->image != null) {
            Storage::delete('public/images/posts/'. $posts->image);
        }
        $posts->tags()->detach();
        $posts->delete();

        return response()->json([
            'status' => 'sukses',
            'message' => 'Post Berhasil Di Hapus.',
        ],200);
    }

    public function views($postId) {
        $post = Post::findOrFail($postId);
        $view = $post->views;
        return response()->json([
            'viewer' => $view,
        ]);
    }

    public function list() {
        $post = Post::where('user_id', auth()->user()->id)->get();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Menampilkan Post Yang Di Buat',
            'data' => $post,
        ]);
    }

    public function taglist(Tag $id)
    {
        $posts = $id->tags()->get();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Menampilkan Post Dengan Tag Tertentu.',
            'data' => $posts
        ]);
    }
}