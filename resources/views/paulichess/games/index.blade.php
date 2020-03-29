@extends('base')

@section('content')
<h1>Games</h1>
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Players</th>
            <th>Actions</th>
        </tr>
    </thead>
@foreach($games as $game)
    <tr>
        <td>{{ $game->id }}</td>
        <td>
            @foreach ($game->players as $player)
            <div>
            {{ $player->user->name }}
            @if ($player->user->id == \Auth::user()->id)
            <b>(you)</b>
            @endif
            @if (in_array($player->color, ['white', 'black']))
            ({{ $player->color }})
            @endif
            </div>
            @endforeach
        </td>
        <td>
            <a href="{{ route('paulichess.games.show', [$game->id]) }}">View Game</a>
            @if (count($game->players) == 1)
            <form method="post" action="{{ route('paulichess.games.join', [$game->id]) }}">
                @csrf
                <button class="btn btn-link pl-0">Join Game</button>
            </form>
            @endif
        </td>
    </tr>
@endforeach
</table>
<form method="post" action="{{ route('paulichess.games.store') }}">
    @csrf
    <button class="btn btn-primary" type="submit">Start a new game</button>
</form>
{!! $games->links() !!}
@endsection

