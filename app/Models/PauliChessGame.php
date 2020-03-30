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

    public function moves() {
        return $this->hasMany(PauliChessGameMove::class, 'pauli_chess_game_id');
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

    public function loadCorrectly() {
        $this->load([
            'players',
            'pieces',
            'moves',
        ]);

        $playersById = $this->players->keyBy('id');
        $piecesById = $this->pieces->keyBy('id');
        $movesById = $this->moves->keyBy('id');

        foreach ($this->players as $player) {
            $player->setRelation('game', $this);
            $player->setRelation('pieces', collect([]));
            $player->setRelation('moves', collect([]));
        }

        foreach ($this->pieces as $piece) {
            $piece->setRelation('game', $this);
            $playerId = $piece->getAttribute($piece->player()->getForeignKeyName());
            $player = $playersById[$playerId];
            $piece->setRelation('player',  $player);
            $player->pieces->push($piece);
            $piece->setRelation('moves', collect([]));
        }

        foreach ($this->moves as $move) {
            $move->setRelation('game', $this);

            $playerId = $move->getAttribute($move->player()->getForeignKeyName());
            $player = $playersById[$playerId];
            $move->setRelation('player', $player);
            $player->moves->push($move);

            $movedPieceId = $move->getAttribute($move->movedPiece()->getForeignKeyName());
            $movedPiece = $piecesById[$movedPieceId];
            $move->setRelation('movedPiece', $movedPiece);
            $piece->moves->push($move);

            $capturedPieceId = $move->getAttribute($move->capturedPiece()->getForeignKeyName());
            if ($capturedPieceId) {
                $capturedPiece = $piecesById[$capturedPieceId];
                $move->setRelation('capturedPiece', $capturedPiece);
            }
        }

        return $this;
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
        if ($this->winner) {
            return false;
        }

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

    public function getOpposingPlayer(PauliChessGamePlayer $player) {
        foreach ($this->players as $opponent) {
            if ($opponent->color != $player->color) {
                return $opponent;
            }
        }
        throw new Exception("A player should always have an opponent in an active game");
    }

    public function switchPlayers($save = true) {
        if ($this->turn == 'white') {
            $this->turn = 'black';
        } else {
            $this->turn = 'white';
        }

        if ($save) {
            $this->save();
        }
    }

    public function executeMove(PauliChessGameMove $move, $save = true) {
        $piece = $move->movedPiece;
        $piece->x = $move->to_x;
        $piece->y = $move->to_y;
        if ($move->promotion_type) {
            $piece->type = $move->promotion_type;
        }
        if ($save) {
            $piece->save();
        }

        if ($move->capturedPiece) {
            $move->capturedPiece->is_captured = true;
            if ($save) {
                $move->capturedPiece->save();
            }
        }

        $this->moves->push($move);
        if ($save) {
            $move->save();
        }

        $this->switchPlayers($save);
    }

    public function undoLastMove($save = true) {
        $move = $this->moves->pop();

        $piece = $move->movedPiece;
        $piece->x = $move->from_x;
        $piece->y = $move->from_y;
        if ($move->promotion_type) {
            $piece->type = PauliChessGamePiece::TYPE_PAWN;
        }
        if ($save) {
            $piece->save();
        }

        if ($move->capturedPiece) {
            $move->capturedPiece->is_captured = false;
            if ($save) {
                $move->capturedPiece->save();
            }
        }

        $this->switchPlayers($save);

        if ($save) {
            $move->delete();
        }
    }

    public function declareWinner($player) {
        $this->winner = $player->color;
        $this->save();
    }

    public function wouldBeInCheckAfterMove(PauliChessGameMove $move) {
        $wouldBeInCheckAfterMove = false;
        $this->executeMove($move, false);
        if ($this->isInCheck($move->player)) {
            $wouldBeInCheckAfterMove = true;
        }
        $this->undoLastMove(false);
        return $wouldBeInCheckAfterMove;
    }

    public function isInCheck(PauliChessGamePlayer $player) {
        $opponent = $this->getOpposingPlayer($player);
        foreach ($opponent->getLegalMoves() as $move) {
            if ($move->capturedPiece && $move->capturedPiece->type === PauliChessGamePiece::TYPE_KING) {
                return true;
            }
        }

        return false;
    }

    public function isInCheckmate(PauliChessGamePlayer $player) {
        if (!$this->isInCheck($player)) {
            return false;
        }

        foreach ($player->getLegalMoves() as $move) {
            $this->executeMove($move, false);
            $wouldBeInCheck = $this->isInCheck($player);
            $this->undoLastMove(false);

            if (!$wouldBeInCheck) {
                /*
                echo json_encode(array_merge(
                    $move->getAttributes(),
                    $move->movedPiece->getAttributes()
                )) . "\n";
                 */
                return false;
            }
        }

        return true;
    }
}
