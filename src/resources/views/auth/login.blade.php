@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endsection

@section('content')
<div class="login-form">

    <div class="login-form__header">
        <h2 class="login-form__title">ログイン</h2>
    </div>

    <div class="login-form__body-content">
        <form class="form" action="/login" method="post" novalidate>
            @csrf

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">メールアドレス</span>
                </div>
                <div class="form__group-content">
                    <input type="email" name="email" value="{{ old('email') }}" class="form__input">
                    @error('email')
                    <div class="form__error"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">パスワード</span>
                </div>
                <div class="form__group-content">
                    <input type="password" name="password" class="form__input">
                    @error('password')
                    <div class="form__error"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>

            <div class="login-form__btn-group">
                <button class="login-form__btn-submit" type="submit">ログインする</button>
            </div>
        </form>

        <div class="login-form__link">
            <a href="/register">会員登録はこちら</a>
        </div>
    </div>
</div>
@endsection