<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($postId)
    {
        $data = Comment::whereNull('parent_id')->where('post_id', $postId)->with('user:id,name')->with('replies.user:id,name')->with('replies.replies')->with('replies.replies.user:id,name')->get();
        $response = [
            'status' => 'sukses',
            'message' => 'Menampilkan Semua Komentar',
            'data' => $data
        ];

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $postId)
    {
        $this->authorize('create', Comment::class);
        try {
            $user = auth()->user();
            $request->validate([
                'text' => 'required|string|min:3|max:255',
                'parent_id' => 'nullable|exists:comments,id',
            ],[
                'text.required' => 'Text harus di isi.',
                'text.string' => 'Text harus berupa string.',
                'text.min' => 'Text harus memiliki 3 karakter atau lebih.',
                'text.max' => 'Text harus harus kurang dari 255.',
            ]);
            $parent = $request->input('parent_id');
            $data = Comment::create([
                'text' => $request->input('text'),
                'user_id' => $user->id,
                'post_id' => $postId,
                'created_by' => auth()->user()->name,
                'parent_id' => $parent
            ]);

            return response()->json([
                'status' => 'sukses',
                'message' => 'Komentar telah ditambahkan.',
                'data' => $data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // $this->authorize('update', $id);
        try {
            $comment = Comment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'text' => 'nullable|string|min:3|max:255',
            ],[
                'text.string' => 'Text harus berupa string.',
                'text.min' => 'Text harus memiliki 3 karakter atau lebih.',
                'text.max' => 'Text harus harus kurang dari 255.',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $text = $request->input('text');

            if (!is_null($text) && !empty($text)) {
                $comment->text = $text;
                $comment->save();
            }

            return response()->json([
                'status' => 'sukses',
                'message' => 'Komentar berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'gagal',
                'error' => $e->getMessage()
            ], 500);
        }
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Komentar berhasil di hapus.',
        ]);
    }

    public function show($id)
    {
        $comment = Comment::where('id',$id)->where('parent_id', null)->get();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Menampilkan Komentar Dengan Id',
            'data' => $comment
        ]);
    }
}
