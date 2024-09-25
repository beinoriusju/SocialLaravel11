@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="text-center">
        <div class="alert alert-info" role="alert">
            {{ __('Your account will be activated by admin in 48 hours.') }}
        </div>
        <div class="alert alert-info" role="alert">
            {{ __('Adminas aktyvuos jūsų paskyrą per 48 valandas') }}
        </div>
    </div>
</div>
@endsection
