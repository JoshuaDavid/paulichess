<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PauliChessGamePlayer extends Model
{
    public function game() {
        $this->belongsTo(PauliChessGame::class);
    }
}
