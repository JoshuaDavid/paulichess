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
    return redirect()->route('paulichess.games.index');
});

Route::model('PauliChessGame', PauliChessGame::class);

Route::prefix('paulichess')
    ->middleware('auth')
    ->group(function() {
        Route::get('games', 'PauliChessGameController@index')
            ->name('paulichess.games.index');

        Route::get('games/{PauliChessGame}', 'PauliChessGameController@show')
            ->name('paulichess.games.show');
    });

Auth::routes();
