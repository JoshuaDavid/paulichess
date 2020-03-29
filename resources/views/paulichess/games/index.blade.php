@extends('base')

@section('content')
<h1>Games</h1>
<table class="table table-bordered table-hover">
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
<form method="post" action="">
    <button class="btn btn-primary" type="submit">Start a new game</button>
</form>
{!! $games->links() !!}
@endsection

