<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class DashboardController extends Controller
{

    public function stats()
    {
        return response()->json([
            'total_posts'      => Post::count(),
            'total_users'      => User::count(),
            'total_categories' => Category::count(),
            'latest_posts'     => Post::take(5)
                                        ->get(['id', 'title', 'created_at']),
        ]);
    }

  
    public function postsCount()
    {
        return response()->json([
            'total_posts' => Post::count()
        ]);
    }

    public function usersCount()
    {
        return response()->json([
            'total_users' => User::count()
        ]);
    }

    public function categoriesCount()
    {
        return response()->json([
            'total_categories' => Category::count()
        ]);
    }

    public function activity()
    {
        return response()->json([
            'latest_posts' => Post::take(5)
                ->get(['id', 'title', 'created_at'])
        ]);
    }

     public function recentPosts()
    {
        return response()->json([
            'data' => Post::orderByDesc('created_at')
                ->take(5)
                ->get(['id', 'title', 'created_at'])
        ]);
    }

        public function topPosts()
    {
        return response()->json([
            'data' => Post::withCount('likes')
                ->orderByDesc('likes_count')
                ->take(5)
                ->get(['id', 'title', 'likes_count'])
        ]);
    }

      public function topCategories()
    {
        return response()->json([
            'data' => Category::withCount('posts')
                ->orderByDesc('posts_count')
                ->take(5)
                ->get(['id', 'name', 'posts_count'])
        ]);
    }
}
