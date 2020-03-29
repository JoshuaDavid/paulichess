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
    public function show(PauliChessGame $game)
    {
        $user = Auth::user();

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

        return response()->view('paulichess.games.show', [
            'game' => $game,
            'board' => $board,
            'legalMoves' => $legalMoves,
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

        $player = $game->getActivePlayer();
        $moves = $player->getLegalMoves();
        foreach ($moves as $move) {
            if ($request->input('move') == $move->getSearchKey()) {
                $game->executeMove($move);
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
