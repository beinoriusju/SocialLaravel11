@extends('layouts.guest')
@section('title')
    {{ __('translations.Register') }}
@endsection
@section('content')

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="row border rounded-5 p-3 bg-white shadow box-area">
    <!-- Left Box -->
    <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #103cbe;">
      <div class="featured-image mb-3">
        <img src="{{asset('images/auth.jpg')}}" class="img-fluid" style="width: 250px;">
      </div>
    </div>

    <!-- Right Box (Register Form) -->
    <div class="col-md-6 right-box">
      <div class="row align-items-center">
        <div class="header-text mb-4">
          <h2>{{ __('translations.Register') }}</h2>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
          <!-- Name Field -->
          <div class="input-group mb-3">
            <input type="text" id="username" name="username" placeholder="{{ __('translations.Username') }}" value="{{ old('username') }}" autocomplete="username" class="form-control form-control-lg bg-light fs-6" required autofocus>
          </div>
          @error('username')
            <div class="text-danger">{{ $message }}</div>
          @enderror

          <!-- Email Field -->
          <div class="input-group mb-3">
            <input type="email" id="email" name="email" placeholder="{{ __('translations.email') }}" value="{{ old('email') }}" autocomplete="email" class="form-control form-control-lg bg-light fs-6" required>
          </div>
          @error('email')
            <div class="text-danger">{{ $message }}</div>
          @enderror

          <!-- Password Field -->
          <div class="input-group mb-3">
            <input type="password" id="password" name="password" placeholder="{{ __('translations.password') }}" autocomplete="new-password" class="form-control form-control-lg bg-light fs-6" required>
          </div>
          @error('password')
            <div class="text-danger">{{ $message }}</div>
          @enderror

          <!-- Password Confirmation Field -->
          <div class="input-group mb-3">
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('translations.confirm password') }}" autocomplete="new-password" class="form-control form-control-lg bg-light fs-6" required>
          </div>
          @error('password_confirmation')
            <div class="text-danger">{{ $message }}</div>
          @enderror

          <!-- Register Button -->
          <div class="input-group mb-3">
            <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">{{ __('translations.Register') }}</button>
          </div>
        </form>

        <!-- Already Registered Link -->
        <div class="row mt-4">
          <small>{{ __('translations.Already registered?') }} <a href="{{ route('login') }}">{{ __('translations.Login') }}</a></small>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
