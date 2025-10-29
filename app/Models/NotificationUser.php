<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{
    protected $table = 'notification_users';
    protected $fillable= [
        'user_id',
        'recever_id',
        'content'
    ]
}
