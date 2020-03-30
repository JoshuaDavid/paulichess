<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PauliChessGamePiece extends Model
{
    public const TYPE_PAWN      = 'pawn';
    public const TYPE_ROOK      = 'rook';
    public const TYPE_KNIGHT    = 'knight';
    public const TYPE_BISHOP    = 'bishop';
    public const TYPE_QUEEN     = 'queen';
    public const TYPE_KING      = 'king';


    public function game() {
        return $this->belongsTo(PauliChessGame::class, 'pauli_chess_game_id');
    }

    public function player() {
        return $this->belongsTo(PauliChessGamePlayer::class, 'pauli_chess_game_player_id');
    }

    public static function getPawnPromotionOptions() {
        return [
            static::TYPE_ROOK,
            static::TYPE_KNIGHT,
            static::TYPE_BISHOP,
            static::TYPE_QUEEN,
        ];
    }

    public static function getShortNameFromType($type) {
        return [
            static::TYPE_PAWN      => 'P',
            static::TYPE_ROOK      => 'R',
            static::TYPE_KNIGHT    => 'N',
            static::TYPE_BISHOP    => 'B',
            static::TYPE_QUEEN     => 'Q',
            static::TYPE_KING      => 'K',
        ][$type];
    }

    public static function getTypeFromShortName($shortName) {
        return [
            'P' => static::TYPE_PAWN,
            'R' => static::TYPE_ROOK,
            'N' => static::TYPE_KNIGHT,
            'B' => static::TYPE_BISHOP,
            'Q' => static::TYPE_QUEEN,
            'K' => static::TYPE_KING,
        ][$shortName];
    }

    public function getSymbol() {
        return [
            'black' => [
                static::TYPE_PAWN      => '♟',
                static::TYPE_ROOK      => '♜',
                static::TYPE_KNIGHT    => '♞',
                static::TYPE_BISHOP    => '♝',
                static::TYPE_QUEEN     => '♛',
                static::TYPE_KING      => '♚',
            ],
            'white' => [
                static::TYPE_PAWN      => '♙',
                static::TYPE_ROOK      => '♖',
                static::TYPE_KNIGHT    => '♘',
                static::TYPE_BISHOP    => '♗',
                static::TYPE_QUEEN     => '♕',
                static::TYPE_KING      => '♔',
            ],
        ][$this->color][$this->type];
    }

    protected function constructMove($toX, $toY, $pieceToCapture, $promotionType) {
        $move = new PauliChessGameMove();
        $move->game()->associate($this->game);
        $move->player()->associate($this->player);
        $move->movedPiece()->associate($this);
        $move->from_x = $this->x;
        $move->from_y = $this->y;
        $move->to_x = $toX;
        $move->to_y = $toY;
        if ($pieceToCapture) {
            $move->capturedPiece()->associate($pieceToCapture);
            $move->type = 'capture';
        } else {
            $move->type = 'move';
        }
        if ($promotionType) {
            $move->promotion_type = $promotionType;
        }
        return $move;
    }

    public function canBeEnPassantCaptured() {
        return false;
    }

    public function getLegalBlackPawnMoves() {
        // Pawns can move forwards by 1.
        // If a pawn is on the starting row, it can move forwards by 2,
        //  assuming that the square in front of it is empty
        // Pawns can capture diagonally forward-left and forward-right.
        // Pawns can capture sideways only if the pawn to their left or
        //  right has just jumped by 2, and the piece they are capturing
        //  is that pawn.
        // If a pawn moves to the last square, it can "promote" and become
        //  a knight, bishop, rook, or queen

        $moves = [];
        if ($this->y > 2) {
            if ($this->game->hasEmptySlot($this->x, $this->y - 1)) {
                $moves[] = $this->constructMove($this->x, $this->y - 1, null, null);
            }
            if ($this->y == 7) {
                if ($this->game->isEmptySquare($this->x, $this->y - 1)
                    && $this->game->hasEmptySlot($this->x, $this->y - 2)) {
                    $moves[] = $this->constructMove($this->x, $this->y - 2, null, null);
                }
            }
        } else if ($this->y == 2) {
            if ($this->game->hasEmptySlot($this->x, 1)) {
                foreach ($this->getPawnPromotionOptions() as $type) {
                    $moves[] = $this->constructMove($this->x, $this->y - 1, null, $type);
                }
            }
        }

        $capturablePieces = $this->game->getPieces(function($piece) {
            return $piece->color == 'white'
                && (
                    (
                        $piece->y == $this->y - 1
                        && (
                            $piece->x == $this->x - 1
                            || $piece->x == $this->x + 1
                        )
                    )
                    || (
                        $piece->y == $this->y
                        && $piece->canBeEnPassantCaptured()
                        && (
                            $piece->x == $this->x - 1
                            || $piece->x == $this->x + 1
                        )
                    )
                );
        });

        foreach ($capturablePieces as $capture) {
            if ($capture->y != 1) {
                $moves[] = $this->constructMove($capture->x, $capture->y, $capture, null);
            } else {
                foreach ($this->getPawnPromotionOptions() as $type) {
                    $moves[] = $this->constructMove($capture->x, $capture->y, $capture, $type);
                }
            }
        }
        
        return $moves;
    }

    public function getLegalWhitePawnMoves() {
        $moves = [];
        // Pawns can move forwards by 1 until the end of the board
        if ($this->y < 7) {
            if ($this->game->hasEmptySlot($this->x, $this->y + 1)) {
                $moves[] = $this->constructMove($this->x, $this->y + 1, null, null);
            }
            if ($this->y == 2) {
                if ($this->game->isEmptySquare($this->x, $this->y + 1)
                    && $this->game->hasEmptySlot($this->x, $this->y + 2)) {
                    $moves[] = $this->constructMove($this->x, $this->y + 2, null, null);
                }
            }
        } else if ($this->y == 7) {
            if ($this->game->hasEmptySlot($this->x, 8)) {
                foreach ($this->getPawnPromotionOptions() as $type) {
                    $moves[] = $this->constructMove($this->x, $this->y + 1, null, $type);
                }
            }
        }

        $capturablePieces = $this->game->getPieces(function($piece) {
            return $piece->color == 'black'
                && (
                    (
                        $piece->y == $this->y + 1
                        && (
                            $piece->x == $this->x - 1
                            || $piece->x == $this->x + 1
                        )
                    )
                    || (
                        $piece->y == $this->y
                        && $piece->canBeEnPassantCaptured()
                        && (
                            $piece->x == $this->x - 1
                            || $piece->x == $this->x + 1
                        )
                    )
                );
        });

        foreach ($capturablePieces as $capture) {
            if ($capture->y != 8) {
                $moves[] = $this->constructMove($capture->x, $capture->y, $capture, null);
            } else {
                foreach ($this->getPawnPromotionOptions() as $type) {
                    $moves[] = $this->constructMove($capture->x, $capture->y, $capture, $type);
                }
            }
        }

        return $moves;
    }

    public function getLegalMovesInDirection($dx, $dy, $maxSteps) {
        $moves = [];
        $x = $this->x;
        $y = $this->y;

        for ($i = 0; $i < $maxSteps; $i++) {
            $x += $dx;
            $y += $dy;

            if ($x < 1 || $x > 8 || $y < 1 || $y > 8) {
                break;
            }

            foreach($this->game->getOpposingPieces($x, $y, $this->color) as $capture) {
                $moves[] = $this->constructMove($capture->x, $capture->y, $capture, null);
            }

            if (!$this->game->hasEmptySlot($x, $y)) {
                break;
            }

            $moves[] = $this->constructMove($x, $y, null, null);
            
            if (!$this->game->isEmptySquare($x, $y)) {
                break;
            }
        }

        return $moves;
    }

    public function syncWithPlayerAndGame() {
        $this->game->setRelation('pieces', $this->game->pieces->map(function($piece) {
            if ($piece->id == $this->id) {
                return $this;
            } else {
                return $piece;
            }
        }));

        $this->player->setRelation('pieces', $this->player->pieces->map(function($piece) {
            if ($piece->id == $this->id) {
                return $this;
            } else {
                return $piece;
            }
        }));
    }

    public function getLegalRookMoves() {
        return array_merge(
            $this->getLegalMovesInDirection( 0, +1, 8),
            $this->getLegalMovesInDirection(+1,  0, 8),
            $this->getLegalMovesInDirection( 0, -1, 8),
            $this->getLegalMovesInDirection(-1,  0, 8)
        );
    }

    public function getLegalBishopMoves() {
        return array_merge(
            $this->getLegalMovesInDirection(+1, +1, 8),
            $this->getLegalMovesInDirection(+1, -1, 8),
            $this->getLegalMovesInDirection(-1, -1, 8),
            $this->getLegalMovesInDirection(-1, +1, 8)
        );
    }

    public function getLegalQueenMoves() {
        return array_merge(
            $this->getLegalMovesInDirection(+1, +1, 8),
            $this->getLegalMovesInDirection(+1,  0, 8),
            $this->getLegalMovesInDirection(+1, -1, 8),
            $this->getLegalMovesInDirection( 0, -1, 8),
            $this->getLegalMovesInDirection(-1, -1, 8),
            $this->getLegalMovesInDirection(-1,  0, 8),
            $this->getLegalMovesInDirection(-1, +1, 8),
            $this->getLegalMovesInDirection( 0, +1, 8)
        );
    }

    public function getLegalKingMoves() {
        $moves = array_merge(
            $this->getLegalMovesInDirection(+1, +1, 1),
            $this->getLegalMovesInDirection(+1,  0, 1),
            $this->getLegalMovesInDirection(+1, -1, 1),
            $this->getLegalMovesInDirection( 0, -1, 1),
            $this->getLegalMovesInDirection(-1, -1, 1),
            $this->getLegalMovesInDirection(-1,  0, 1),
            $this->getLegalMovesInDirection(-1, +1, 1),
            $this->getLegalMovesInDirection( 0, +1, 1)
        );
        // todo: Add castling
        return $moves;
    }

    public function getLegalKnightMoves() {
        return array_merge(
            $this->getLegalMovesInDirection(+1, +2, 1),
            $this->getLegalMovesInDirection(+2, +1, 1),
            $this->getLegalMovesInDirection(+2, -1, 1),
            $this->getLegalMovesInDirection(+1, -2, 1),
            $this->getLegalMovesInDirection(-1, -2, 1),
            $this->getLegalMovesInDirection(-2, -1, 1),
            $this->getLegalMovesInDirection(-2, +1, 1),
            $this->getLegalMovesInDirection(-1, +2, 1)
        );
    }

    public function getLegalMoves() {
        if ($this->type === static::TYPE_PAWN) {
            if ($this->color === 'black') {
                return $this->getLegalBlackPawnMoves();
            } else {
                return $this->getLegalWhitePawnMoves();
            }
        } else if ($this->type === static::TYPE_KING) {
            return $this->getLegalKingMoves();
        } else if ($this->type === static::TYPE_QUEEN) {
            return $this->getLegalQueenMoves();
        } else if ($this->type === static::TYPE_BISHOP) {
            return $this->getLegalBishopMoves();
        } else if ($this->type === static::TYPE_ROOK) {
            return $this->getLegalRookMoves();
        } else if ($this->type === static::TYPE_KNIGHT) {
            return $this->getLegalKnightMoves();
        }
        return [];
    }

    public function canMoveToWithoutCapture($x, $y) {
        $moves = $this->getLegalMoves();
        foreach ($moves as $move) {
            if ($move->to_x == $x and $move->to_y == $y && !$move->capturedPiece) {
                return true;
            }
        }
        return false;
    }

    public function canCapture($capturedPiece) {
        $moves = $this->getLegalMoves();
        foreach ($moves as $move) {
            if ($move->capturedPiece && $move->capturedPiece->id == $capturedPiece->id) {
                return true;
            }
        }
        return false;
    }
}
