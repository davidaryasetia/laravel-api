<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(15);
        return new PostResource(true, 'List Data Posts', $posts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048', 
            'title' => 'required', 
            'content' => 'required',
        ]);

        // check jika validator fails
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        // upload image 
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image' => $image->hashName(), 
            'title' => $request->title, 
            'content' => $request->content, 
        ]);

        return new PostResource(true, 'Data Post Berhasil Ditambahkan', $post);
    }

    public function show(Post $post)
    {
        return new PostResource(true, 'Data Post Ditemukan', $post);
    }

    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required', 
            'content' => 'required',
        ]);

        // check jika validasi gagal
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        // check jika image tidak kosong
        if($request->hasFile('image')){

            // upload image 
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // delete image lama 
            Storage::delete('public/posts/'.$post->image);

            $post->update([
                'image' => $image->hashName(), 
                'title' => $request->title, 
                'content' => $request->content, 
            ]);

        } else {
            // update tanpa image
            $post->update([
                'title' => $request->title, 
                'content' => $request->content,
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil Diubah', $post);
    }

    public function destroy(Post $post)
    {
        // hapus image 
        Storage::delete('public/posts/'. $post->image);
        
        $post->delete();

        return new PostResource(true, 'Data Post Berhasil Dihapus', null);

    }
}
