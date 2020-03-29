<?php

use Illuminate\Support\Facades\Route;
use App\Models\PauliChessGame;

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
    if (Auth::user()) {
        return redirect()->route('paulichess.games.index');
    } else {
        return redirect()->route('login');
    }
});

Route::model('PauliChessGame', PauliChessGame::class);

Route::prefix('paulichess')
    ->middleware('auth')
    ->group(function() {
        Route::get('games', 'PauliChessGameController@index')
            ->name('paulichess.games.index');

        Route::get('games/{PauliChessGame}', 'PauliChessGameController@show')
            ->name('paulichess.games.show');

        Route::post('games', 'PauliChessGameController@store')
            ->name('paulichess.games.store');

        Route::post('games/{PauliChessGame}/join', 'PauliChessGameController@joinGame')
            ->name('paulichess.games.join');
    });

Route::mixin(new \Laravel\Ui\AuthRouteMethods());
Auth::routes();
