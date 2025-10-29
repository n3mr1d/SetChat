<?php

use App\Http\Controllers\GateController;
use App\Http\Controllers\Room\PublicRoom;
use App\Http\Controllers\Welcome;
use Illuminate\Support\Facades\Route;

// WELCOME PAGE
Route::get('/', [Welcome::class, 'index'])->name('index.welcome');
// GATE ROUTE FOR LOGIN AND REGISTER PAGE
// Middleware : guest
Route::group(['middleware' => 'guest'], function () {
    // login page
    Route::get('/login', [GateController::class, 'loginindex'])->name('login');
    Route::post('/login', [GateController::class, 'loginstore'])->name('store.login');
    // register page
    Route::match(['get', 'post'], '/register', [GateController::class, 'register'])->name('index.register');   // register page

});
// Public Room default
Route::group(['middleware' => 'auth'], function () {
    Route::get('/room', [PublicRoom::class, 'index'])->name('index.room');
    Route::get('/tools/chat', [PublicRoom::class, 'view'])->name('index.chat');
    Route::match(['get', 'post'], '/tools/send', [PublicRoom::class, 'send'])->name('index.send');
    Route::get('/tools/setting', [PublicRoom::class, 'setting'])->name('index.setting');

});
