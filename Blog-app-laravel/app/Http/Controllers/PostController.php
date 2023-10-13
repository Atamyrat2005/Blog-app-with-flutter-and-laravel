<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')
                ->with('user:id,name,image')
                ->withCount('comments', 'likes')
                ->with('likes', function($like){
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'post_id')->get();
                })
                ->get()
        ], 200);
    }

    public function show($id): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return response([
            'post' => Post::where('id', $id)
                ->withCount('comments', 'likes')
                ->get()
        ], 200);
    }

    public function store(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $attrs = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $image = $this->saveImage($request->image, 'posts');

        $post = Post::create([
            'body' =>  $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

//        IMAGE

        return response([
            'message' => 'Post created',
            'post' => $post
        ], 200);
    }

    public function update(Request $request, $id): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        $post = Post::find($id);

        if (!$post)
        {
            return response([
                'message' => 'Post not found'
            ], 403);
        }

        if ($post->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }

        //validate fields
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' =>  $attrs['body']
        ]);

        // for now skip for post image

        return response([
            'message' => 'Post updated.',
            'post' => $post
        ], 200);
    }

    public function destroy($id)
    {

        $post = Post::find($id);

        if (!$post)
        {
            return response([
                'message' => 'Post not found'
            ], 403);
        }

        if ($post->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied'
            ], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Post deleted',
            'post' => $post
        ], 200);
    }
}
