<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePauliChessGamePiecesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pauli_chess_game_pieces', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pauli_chess_game_id')->unsigned();
            $table->foreign('pauli_chess_game_id', 'game_id_foreign')
                ->references('id')
                ->on('pauli_chess_games')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('type');
            $table->string('color');
            $table->integer('x');
            $table->integer('y');
            $table->bigInteger('pauli_chess_game_player_id')->unsigned();
            $table->foreign('pauli_chess_game_player_id', 'player_id_foreign')
                ->references('id')
                ->on('pauli_chess_game_players')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('pauli_chess_game_pieces');
    }
}
