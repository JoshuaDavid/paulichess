<?php

namespace App\Http\Controllers;

use App\Models\PauliChessGame;
use App\Models\PauliChessGamePlayer;
use Illuminate\Http\Request;
use Auth;

class PauliChessGameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $games = PauliChessGame::query()
            ->paginate();

        return response()->view('paulichess.games.index', [
            'games' => $games,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $game = new PauliChessGame();
        $game->turn = 'white';
        $game->save();

        $player = new PauliChessGamePlayer();
        $player->game()->associate($game);
        $player->user()->associate($user);
        $player->color = 'shuffle';
        $player->save();

        return redirect()->route('paulichess.games.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PauliChessGame  $pauliChessGame
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, PauliChessGame $game)
    {
        $user = Auth::user();

        $game->loadCorrectly();

        $movedPieceId = $request->input('moved_piece_id');
        $capturedPieceId = $request->input('captured_piece_id');
        $toX = $request->input('to_x');
        $toY = $request->input('to_y');

        $movedPiece = null;
        $capturedPiece = null;
        foreach ($game->pieces as $piece) {
            if ($piece->id == $movedPieceId) {
                $movedPiece = $piece;
            }
            if ($piece->id == $capturedPieceId) {
                $capturedPiece = $piece;
            }
        }

        if ($movedPiece && $movedPiece->color != $game->turn) {
            throw new \Exception("You can only move your own pieces");
        }

        if ($movedPiece && $capturedPiece && $movedPiece->color == $capturedPiece->color) {
            throw new \Exception("You cannot capture your own pieces");
        }

        $game->load(['players', 'pieces']);
        $board = array_fill(1, 8, array_fill(1, 8, []));
        foreach ($game->pieces as $piece) {
            if (!$piece->is_captured) {
                $board[$piece->y][$piece->x][] = $piece;
            }
        }

        $legalMoves = [];
        if ($game->isTurnOfUser($user)) {
            $player = $game->getActivePlayer();
            $legalMoves = $player->getLegalMoves();
        }


        $filteredMoves = [];
        foreach ($legalMoves as $move) {
            $isOk = true;
            if ($movedPiece && $move->movedPiece->id != $movedPiece->id) {
                $isOk = false;
            }

            if ($toX && $toY && !($move->to_x == $toX && $move->to_y == $toY && !$move->capturedPiece)) {
                $isOk = false;
            }

            if ($capturedPiece && !($move->capturedPiece && $move->capturedPiece->id == $capturedPiece->id)) {
                $isOk = false;
            }

            if ($game->wouldBeInCheckAfterMove($move)) {
                // Cannot move into check
                $isOk = false;
            }
            $game->undoLastMove(false);




            if ($isOk) {
                $filteredMoves[] = $move;
            }
        }

        $legalMoves = $filteredMoves;

        $movesByPieceId = [];
        foreach ($game->pieces as $piece) {
            $movesByPieceId[$piece->id] = [];
        }
        foreach ($legalMoves as $move) {
            $movesByPieceId[$move->movedPiece->id][] = $move;
        }

        return response()->view('paulichess.games.show', [
            'game'              => $game,
            'board'             => $board,
            'movedPiece'        => $movedPiece,
            'movesByPieceId'    => $movesByPieceId,
            'capturedPiece'     => $capturedPiece,
            'legalMoves'        => $legalMoves,
            'toX'               => $toX,
            'toY'               => $toY,
        ]);
    }

    public function joinGame(PauliChessGame $game) {
        $user = Auth::user();

        $player = new PauliChessGamePlayer();
        $player->game()->associate($game);
        $player->user()->associate($user);
        $player->color = 'shuffle';
        $player->save();

        $game->save();
        $game->init();

        return redirect()->route('paulichess.games.show', [$game->id]);
    }

    public function move(Request $request, PauliChessGame $game) {
        $user = Auth::user();
        if (!$game->isTurnOfUser($user)) {
            throw new Exception("It is not your turn");
        }

        $game->loadCorrectly();

        $player = $game->getActivePlayer();
        $moves = $player->getLegalMoves();
        foreach ($moves as $move) {
            if ($request->input('move') == $move->getSearchKey()) {
                $game->executeMove($move);

                if ($game->isInCheckmate($game->getOpposingPlayer($player))) {
                    $game->declareWinner($player);
                }
                break;
            }
        }

        return redirect()->route('paulichess.games.show', [$game->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PauliChessGame  $pauliChessGame
     * @return \Illuminate\Http\Response
     */
    public function edit(PauliChessGame $pauliChessGame)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PauliChessGame  $pauliChessGame
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PauliChessGame $pauliChessGame)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PauliChessGame  $pauliChessGame
     * @return \Illuminate\Http\Response
     */
    public function destroy(PauliChessGame $pauliChessGame)
    {
        //
    }
}
