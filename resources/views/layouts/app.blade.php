<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Meta Information -->
    <meta name="description" content="A brief description of the page or website.">
    <meta name="keywords" content="your, keywords, go, here">
    <meta name="author" content="Your Name or Company Name">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.1/echo.iife.min.js"></script> -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('front/css/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/socialv.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/my.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
</head>
<body>
    <!-- Loader Start -->
    <div id="loading">
        <div id="loading-center"></div>
    </div>
    <!-- Loader END -->

    <!-- Wrapper Start -->
    @include("layouts.sidebar")

    <main class="main-content">
        <div class="position-relative">
            @include("layouts.navigation")

            <!-- Main content -->
            @yield("content")
            <!-- End of main content -->
        </div>

        <!-- @include("layouts.rightbar") -->
    </main>

    <!-- JavaScript Libraries -->
    <script src="{{ asset('front/js/libs.min.js') }}"></script>
    <script src="{{ asset('front/js/lodash.min.js') }}"></script>
    <script src="{{ asset('front/js/utility.js') }}"></script>
    <script src="{{ asset('front/js/setting.js') }}"></script>
    <script src="{{ asset('front/js/setting-init.js') }}" defer></script>
    <script src="{{ asset('front/js/slider.js') }}"></script>
    <script src="{{ asset('front/js/masonry.pkgd.min.js') }}"></script>
    <script src="{{ asset('front/js/enchanter.js') }}"></script>
    <script src="{{ asset('front/js/sweetalert2.min.js') }}" async></script>
    <script src="{{ asset('front/js/sweet-alert.js') }}" defer></script>
    <script src="{{ asset('front/js/apps.js') }}"></script>
    <script src="{{ asset('front/js/flatpickr.min.js') }}"></script>
    <script src="{{ asset('front/js/fslightbox.js') }}" defer></script>
    <script src="{{ asset('front/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('front/js/lottie.js') }}"></script>
    <script src="{{ asset('front/js/select2.js') }}"></script>
    <script src="{{ asset('front/js/ecommerce.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('front/js/my.js') }}"></script>


</body>
</html>
