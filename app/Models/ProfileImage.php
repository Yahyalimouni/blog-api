<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileImage extends Model
{
    protected $primaryKey = 'user_id';

    // Fillable columns
    protected $fillable = [
        'path'
    ];

    protected function casts(): array
    {
        return [
            'path' => 'string',
        ];
    }

    // An image belongs to one and only user
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
