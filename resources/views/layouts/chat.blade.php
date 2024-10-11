<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Vite integration to handle JS and CSS -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <!-- Additional Chat CSS -->
    <link rel="stylesheet" href="{{ asset('front/css/chat.css') }}">

    <!-- Livewire Styles (No longer explicitly included in Livewire 3 as per its new integration) -->
</head>
<body>
    <div class="position-relative">
        <!-- Main content -->
        @yield("content")
        <!-- End of main content -->
    </div>

    <!-- Livewire Scripts (No longer needed to explicitly add this in Livewire 3) -->

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('front/js/fslightbox.js') }}" defer></script>
    <script src="{{ asset('front/js/my.js') }}"></script>


</body>
</html>
