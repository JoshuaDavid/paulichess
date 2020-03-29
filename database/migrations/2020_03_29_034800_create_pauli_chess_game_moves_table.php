<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePauliChessGameMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pauli_chess_game_moves', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pauli_chess_game_id')->unsigned();
            $table->string('type');
            $table->integer('from_x');
            $table->integer('from_y');
            $table->integer('to_x');
            $table->integer('to_y');
            $table->string('promotion_type')->nullable();
            $table->foreign('pauli_chess_game_id')
                ->references('id')
                ->on('pauli_chess_games')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('pauli_chess_game_player_id')->unsigned();
            $table->foreign('pauli_chess_game_player_id')
                ->references('id')
                ->on('pauli_chess_game_players')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('moved_piece_id')->unsigned();
            $table->foreign('moved_piece_id')
                ->references('id')
                ->on('pauli_chess_game_pieces')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('captured_piece_id')->unsigned()->nullable();
            $table->foreign('captured_piece_id')
                ->references('id')
                ->on('pauli_chess_game_pieces')
                ->onUpdate('cascade')
                ->onDelete('set null');
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
        Schema::dropIfExists('pauli_chess_game_moves');
    }
}
