<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id', 
            'comments_content' => 'required', 
        ]);
    
        $request['user_id'] = auth()->user()->id;

        $comment = Comment::create($request->all());

        return new CommentResource($comment->loadMissing(['comentator:id,username,firstname,lastname'])); 
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comments_content' => 'required', 
        ]);
        // dd($request->all());

        $comment = Comment::findOrFail($id);
        $comment->update($request->only('comments_content'));
        return new CommentResource($comment->loadMissing(['comentator:id,username']));

    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return new CommentResource($comment->loadMissing(['comentar:id,username']));
    }
}
