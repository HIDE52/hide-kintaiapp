@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/auth/login.css')}}">
@endsection

@section('content')
<div class="admin-login">

    <div class="admin-login__header">
        <h2 class="admin-login__title">管理者ログイン</h2>
    </div>

    @if (session('error'))
        <div class="admin-login__alert">
            <strong>{{ session('error') }}</strong>
        </div>
    @endif

    <div class="admin-login__body-content">
        <form class="admin-form" action="/login" method="post" novalidate>
            @csrf

            <div class="admin-form__group">
                <div class="admin-form__group-title">
                    <span class="admin-form__label">メールアドレス</span>
                </div>
                <div class="admin-form__group-content">
                    <input type="email" name="email" value="{{ old('email') }}" class="admin-form__input">
                    @error('email')
                    <div class="admin-form__error"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>

            <div class="admin-form__group">
                <div class="admin-form__group-title">
                    <span class="admin-form__label">パスワード</span>
                </div>
                <div class="admin-form__group-content">
                    <input type="password" name="password" class="admin-form__input">
                    @error('password')
                    <div class="admin-form__error"><strong>{{ $message }}</strong></div>
                    @enderror
                </div>
            </div>

            <div class="admin-login__btn-group">
                <button class="admin-login__btn-submit" type="submit">管理者ログインする</button>
            </div>
        </form>
    </div>
</div>
@endsection