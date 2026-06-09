@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css')}}">
@endsection

@section('content')
<div class="verification__container">
    <div class="verification__card">
        <p class="verification__text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        <div class="verification__action">
            <a href="http://localhost:8025" target="_blank" class="btn__verify">
                認証はこちらから
            </a>
        </div>

        <form class="verification__resend" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn__resend-link">
                認証メールを再送する
            </button>
        </form>

        @if (session('status') == 'verification-link-sent')
            <p class="verification__alert">
                新しい認証メールを送信しました。
            </p>
        @endif
    </div>
</div>
@endsection