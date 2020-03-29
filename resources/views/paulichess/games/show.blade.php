@extends('base')

@section('content')
<table>
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
                <span>{{ $piece->getSymbol() }}</span>
                @endforeach
            </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

