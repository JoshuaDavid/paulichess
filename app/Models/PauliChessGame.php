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

    public function init($randomize = false) {
        if ($randomize) {
            if (rand(0, 1) == 0) {
                $colors = ['white', 'black'];
            } else {
                $colors = ['black', 'white'];
            }
        } else {
            $colors = ['white', 'black'];
        }

        $this->players[0]->color = $colors[0];
        $this->players[0]->save();
        $this->players[1]->color = $colors[1];
        $this->players[1]->save();

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

    public function getPlayerOfUser($user) {
        foreach ($this->players as $player) {
            if ($player->user and $player->user->id == $user->id) {
                return $player;
            }
        }
        return null;
    }

    public function isUserPlaying($user) {
        return (bool) $this->getPlayerOfUser($user);
    }

    public function isTurnOfUser($user) {
        // Get fancy cause a player can play against themselves
        foreach ($this->players as $player) {
            if ($player->user && $player->user->id == $user->id && $player->color == $this->turn) {
                return true;
            }
        }
        return false;
    }

    public function getActivePlayer() {
        foreach ($this->players as $player) {
            if ($player->color == $this->turn) {
                return $player;
            }
        }
        return null;
    }

    public function getPieces($filter) {
        $matches = [];
        foreach ($this->pieces as $piece) {
            if ($piece->is_captured == false && $filter($piece)) {
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

    public function switchPlayers() {
        if ($this->turn == 'white') {
            $this->turn = 'black';
        } else {
            $this->turn = 'white';
        }
        $this->save();
    }

    public function executeMove(PauliChessGameMove $move) {
        $piece = $move->movedPiece;
        $piece->x = $move->to_x;
        $piece->y = $move->to_y;
        if ($move->promotion_type) {
            $piece->type = $move->promotion_type;
        }
        $piece->save();

        if ($move->capturedPiece) {
            $move->capturedPiece->is_captured = true;
            $move->capturedPiece->save();

            if ($move->capturedPiece->type == PauliChessGamePiece::TYPE_KING) {
                $this->winner = $move->player->color;
                $this->save();
            }
        }

        $move->save();

        $this->switchPlayers();
    }
}
