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
                @foreach($board[$y][$x] as $piece)
                <div class="slot">{{ $piece->getSymbol() }}</div>
                @endforeach
                @foreach(range(count($board[$y][$x]), 1) as $i)
                <div class="slot">&nbsp;</div>
                @endforeach
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

<div>
@if ($game->isUserPlaying(\Auth::user()))
    @if ($game->isTurnOfUser(\Auth::user()))
        <h2>It is your turn!</h2>
    @else
        <div class="alert alert-info">Waiting on other player</div>
    @endif
@else
    <div class="alert alert-info">You are observing this game.</div>
@endif
</div>
@endsection

