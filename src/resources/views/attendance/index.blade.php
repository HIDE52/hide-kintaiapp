@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/index.css') }}">
@endsection

@section('content')
<div class="attendance">
    <div class="attendance__info">
        <span class="attendance__status-badge">{{ $status ?? '勤務外' }}</span>
        <p class="attendance__date">{{ $currentDate }}</p>
        <h1 class="attendance__time">{{ $currentTime ?? '08:00' }}</h1>
    </div>

    @if(session('success') && ($status ?? '勤務外') !== '退勤済')
        <div class="attendance__alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="attendance__button-container">
        @if(($status ?? '勤務外') === '勤務外')
            <form action="/attendance/start" method="POST" class="attendance__form" novalidate>
                @csrf
                <button type="submit" class="stamp-button stamp-button--black">出勤</button>
            </form>

        @elseif(($status ?? '勤務外') === '出勤中')
            <form action="/attendance/end" method="POST" class="attendance__form" novalidate>
                @csrf
                @method('PATCH')
                <button type="submit" class="stamp-button stamp-button--black">退勤</button>
            </form>

            <form action="/attendance/rest/start" method="POST" class="attendance__form" novalidate>
                @csrf
                <button type="submit" class="stamp-button stamp-button--outline-gray">休憩入</button>
            </form>

        @elseif(($status ?? '勤務外') === '休憩中')
            <form action="/attendance/rest/end" method="POST" class="attendance__form" novalidate>
                @csrf
                @method('PATCH')
                <button type="submit" class="stamp-button stamp-button--outline-gray">休憩戻</button>
            </form>

        @elseif(($status ?? '勤務外') === '退勤済')
            <p class="attendance__thanks-text">お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection