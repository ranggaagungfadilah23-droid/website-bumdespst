<?php
// app/Http/Controllers/WebPushController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebPushController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint'                  => 'required|url',
            'keys.auth'                 => 'required|string',
            'keys.p256dh'               => 'required|string',
        ]);

        auth()->user()->updatePushSubscription(
            endpoint: $request->endpoint,
            key:      $request->keys['p256dh'],
            token:    $request->keys['auth'],
            encoding: 'aesgcm',
        );

        return response()->json(['status' => 'subscribed']);
    }

    public function unsubscribe(Request $request)
    {
        auth()->user()->deletePushSubscription($request->endpoint);
        return response()->json(['status' => 'unsubscribed']);
    }
}