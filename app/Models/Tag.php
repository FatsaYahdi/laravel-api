<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
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
}
