<?php

use App\Http\Controllers\AdminSongsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\MusicController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', [MusicController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/{id}', [MusicController::class, 'playSong']);
    });

//Admin Routes...
Route::prefix('admin')
    ->middleware('role:' . \App\Utils\Roles::$ADMIN)
    ->group(function (){
        Route::resource('songs', AdminSongsController::class);
        Route::post('upload', [AdminSongsController::class, 'upload']);
        Route::get('clear_temp', [AdminSongsController::class, 'clearTemp']);
        Route::post('artists/get_artists', [AdminSongsController::class, 'getArtists']);
        Route::resource('users', AdminUserController::class);
        Route::resource('artists', ArtistController::class);
    });
