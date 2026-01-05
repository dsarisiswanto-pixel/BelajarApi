<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Like;
use App\Models\Bookmark;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
   
    public function indexposts()
    {
        $posts = Post::with('category')
            ->where('status', 'published')
            ->paginate(5);

        return new PostResource(true, 'List Data Posts', $posts);
    }

   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'image'       => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'title'       => 'required',
            'content'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'user_id'     => $request->user()->id,
            'category_id' => $request->category_id,
            'image'       => $image->hashName(),
            'title'       => $request->title,
            'content'     => $request->input('content'),
            'status'      => $request->input('status', 'published'),
        ]);

        return new PostResource(true, 'Post berhasil ditambahkan', $post);
    }


    public function show($id)
    {
        $post = Post::with('category')->find($id);

        if (! $post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        return new PostResource(true, 'Detail Post', $post);
    }

    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required',
            'content'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Post::find($id);
        if (! $post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $post->update([
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'content'     => $request->input('content')
        ]);

        return new PostResource(true, 'Post berhasil diupdate', $post);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if (! $post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        Storage::delete('public/posts/' . $post->image);
        $post->delete();

        return response()->json(['message' => 'Post berhasil dihapus']);
    }

   
    public function draft()
    {
        $posts = Post::with('category')
            ->where('status', 'draft')
            ->paginate(5);

        return new PostResource(true, 'List Post Draft', $posts);
    }


    public function published()
    {
        $posts = Post::with('category')
            ->where('status', 'published')
            ->paginate(5);

        return new PostResource(true, 'List Post Published', $posts);
    }

   
    public function popular()
    {
        return response()->json([
            'data' => Post::with('category')->get()
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->q;

        return response()->json([
            'data' => Post::with('category')
                ->where('title', 'like', "%$q%")
                ->orWhere('content', 'like', "%$q%")
                ->get()
        ]);
    }


    public function myPosts(Request $request)
    {
        $posts = Post::with('category')
            ->where('id', $request->user()->id)
            ->paginate(5);

        return new PostResource(true, 'My Posts', $posts);
    }

  
    public function count()
    {
        return response()->json([
            'total'     => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'draft'     => Post::where('status', 'draft')->count(),
        ]);
    }

    public function publish($id)
    {
        $post = Post::find($id);
        if (! $post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $post->update(['status' => 'published']);

        return response()->json(['message' => 'Post berhasil dipublish']);
    }

    
    public function unpublish($id)
    {
        $post = Post::find($id);
        if (! $post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        $post->update(['status' => 'draft']);

        return response()->json(['message' => 'Post berhasil di-unpublish']);
    }

  
    public function likes($id)
    {
        $post = Post::withCount('likes')->find($id);

        if (! $post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }

        return response()->json([
            'post_id' => $post->id,
            'likes'   => $post->likes_count
        ]);
    }


    public function like($id, Request $request)
    {
        $user = $request->user();

        if (Like::where('user_id', $user->id)->where('post_id', $id)->exists()) {
            return response()->json(['message' => 'Post sudah di-like'], 409);
        }

        Like::create([
            'user_id' => $user->id,
            'post_id' => $id,
        ]);

        return response()->json(['message' => 'Post berhasil di-like']);
    }

    public function unlike($id, Request $request)
    {
        $request->user()
            ->likes()
            ->where('post_id', $id)
            ->delete();

        return response()->json(['message' => 'Post berhasil di-unlike']);
    }

    public function bookmark($id, Request $request)
    {
        $user = $request->user();

        if (Bookmark::where('user_id', $user->id)->where('post_id', $id)->exists()) {
            return response()->json(['message' => 'Post sudah di-bookmark'], 409);
        }

        Bookmark::create([
            'user_id' => $user->id,
            'post_id' => $id,
        ]);

        return response()->json(['message' => 'Post berhasil di-bookmark']);
    }

    public function unbookmark($id, Request $request)
    {
        $request->user()
            ->bookmarks()
            ->where('post_id', $id)
            ->delete();

        return response()->json(['message' => 'Bookmark berhasil dihapus']);
    }

    public function myBookmarks(Request $request)
{
    $bookmarks = Bookmark::with('post.category')
        ->where('user_id', $request->user()->id)
        ->paginate(5);

    return response()->json([
        'message' => 'My Bookmarked Posts',
        'data' => $bookmarks
    ]);
}

}
