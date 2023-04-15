<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'created_by'
    ];
    protected $casts = [
        'created_at' => 'datetime:d m Y',
        'updated_at' => 'datetime:d m Y'
    ];
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Post::class,'post_categories');
    }
}
