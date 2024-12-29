<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['flexible.throttle:3,60,3600'])->get('/test-throttle', function () {
    if (rand(0, 1)) {abort(403);}
    return response()->json(['message' => 'Request successful']);
});