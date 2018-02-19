<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Dienstplan für den Responder einer Feuerwehr">
        <meta name="theme-color" content="#E91C23">

        <title>@yield('title') - FRS Irgendwo</title>
        <link rel="shortcut icon" href='{{ asset('favicon.ico') }}'>

        <link rel="stylesheet" href="{{ mix('css/main.css') }}">
        @stack('early')
    </head>

    <body>
        <div id="layout">
            @include('layouts.menu')
            <div id="x-scrollbox">
                <div id="main">
                    <div class="content">
                        @yield('content')
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>

        <link rel="stylesheet" href="{{ mix('css/late.css') }}">
        @stack('late')
    </body>
</html>
