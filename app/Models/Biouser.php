<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biouser extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'bio',
        'path_avatar',
        'pgp_public',
        'website',
        'email ',
    ];
}
