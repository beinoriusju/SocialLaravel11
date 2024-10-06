@extends('layouts.guest')
@section('title')
    {{ __('translations.Verify Email') }}
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

    <!-- Right Box (Email Verification) -->
    <div class="col-md-6 right-box">
      <div class="row align-items-center">
        <div class="header-text mb-4">
          <h2>{{ __('translations.Verify Email') }}</h2>
        </div>

        <div class="card-text p-4">
          <p>
            {{ __('translations.Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you did not receive the email, we will gladly send you another.') }}
          </p>

          @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
              {{ __('translations.A new verification link has been sent to the email address you provided during registration.') }}
            </div>
          @endif

          <!-- Resend Verification Form -->
          <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="input-group mb-3">
              <button class="btn btn-lg btn-primary w-100 fs-6" type="submit">{{ __('translations.Resend Verification Email') }}</button>
            </div>
          </form>
          <div class="row mt-4">
            <small>{{ __('translations.Already verified?') }} <a href="{{ route('login') }}">{{ __('translations.Login') }}</a></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection 
