<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'text',
        'user_id',
        'post_id',
        'parent_id',
        'created_by'
    ];
    protected $hidden = [
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->belongsTo(Post::class);
    }
    protected $casts = [
        'created_at' => 'datetime:d F Y',
        'updated_at' => 'datetime:d F Y'
    ];
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
