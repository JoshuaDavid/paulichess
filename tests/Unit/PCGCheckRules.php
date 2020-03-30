<?php

namespace Tests\Unit;

use App\Models\PauliChessGamePiece;

class PCGCheckRules extends AbstractPauliChessGameTestCase {
    public function testNotInCheckIfNotInDanger() {
        $game = $this->initEmptyGame();
        $wk = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_KING, 5, 1);
        $br = $this->addPiece($game->getBlackPlayer(), PauliChessGamePiece::TYPE_ROOK, 1, 8);

        $this->assertFalse($game->isInCheck($game->getWhitePlayer()));
    }

    public function testInCheckIfInDanger() {
        $game = $this->initEmptyGame();
        $wk = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_KING, 5, 1);
        $br = $this->addPiece($game->getBlackPlayer(), PauliChessGamePiece::TYPE_ROOK, 5, 8);

        $this->assertTrue($game->isInCheck($game->getWhitePlayer()));
    }

    public function testNotInCheckateIfNotInCheck() {
        $game = $this->initEmptyGame();
        $wk = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_KING, 5, 1);
        $br = $this->addPiece($game->getBlackPlayer(), PauliChessGamePiece::TYPE_ROOK, 1, 8);

        $this->assertFalse($game->isInCheckmate($game->getWhitePlayer()));
    }

    public function testNotInCheckateIfCanEscapeCheck() {
        $game = $this->initEmptyGame();
        $wk = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_KING, 5, 1);
        $br = $this->addPiece($game->getBlackPlayer(), PauliChessGamePiece::TYPE_ROOK, 5, 8);

        $this->assertFalse($game->isInCheckmate($game->getWhitePlayer()));
    }

    public function testIsInCheckateIfCannotEscapeCheck() {
        $game = $this->initEmptyGame();
        $wk = $this->addPiece($game->getWhitePlayer(), PauliChessGamePiece::TYPE_KING, 5, 1);
        $br1 = $this->addPiece($game->getBlackPlayer(), PauliChessGamePiece::TYPE_ROOK, 1, 1);
        $br2 = $this->addPiece($game->getBlackPlayer(), PauliChessGamePiece::TYPE_ROOK, 1, 2);

        $this->assertTrue($game->isInCheckmate($game->getWhitePlayer()));
    }
}
