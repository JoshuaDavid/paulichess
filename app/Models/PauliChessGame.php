<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PauliChessGame extends Model
{
    public function players() {
        return $this->hasMany(PauliChessGamePlayer::class, 'pauli_chess_game_id');
    }

    public function pieces() {
        return $this->hasMany(PauliChessGamePiece::class, 'pauli_chess_game_id');
    }

    public function getWhitePlayer() {
        foreach ($this->players as $player) {
            if ($player->color == 'white') {
                return $player;
            }
        }
    }

    public function getBlackPlayer() {
        foreach ($this->players as $player) {
            if ($player->color == 'black') {
                return $player;
            }
        }
    }

    public function init() {
        $whitePlayer = $this->getWhitePlayer();
        $blackPlayer = $this->getBlackPlayer();
        $layout = [
            [
                'y' => 1,
                'color' => 'white',
                'player' => $whitePlayer,
                'pieces' => str_split("RNBQKBNR"),
            ],
            [
                'y' => 2,
                'color' => 'white',
                'player' => $whitePlayer,
                'pieces' => str_split("PPPPPPPP"),
            ],
            [
                'y' => 7,
                'color' => 'black',
                'player' => $blackPlayer,
                'pieces' => str_split("PPPPPPPP"),
            ],
            [
                'y' => 8,
                'color' => 'black',
                'player' => $blackPlayer,
                'pieces' => str_split("RNBQKBNR"),
            ],
        ];
        foreach ($layout as $row) {
            foreach ($row['pieces'] as $i => $shortName) {
                $piece = new PauliChessGamePiece();
                $piece->player()->associate($row['player']);
                $piece->game()->associate($this);
                $piece->color = $row['color'];
                $piece->y = $row['y'];
                $piece->x = $i + 1;
                $piece->type = PauliChessGamePiece::getTypeFromShortName($shortName);
                $piece->save();
            }
        }
    }

    public function isUserPlaying($user) {
        foreach ($this->players as $player) {
            if ($player->user and $player->user->id == $user->id) {
                return true;
            }
        }
        return false;
    }

    public function isTurnOfUser($user) {
        foreach ($this->players as $player) {
            if ($player->user and $player->user->id == $user->id) {
                if ($player->color == $this->turn) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getPieces($filter) {
        $matches = [];
        foreach ($this->pieces as $piece) {
            if ($filter($piece)) {
                $matches[] = $piece;
            }
        }
        return $matches;
    }

    public function isEmptySquare($x, $y) {
        $pieces = $this->getPieces(function ($piece) use ($x, $y) {
            return $piece->x == $x && $piece->y == $y;
        });
        return count($pieces) == 0;
    }

    public function hasEmptySlot($x, $y) {
        $pieces = $this->getPieces(function ($piece) use ($x, $y) {
            return $piece->x == $x && $piece->y == $y;
        });
        return count($pieces) < 2;
    }

    public function getOpposingPieces($x, $y, $color) {
        return $this->getPieces(function ($piece) use ($x, $y, $color) {
            return $piece->x == $x && $piece->y == $y && $piece->color != $color;
        });
    }
}
