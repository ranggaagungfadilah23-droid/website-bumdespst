<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
    'name',
    'username',
    'email',
    'password',
    'google_id',
    'role',
    'status',
    'no_hp',
    'email_verified_at',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function mitra()
    {
        return $this->hasOne(Mitra::class);
    }

    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class, 'user_id');
    }

    public function produks()
    {
        return $this->hasMany(Produk::class);
    }

    public function jasas()
    {
        return $this->hasMany(Jasa::class);
    }
}