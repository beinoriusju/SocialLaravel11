@extends('layouts.guest')
@section('title')
    {{ __('translations.Login') }}
@endsection
@section('content')

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
                <h2>{{ __('translations.Login') }}</h2>
           </div>
           <form method="POST" action="{{ route('login') }}">
               @csrf
           <div class="input-group mb-3">
               <input type="text" id="email" class="form-control" name="email" placeholder="{{ __('translations.email') }}" value="{{ old('email') }}" autocomplete="email" class="form-control form-control-lg bg-light fs-6" required autofocus>
           </div>
           @error('email')
               <div class="text-danger">{{ __('validation.email') }}</div>
           @enderror
           <div class="input-group mb-3">
               <input type="password" id="password" name="password" placeholder="{{ __('translations.password') }}" autocomplete="current-password" class="form-control form-control-lg bg-light fs-6">
           </div>
           @error('password')
               <div class="text-danger">{{ __('validation.password') }}</div>
           @enderror
           <div class="input-group mb-3">
               <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">{{ __('translations.Login') }}</button>
           </div>
         <div class="input-group mb-5 d-flex justify-content-between">
             <div class="form-check">
                 <input type="checkbox" id="remember_me" name="remember">
                 <label for="formCheck" class="form-check-label text-secondary"><small>{{ __('translations.remember me') }}</small></label>
             </div>
             <div class="forgot">
                 <small><a href="{{ route('password.request') }}">{{ __('translations.Forgot your password?') }}</a></small>
             </div>
         </div>
       </form>
       <div class="row">
           <small>{{ __("translations.Don't have account?") }} <a href="{{ route('register') }}">{{ __('translations.Register') }}</a></small>
       </div>
     </div>
  </div>
 </div>
</div>

@endsection
