@extends('base')

@section('content')
<table style="width: 100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>White</th>
            <th>Black</th>
            <th>View</th>
        </tr>
    </thead>
@foreach($games as $game)
    <tr>
        <td>{{ $game->id }}</td>
        <td>
            @if ($game->getWhitePlayer())
            {{ $game->getWhitePlayer()->user->name }}
            @else
            Nobody
            @endif
        </td>
        <td>
            @if ($game->getBlackPlayer())
            {{ $game->getBlackPlayer()->user->name }}
            @else
            Nobody
            @endif
        </td>
        <td>
            <a href="{{ route('paulichess.games.show', [$game->id]) }}">View Game</a>
        </td>
    </tr>
@endforeach
</table>
@endsection

