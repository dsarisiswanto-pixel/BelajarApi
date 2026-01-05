<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Str;

class CategoryController extends Controller
{


    public function indexcategory()
    {
        return response()->json(Category::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'slug' => 'nullable|unique:categories,slug',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => $request->slug
                ? Str::slug($request->slug)
                : Str::slug($request->name),
        ]);

        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|unique:categories,name,' . $id,
            'slug' => 'sometimes|required|unique:categories,slug,' . $id,
        ]);

        $data = [];

        if ($request->has('name')) {
            $data['name'] = $request->name;
        }

        if ($request->has('slug')) {
            $data['slug'] = Str::slug($request->slug);
        }

        $category->update($data);

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }


    public function posts($id)
    {
        $category = Category::with('posts')->find($id);

        if (! $category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json([
            'category' => $category->name,
            'posts' => $category->posts
        ]);
    }



    public function popular()
    {
        $categories = Category::withCount('posts')
            ->orderByDesc('posts_count')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function empty()
    {
        $categories = Category::doesntHave('posts')->get();

        return response()->json(['data' => $categories]);
    }

    public function attachPost(Request $request, $id)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $post = Post::find($request->post_id);
        $post->category_id = $id;
        $post->save();

        return response()->json([
            'message' => 'Post berhasil dipindahkan ke category'
        ]);
    }


    public function count()
    {
        return response()->json([
            'total' => Category::count()
        ]);
    }

    public function postsCount($id)
    {
        $category = Category::withCount('posts')->find($id);

        if (! $category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'category_id' => $category->id,
            'category'    => $category->name,
            'posts_count' => $category->posts_count
        ]);
    }
}
