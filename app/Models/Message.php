<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

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

    // function setContetAttribute
    protected function setContentAttribute($value)
    {
        $this->attributes['content'] = Crypt::encryptString($value);
    }

    public function getContentAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    protected function username()
    {
        return $this->belongsTo(User::class, 'user_id');

    }
}
