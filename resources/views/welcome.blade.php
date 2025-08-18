<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>


        @vite(['resources/css/app.css', 'resources/js/app.js'])



    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">

        <div x-data="{ count: 0 }">
            <button class="btn bg-gray-200" @click="count++">Add</button>
            <span x-text="count" class="text-2xl"></span>
        </div>

        <p x-text="'abc'" class="text-white"></p>

        @vite(['resources/js/app.js'])

    </body>
</html>
