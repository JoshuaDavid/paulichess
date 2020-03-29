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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pieces() {
        return $this->hasMany(PauliChessGamePiece::class, 'pauli_chess_game_player_id');
    }

    public function getLegalMoves() {
        $moves = [];
        foreach ($this->pieces as $piece) {
            $moves = array_merge($moves, $piece->getLegalMoves());
        }
        return $moves;
    }
}
