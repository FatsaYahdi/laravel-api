<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at'
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:d F Y',
        'updated_at' => 'datetime:d F Y',
    ];

    public function posts()
    {
        return $this->belongsTo(Post::class);
    }
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $url = 'http://localhost:8000/password/reset?token=' . $token;
        $this->notify(new ResetPasswordNotification($url, $token));
    }
}
