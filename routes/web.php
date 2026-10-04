<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\PhuTungController;

Route::resource('phutung', PhuTungController::class)->except(['show']);
