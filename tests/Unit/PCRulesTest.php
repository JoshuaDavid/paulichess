<?php

namespace Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;

use App\Models\PauliChessGamePiece;

class PCRulesTest extends AbstractPauliChessGameTestCase {
    public function testWhitePawnOnD4CanAdvanceOne() {
        $game = $this->initEmptyGame();
        $pawn = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 4);
        $this->assertHasNMoves($pawn, 1);
        $this->assertCanMoveToWithoutCapture($pawn, 4, 5);
    }

    public function testWhitePawnOnD2CanAdvanceOneOrTwo() {
        $game = $this->initEmptyGame();
        $pawn = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 2);
        $this->assertHasNMoves($pawn, 2);
        $this->assertCanMoveToWithoutCapture($pawn, 4, 3);
        $this->assertCanMoveToWithoutCapture($pawn, 4, 4);
    }
}
