<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/app.css">
        <script src="/js/app.js"></script>
        <style>
            .chessboard td.white-square {
                background: #eeeeee;
            }

            .chessboard td.black-square {
                background: #aaaaaa;
            }

            .chessboard td {
                height: 3em;
                width:  3em;
                text-align: center;
            }

            .chessboard .slot {
                height: 2em;
                width: 1em;
                box-shadow: 0px 0px 1px 1px gray;
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Pauli Chess</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="nav navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ route('paulichess.games.index') }}">All Games</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    @if(\Auth::user())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            You are logged in as {{ \Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li class="dropdown-item">
                                <a href="/paulichess/my-active-games">My Active Games</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="/paulichess/my-active-games">My Past Games</a>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li class="dropdown-item">
                                <form method="post" action="/logout" class="form-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link pl-0">Log Out</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            You are not logged in
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li class="dropdown-item">
                                <a href="/login">Log In</a>
                            </li>
                            <li class="dropdown-item">
                                <a href="/register">Register</a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </div>
        </nav>
        <div class="container">
            @section('content')
            @show
            <div class="alert alert-warning">
                <p>This is an unfinished game. Known limitations include, but are not limited to the following:</p>
                <p>This game was inspired by <a href="https://blog.plover.com/games/double-chess.html">this blog post</a></p>
                <ul>
                    <li>Pawns cannot capture en-passant.</li>
                    <li>You cannot castle.</li>
                    <li>There is no warning that you are in check.</li>
                    <li>Likewise, nothing stops you from moving into check.</li>
                    <li>The game ends when a king is captured. Stalemate is not a thing.</li>
                    <li>The "if you make the same series of moves 3 times it's stalemate" rule does not apply.</li>
                </ul>
                <p>If you run across a bug, you can file a bug report <a href="https://github.com/JoshuaDavid/paulichess/issues/new">here</a>.</p>
                <p>If you would like to contribute, or just want to see how this was made, <a href="">check the project out on GitHub</a>.</p>
            </div>
        </div>
    </body>
</html>

