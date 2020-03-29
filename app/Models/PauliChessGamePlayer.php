<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class PauliChessGamePlayer extends Model
{
    public function game() {
        return $this->belongsTo(PauliChessGame::class, 'pauli_chess_game_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
