<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery CDN -->
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/chat.css') }}">

    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body>
        <div class="position-relative">
            <!-- Main content -->
            @yield("content")
            <!-- End of main content -->
        </div>
    <!-- Livewire Scripts -->
    @livewireScripts



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
