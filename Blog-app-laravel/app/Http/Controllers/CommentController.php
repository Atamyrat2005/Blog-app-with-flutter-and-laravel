<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index ($id): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        $post = Post::find($id);

        if (!$post)
        {
            return response([
                'message' => 'Post not found'
            ], 403);
        }
        return response([
            'comments' => $post->comments()->with('user:id,name,image')->get()
        ], 200);
    }

    public function store (Request $request, $id): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        $post = Post::find($id);

        if (!$post)
        {
            return response([
                'message' => 'Post not found'
            ], 403);
        }


        $request->validate([
            'comment' => ['required', 'string'],
        ]);

//        IMAGE

        Comment::create([
            'comment' => $request->comment,
            'post_id' => $id,
            'user_id' => auth()->user()->id,
        ]);

        return response([
            'message' => 'Comment created'
        ], 200);
    }

    // update a comment
    public function update(Request $request, $id): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            return response([
                'message' => 'Comment not found'
            ], 403);
        }

        if($comment->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }

        //validate fields
        $request->validate([
            'comment' => ['required', 'string'],
        ]);

        $comment->update([
            'comment' => $request->comment
        ]);

        return response([
            'message' => 'Comment updated'
        ], 200);
    }

    // delete a comment
    public function destroy($id): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $comment = Comment::find($id);

        if(!$comment)
        {
            return response([
                'message' => 'Comment not found'
            ], 403);
        }

        if($comment->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }

        $comment->delete();

        return response([
            'message' => 'Comment deleted'
        ], 200);
    }
}
