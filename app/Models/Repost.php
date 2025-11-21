<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repost extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
    ];

    /**
     * Get the user that made the repost.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that was reposted.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
