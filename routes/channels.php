<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Tambahkan ini juga
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});