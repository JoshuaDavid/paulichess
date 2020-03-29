<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            table {
                border-collapse: collapse;
                table-layout: fixed;
                border: 1px solid black;
            }

            td, th {
                border: 1px solid black;
            }

            td.white-square {
                background: #eeeeee;
            }

            td.black-square {
                background: #aaaaaa;
            }
        </style>
    </head>
    <body>
        @section('content')
        @show
    </body>
</html>

