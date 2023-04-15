<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = Bookmark::where('user_id',auth()->id())->with('post')->get();
        return response()->json([
            'status' => 'sukses',
            'message' => 'Menampilkan Semua Post Yang Di Simpan',
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Post $id)
    {
        try {
            $exist = Bookmark::where('user_id', auth()->id())->where('post_id',$id->id)->first();
            if ($exist) {
                return response()->json([
                    'status' => 'sukses',
                    'message' => "Postingan sudah ada di bookmark",
                ]);
            }
            Bookmark::create([
                'user_id' => auth()->id(),
                'post_id' => $id->id
            ]);

            return response()->json([
                'status' => 'sukses',
                'message' => "Postingan berhasil di simpan",
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'anu' => $th,
                'status' => 'gagal',
                'message' => 'Ada yang salah'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // 
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $bm = Bookmark::where('post_id', $id)->where('user_id', auth()->user()->id)->first();
        try {
            $bm->delete();
            return response()->json([
                'status' => 'sukses',
                'message' => 'Berhasil di hapus dari bookmark'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'gagal',
                'message' => $e
            ]);
        }
    }
}
