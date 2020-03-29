<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionTypeColumnToPauliChessGameMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pauli_chess_game_moves', function (Blueprint $table) {
            $table->string('special_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pauli_chess_game_moves', function (Blueprint $table) {
            $table->dropColumn('special_type');
        });
    }
}
