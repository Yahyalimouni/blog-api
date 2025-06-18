<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function parent() 
    {
        return $this->belongsTo(self::class, "parent_id");
    }

    public function replies()
    {
        return $this->hasMany(self::class, "parent_id");
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'comment_user');
    }
}
