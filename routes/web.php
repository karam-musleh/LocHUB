<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// routes/web.php
Route::get('/api/docs', function () {
    return response()->file(public_path('openapi.json'));
});
