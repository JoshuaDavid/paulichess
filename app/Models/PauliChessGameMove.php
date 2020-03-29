<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Obj;

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

    public function getSearchKey() {
        return implode('-', [
            $this->movedPiece->id,
            $this->from_x,
            $this->from_y,
            data_get($this, 'capturedPiece.id', ''),
            $this->to_x,
            $this->to_y,
            data_get($this, 'promotion_type', ''),
        ]);
    }
}
