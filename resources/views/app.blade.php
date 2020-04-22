<!doctype html>
<html style="overflow: auto" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Team Portal</title>
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    </head>
    <body>
       <div id="app"></div>
       <script src="{{ mix('/js/manifest.js') }}"></script>
       <script src="{{ mix('/js/vendor.js') }}"></script>
       <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
