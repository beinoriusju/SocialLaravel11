@extends('layouts.guest')
@section('title')
    {{ __('translations.Reset Password') }}
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

    <!-- Right Box (Reset Password Form) -->
    <div class="col-md-6 right-box">
      <div class="row align-items-center">
        <div class="header-text mb-4">
          <h2>{{ __('translations.Reset Password') }}</h2>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="input-group mb-3">
                <input type="email" id="email" name="email" placeholder="{{ __('translations.email') }}" value="{{ old('email', $request->email) }}" autocomplete="username" class="form-control form-control-lg bg-light fs-6" required autofocus>
            </div>
            @error('email')
              <div class="text-danger">{{ $message }}</div>
            @enderror

            <!-- Password -->
            <div class="input-group mb-3">
                <input type="password" id="password" name="password" placeholder="{{ __('translations.password') }}" autocomplete="new-password" class="form-control form-control-lg bg-light fs-6" required>
            </div>
            @error('password')
              <div class="text-danger">{{ $message }}</div>
            @enderror

            <!-- Confirm Password -->
            <div class="input-group mb-3">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('translations.confirm password') }}" autocomplete="new-password" class="form-control form-control-lg bg-light fs-6" required>
            </div>
            @error('password_confirmation')
              <div class="text-danger">{{ $message }}</div>
            @enderror

            <!-- Reset Password Button -->
            <div class="input-group mb-3">
                <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">{{ __('translations.Reset Password') }}</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
