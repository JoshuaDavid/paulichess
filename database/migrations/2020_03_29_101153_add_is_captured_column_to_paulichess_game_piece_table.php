<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCapturedColumnToPaulichessGamePieceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pauli_chess_game_pieces', function (Blueprint $table) {
            $table->boolean('is_captured')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pauli_chess_game_pieces', function (Blueprint $table) {
            $table->dropColumn('is_captured');
        });
    }
}
