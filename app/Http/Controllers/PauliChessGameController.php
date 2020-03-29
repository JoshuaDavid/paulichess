<?php

namespace App\Http\Controllers;

use App\Models\PauliChessGame;
use Illuminate\Http\Request;

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PauliChessGame  $pauliChessGame
     * @return \Illuminate\Http\Response
     */
    public function show(PauliChessGame $game)
    {
        $game->load(['players', 'pieces']);
        $board = array_fill(1, 8, array_fill(1, 8, []));
        foreach ($game->pieces as $piece) {
            $board[$piece->y][$piece->x][] = $piece;
        }
        return response()->view('paulichess.games.show', [
            'game' => $game,
            'board' => $board,
        ]);
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
