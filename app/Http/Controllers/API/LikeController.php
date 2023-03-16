<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function like(Request $request, $post)
    {
        $like = Like::where('post_id', $post)->where('user_id', auth()->user()->id)->first();
        if ($like) {
            $like->delete();
            return response()->json([
                'status' => 'sukses',
                'message' => 'Unlike!'
            ]);
        } else {
            Like::create([
                'post_id' => $post,
                'user_id' => auth()->user()->id
            ]);
            return response()->json([
                'status' => 'sukses',
                'message' => 'Like!'
            ]);
        }
    }
}