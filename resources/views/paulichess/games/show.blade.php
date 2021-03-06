@extends('base')

@section('content')
<h1>Pauli Chess: {{ implode(' vs ', $game->players->pluck('user')->pluck('name')->toArray()) }}</h1>
<table class="chessboard">
    <thead>
        <th></th>
        <th>a</th>
        <th>b</th>
        <th>c</th>
        <th>d</th>
        <th>e</th>
        <th>f</th>
        <th>g</th>
        <th>h</th>
    </thead>
    <tbody>
        @foreach(range(8, 1) as $y)
        <tr>
            <th>{{ $y }}</th>
            @foreach(range(1, 8) as $x)
            <td class="{{ ($x + $y) % 2 == 0 ? 'white' : 'black' }}-square">
                @if (!($movedPiece))
                    @foreach($board[$y][$x] as $piece)
                        @if (count($movesByPieceId[$piece->id]) == 0)
                            <div class="slot">{{ $piece->getSymbol() }}</div>
                        @else
                            <a href="{{ route('paulichess.games.show', [$game->id, 'moved_piece_id' => $piece->id]) }}">
                                <div class="slot">{{ $piece->getSymbol() }}</div>
                            </a>
                        @endif
                    @endforeach
                @elseif($toX && $toY)
                    @foreach($board[$y][$x] as $piece)
                        @if ($piece->id == $movedPiece->id)
                            <div class="slot bg-danger">&nbsp;</div>
                        @else
                            <div class="slot">{{ $piece->getSymbol() }}</div>
                        @endif
                    @endforeach
                @elseif($capturedPiece)
                    @foreach($board[$y][$x] as $piece)
                        @if ($piece->id == $movedPiece->id)
                            <div class="slot bg-danger">&nbsp;</div>
                        @elseif ($piece->id == $capturedPiece->id)
                            <div class="slot bg-success">{{ $movedPiece->getSymbol() }}</div>
                        @else
                            <div class="slot">{{ $piece->getSymbol() }}</div>
                        @endif
                    @endforeach
                @else
                    @foreach($board[$y][$x] as $piece)
                        @if ($piece->id == $movedPiece->id)
                            <div class="slot bg-secondary">{{ $piece->getSymbol() }}</div>
                        @elseif ($movedPiece->canCapture($piece))
                            <a href="{{ route('paulichess.games.show', [$game->id, 'moved_piece_id' => $movedPiece->id, 'captured_piece_id' => $piece->id]) }}">
                                <div class="slot bg-warning">{{ $piece->getSymbol() }}</div>
                            </a>
                        @else
                            <div class="slot">{{ $piece->getSymbol() }}</div>
                        @endif
                    @endforeach
                @endif

                @if (count($board[$y][$x]) < 2)
                    @if (!$movedPiece)
                    <div class="slot">&nbsp;</div>
                    @elseif ($capturedPiece)
                        <div class="slot">&nbsp;</div>
                    @elseif ($toX && $toY)
                        @if ($toX == $x && $toY == $y)
                            <div class="slot bg-success">{{ $movedPiece->getSymbol() }}</div>
                        @else
                            <div class="slot">&nbsp;</div>
                        @endif
                    @else
                        @if($movedPiece->canMoveToWithoutCapture($x, $y))
                            <a href="{{ route('paulichess.games.show', [$game->id, 'moved_piece_id' => $movedPiece->id, 'to_x' => $x, 'to_y' => $y]) }}">
                                <div class="slot bg-primary">&nbsp;</div>
                            </a>
                        @else
                            <div class="slot">&nbsp;</div>
                        @endif
                    @endif
                @endif

                @if (count($board[$y][$x]) < 1)
                    <div class="slot">&nbsp;</div>
                @endif
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

<div>
@if ($game->winner)
<h1>Game over! Winner is {{ $game->winner }}</h1>
<h2>Moves:</h2>
<ol>
    @foreach ($game->moves as $move)
    <li>
        {{ $move->player->name }}
        ({{ $move->player->color }})
        moved
        <b>({{ $move->movedPiece->type }})</b>
        from
        <b>{{ " abcdefgh"[$move->from_x] . $move->from_y }}</b>
        to
        <b>{{ " abcdefgh"[$move->from_x] . $move->from_y }}</b>
        @if ($move->capturedPiece)
        <span>capturing the</span>
        <b>{{ $move->capturedPiece->type }}</b>
        @else
        <span>making no captures</span>
        @endif
        @if ($move->promotion_type)
        <span>and promote to</span>
        <b>{{ $move->promotion_type }}</b>
        @endif
    </li>
    @endforeach
</ol>
@else
It is {{ $game->turn }}'s turn.
@if ($game->isUserPlaying(\Auth::user()))
    @if ($game->isTurnOfUser(\Auth::user()))
        <h2>It is your turn to move as {{ $game->turn }}</h2>
        <b>Your legal moves are as follows:</b>
        <form method="post" action="{{ route('paulichess.games.move', [$game->id]) }}">
            @csrf
            <div>
                @foreach($legalMoves as $move)
                <div>
                    <input 
                        type="radio"
                        id="move-{{$move->getSearchKey()}}"
                        name="move"
                        value="{{$move->getSearchKey()}}"
                        @if (count($legalMoves) == 1)
                        checked="checked"
                        @endif
                        />
                    <label for="move-{{$move->getSearchKey()}}">
                        <span>Move</span>
                        <b>{{ $move->movedPiece->type }}</b>
                        <span>from</span>
                        <b>{{ " abcdefgh"[$move->from_x] . $move->from_y }}</b>
                        <span>to</span>
                        <b>{{ " abcdefgh"[$move->to_x] . $move->to_y }}</b>
                        @if ($move->capturedPiece)
                        <span>capturing the</span>
                        <b>{{ $move->capturedPiece->type }}</b>
                        @else
                        <span>making no captures</span>
                        @endif
                        @if ($move->promotion_type)
                        <span>and promote to</span>
                        <b>{{ $move->promotion_type }}</b>
                        @endif
                    </label>
                </div>
                @endforeach
            </div>
            <div>
                <button class="btn btn-primary">Move</button>
            </div>
        </form>
    @else
        <div class="alert alert-info">Waiting on other player</div>
    @endif
@else
    <div class="alert alert-info">You are observing this game.</div>
@endif
@endif
</div>
@endsection

