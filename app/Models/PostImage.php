<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PostImage extends Model
{
    protected $primaryKey = "post_id";
        
    public $fillable = [
        'path',
        'post_id'
    ];

    public function post() {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
