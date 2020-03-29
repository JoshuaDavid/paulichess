<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePauliChessGamePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pauli_chess_game_players', function (Blueprint $table) {
            $table->id();
            $table->integer('pauli_chess_game_id')->unsigned();
            $table->foreign('pauli_chess_game_id')
                ->references('id')
                ->on('pauli_chess_game');
            $table->string('color');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pauli_chess_game_players');
    }
}
