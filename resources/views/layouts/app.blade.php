<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel + Alpine Js</title>

    @vite(['resources/css/app.css'])


</head>

<body class="bg-gary-200 text-gray-800">


    <div class="container mx-auto mt-8 px-14">

        @yield('content')

    </div>


    @vite(['resources/js/app.js'])
    <!-- lucide icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    <!-- lucide icons -->

    @stack('scripts')

</body>

</html>
