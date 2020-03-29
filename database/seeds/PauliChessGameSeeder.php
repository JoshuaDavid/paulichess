<?php

use Illuminate\Database\Seeder;

use App\Models\PauliChessGame;
use App\Models\PauliChessGamePlayer;
use App\User;

class PauliChessGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alice = User::where('name', 'alice')->first();
        $bob = User::where('name', 'bob')->first();

        $game = new PauliChessGame();
        $game->save();

        $alicePlayer = new PauliChessGamePlayer();
        $alicePlayer->user()->associate($alice);
        $alicePlayer->game()->associate($game);
        $alicePlayer->color = 'white';
        $alicePlayer->save();

        $bobPlayer = new PauliChessGamePlayer();
        $bobPlayer->user()->associate($bob);
        $bobPlayer->game()->associate($game);
        $bobPlayer->color = 'black';
        $bobPlayer->save();
    }
}
