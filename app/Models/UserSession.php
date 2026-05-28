<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = ['browser_token', 'user_id', 'session_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
