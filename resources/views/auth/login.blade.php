@extends('layouts.app')

@section('title', __('messages.login'))

@section('extra-css')
<style>
    .login-box .lang-btn {
        border: 2px solid #1a237e !important;
        color: #1a237e !important;
        background: transparent !important;
    }

    .login-box .lang-btn.active,
    .login-box .lang-btn:hover {
        background: #1a237e !important;
        color: white !important;
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-box">
        <h2>{{ __('messages.login') }}</h2>
        
        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="error-message">
                {{ __('messages.login_error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="username">{{ __('messages.username') }}</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">{{ __('messages.password') }}</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                {{ __('messages.sign_in') }}
            </button>
        </form>

        <!-- Language Switcher -->
        <div style="margin-top: 20px; text-align: center;">
            <div class="lang-switch" style="justify-content: center;">
                <a href="{{ url()->current() }}?lang=en" class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ url()->current() }}?lang=id" class="lang-btn {{ app()->getLocale() == 'id' ? 'active' : '' }}">ID</a>
            </div>
        </div>
    </div>
</div>
@endsection