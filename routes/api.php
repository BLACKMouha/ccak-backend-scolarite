<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('keycloak')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/protected-resource', function () {
        return response()->json(['message' => 'This is a protected resource accessible only to authenticated Keycloak users.']);
    });
});
