<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PauliChessGamePlayer extends Model
{
    public function game() {
        $this->belongsTo(PauliChessGame::class);
    }
}
