<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PauliChessGameMove extends Model
{
    public function game() {
        return $this->belongsTo(PauliChessGame::class, 'pauli_chess_game_id');
    }

    public function player() {
        return $this->belongsTo(PauliChessGamePlayer::class, 'pauli_chess_game_player_id');
    }

    public function movedPiece() {
        return $this->belongsTo(PauliChessGamePiece::class, 'moved_piece_id');
    }

    public function capturedPiece() {
        return $this->belongsTo(PauliChessGamePiece::class, 'captured_piece_id');
    }
}
