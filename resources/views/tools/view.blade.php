<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    <!-- <meta http-equiv="refresh" content="3"> -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    <main>
        @foreach ($messages as $msg)
        <div class="p-2 rounded {{ $msg['style'] }}">
            <span>{{$msg['created_at']}}</span> {{$msg['username']}} : {{$msg['content']}}
        </div>
        @endforeach

    </main>
</body>

</html>
