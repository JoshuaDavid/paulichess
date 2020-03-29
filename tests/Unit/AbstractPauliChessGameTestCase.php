<?php

namespace Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;

use App\Models\PauliChessGame;
use App\Models\PauliChessGamePlayer;
use App\Models\PauliChessGamePiece;
use App\Models\PauliChessGameMove;

class AbstractPauliChessGameTestCase extends TestCase {
    protected $ids;

    public function setUp(): void {
        $this->ids = [];
    }

    public function id($table) {
        $id = data_get($this->ids, $table, 1);
        $this->ids[$table] = $id + 1;
        return $id;
    }

    public function makePartialModel($class) {
        $model = Mockery::mock($class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $model->id = $this->id($class);
        return $model;
    }
    
    public function initEmptyGame() {
        $game = $this->makePartialModel(PauliChessGame::class);
        $game->setRelation('players', collect([]));
        $game->setRelation('pieces', collect([]));
        $game->setRelation('moves', collect([]));
        $game->turn = 'white';

        $white = $this->makePartialModel(PauliChessGamePlayer::class);
        $white->shouldReceive('newQuery')->andReturn(null);
        $white->color = 'white';
        $white->setRelation('game', $game);
        $white->setRelation('pieces', collect([]));
        $white->setRelation('moves', collect([]));
        $game->players->push($white);

        $black = $this->makePartialModel(PauliChessGamePlayer::class);
        $black->shouldReceive('getConnection->connection')->andReturn(null);
        $black->color = 'black';
        $black->setRelation('game', $game);
        $black->setRelation('pieces', collect([]));
        $black->setRelation('moves', collect([]));
        $game->players->push($black);

        return $game;
    }

    public function addPiece($player, $type, $x, $y) {
        $piece = $this->makePartialModel(PauliChessGamePiece::class);
        $piece->shouldReceive('getConnection->connection')->andReturn(null);
        $piece->setRelation('game', $player->game);
        $piece->setRelation('player', $player);
        $piece->setRelation('moves', collect([]));
        $piece->type = $type;
        $piece->x = $x;
        $piece->y = $y;

        $player->game->pieces->add($piece);
        $player->pieces->add($piece);

        $piece->shouldReceive('constructMove')
            ->andReturnUsing(function ($toX, $toY, $pieceToCapture, $promotionType) use ($piece) {
                $movedPiece = $piece;
                $move = $this->makePartialModel(PauliChessGameMove::class);

                $move->setRelation('game', $movedPiece->game);
                $movedPiece->game->moves->push($move);
                $move->setRelation('player', $movedPiece->player);
                $movedPiece->player->moves->push($move);
                $move->setRelation('movedPiece', $movedPiece);
                $movedPiece->moves->push($move);

                $move->from_x = $movedPiece->x;
                $move->from_y = $movedPiece->y;
                $move->to_x = $toX;
                $move->to_y = $toY;

                if ($pieceToCapture) {
                    $move->setRelation('capturedPiece', $pieceToCapture);
                    $move->type = 'capture';
                } else {
                    $move->setRelation('capturedPiece', null);
                    $move->type = 'move';
                }

                if ($promotionType) {
                    $move->promotion_type = $promotionType;
                }

                return $move;
            });


        return $piece;
    }

    public function getMatchingMoves($piece, $filter) {
        $matches = [];
        $moves = $piece->getLegalMoves();
        foreach ($moves as $move) {
            if ($filter($move)) {
                $matches[] = $move;
            }
        }
        return $matches;
    }

    public function assertHasNMoves($piece, $n, $message = null) {
        $nMoves = count($piece->getLegalMoves());
        $this->assertEquals($n, $nMoves, $message
            ?: "Expected {$piece->type} to have exactly {$n} moves, but actually has {$nMoves}");
    }

    public function assertCanMoveToWithoutCapture($piece, $x, $y, $message = null) {
        $this->assertTrue(count($this->getMatchingMoves($piece, function($move) use ($x, $y) {
            return $move->to_x == $x
                && $move->to_y == $y
                && $move->capturedPiece == null;
        })) > 0, $message ?: "Expected {$piece->type} to be able to move to {$x} {$y} but it cannot");
    }
}
