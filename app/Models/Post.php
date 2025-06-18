<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'content',
        'user_id'
    ];

    protected function casts(): array
    {
        return [
            'title' => 'string',
            'category_id' => 'string',
            'user_id' => 'integer',
            'username' => 'string'
        ];
    }

    // A post belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A post belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // A post has one image
    public function path()
    {
        return $this->hasOne(PostImage::class, 'post_id', 'id');
    }

    // A post has one to many images
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    // A post can be liked by many users
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'post_user');
    }
}
