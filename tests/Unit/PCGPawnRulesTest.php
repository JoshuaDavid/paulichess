<?php

namespace Tests\Unit;

use App\Models\PauliChessGamePiece;

class PCGPawnRulesTest extends AbstractPauliChessGameTestCase {
    public function testWhitePawnOnD4CanAdvanceOne() {
        $game = $this->initEmptyGame();
        $pawn = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 4);

        $this->assertCanMoveToWithoutCapture($pawn, 4, 5);
        $this->assertHasNMoves($pawn, 1);
    }

    public function testWhitePawnOnD2CanAdvanceOneOrTwo() {
        $game = $this->initEmptyGame();
        $pawn = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 2);

        $this->assertCanMoveToWithoutCapture($pawn, 4, 3);
        $this->assertCanMoveToWithoutCapture($pawn, 4, 4);
        $this->assertHasNMoves($pawn, 2);
    }

    public function testWhitePawnCanCaptureDiagonally() {
        $game = $this->initEmptyGame();
        $wp = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 4);
        $bp = $this->addPiece($game->getBlackPlayer(), PauliChessGamePiece::TYPE_PAWN, 5, 5);

        $this->assertCanMoveToWithoutCapture($wp, 4, 5);
        $this->assertCanCapture($wp, $bp);
        $this->assertHasNMoves($wp, 2);
    }

    public function testWhitePawnCanMoveIntoSingleOccupiedSquare() {
        $game = $this->initEmptyGame();
        $wp1 = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 4);
        $wp2 = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 5);

        $this->assertCanMoveToWithoutCapture($wp1, 4, 5);
        $this->assertHasNMoves($wp1, 1);
    }

    public function testWhitePawnCannotMoveIntoDoubleOccupiedSquare() {
        $game = $this->initEmptyGame();
        $wp1 = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 4);
        $wp2 = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 5);
        $wp3 = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 5);

        $this->assertHasNMoves($wp1, 0);
    }

    public function testWhitePawnCanCaptureIntoDoubleOccupiedSquare() {
        $game = $this->initEmptyGame();
        $wp1 = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 4, 4);
        $wp2 = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_PAWN, 5, 5);
        $bp1 = $this->addPiece($game->getBlackPlayer(), PauliChessGamePiece::TYPE_PAWN, 5, 5);

        $this->assertCanMoveToWithoutCapture($wp1, 4, 5);
        $this->assertCanCapture($wp1, $bp1);
        $this->assertHasNMoves($wp1, 2);
    }
}
