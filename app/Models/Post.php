<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Bookmark;
use App\Models\Like;


class Post extends Model
{
    use HasFactory;

    protected $fillable = [
    'category_id',
    'image',
    'title',
    'content',
    'status'
    
];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 🔹 RELASI BOOKMARK
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    // 🔹 RELASI LIKE
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/posts/' . $image),
        );
    }
}
