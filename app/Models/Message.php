<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'is_public',
        'content',
        'user_id',
        'room_id',
    ];

    protected function casts()
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    protected function username()
    {
        return $this->belongsTo(User::class, 'user_id');

    }
}
