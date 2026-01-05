<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use App\Models\Bookmark;

class UserController extends Controller
{
 
    public function index()
    {
        return response()->json(User::all());
    }

    public function show($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

  
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'User updated',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted'
        ]);
    }

   public function posts($id)
{
    $user = User::with('likedPosts')->find($id);

    if (! $user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json($user->likedPosts);
}


   
    public function likes($id)
    {
        $user = User::with('likes.post')->find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user->likes);
    }

    public function bookmarks($id)
    {
        $user = User::with('bookmarks.post')->find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user->bookmarks);
    }
}
