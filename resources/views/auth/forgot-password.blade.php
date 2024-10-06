@extends('layouts.guest')
@section('title')
    Forgot password
@endsection
@section('content')

<div class="preloader"></div>


 <div class="container d-flex justify-content-center align-items-center min-vh-100">
   <div class="row border rounded-5 p-3 bg-white shadow box-area">
   <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #103cbe;">
       <div class="featured-image mb-3">
        <img src="{{asset('images/auth.jpg')}}" class="img-fluid" style="width: 250px;">
       </div>
   </div>

   <div class="col-md-6 right-box">
      <div class="row align-items-center">
            <div class="header-text mb-4">
                 <h2>{{ __('translations.Forgot your password?') }}</h2>
                 <p>{{ __('translations.Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
            </div>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="{{ __('translations.Email') }}" value="{{ old('email') }}" required autofocus>
            </div>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            <div class="input-group mb-3">
                <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">{{ __('translations.Submit') }}</button>
            </div>
          </form>
          <div class="text-muted text-center mt-3">
              <h6>{{ __('translations.Back to') }} <a href="{{ route('login') }}">{{ __('translations.Login') }}</a></h6>
          </div>
      </div>
   </div>
  </div>
</div>
@endsection 
