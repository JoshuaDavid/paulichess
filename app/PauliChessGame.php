<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PauliChessGame extends Model
{
    public function players() {
        return $this->hasMany(PauliChessGamePlayer::class);
    }
}
