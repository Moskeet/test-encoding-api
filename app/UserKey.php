<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserKey extends Model
{
    protected $table = 'user_keys';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'user_public_key','server_public_key','server_private_key','message'
    ];

}
