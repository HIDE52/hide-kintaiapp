@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/index.css') }}">
@endsection

@section('content')
<div class="attendance-stamp">
    <div class="attendance-stamp__info">
        <span class="attendance-stamp__status-badge">{{ $status ?? '勤務外' }}</span>
        <p class="attendance-stamp__date">{{ $currentDate }}</p>
        <h1 class="attendance-stamp__time">{{ $currentTime ?? '08:00' }}</h1>
    </div>

    @if($hasForgotEndStamp)
        <div class="attendance-stamp__alert-forgot">
            前日の退勤打刻がされていません。<br>
            <a href="{{ route('attendance.detail', ['id' => $yesterdayId]) }}" class="attendance-stamp__alert-link">
                勤怠詳細画面から修正申請を行ってください。
            </a>
        </div>
    @endif

    @if(session('success') && ($status ?? '勤務外') !== '退勤済')
        <div class="attendance-stamp__alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="attendance-stamp__btn-container">
        @if(($status ?? '勤務外') === '勤務外')
            <form action="/attendance/start" method="POST" class="attendance-stamp__form" novalidate>
                @csrf
                <button type="submit" class="attendance-stamp__btn attendance-stamp__btn--black" {{ $hasForgotEndStamp ? 'disabled' : '' }}>出勤</button>
            </form>

        @elseif(($status ?? '勤務外') === '出勤中')
            <form action="/attendance/end" method="POST" class="attendance-stamp__form" novalidate>
                @csrf
                @method('PATCH')
                <button type="submit" class="attendance-stamp__btn attendance-stamp__btn--black">退勤</button>
            </form>

            <form action="/attendance/rest/start" method="POST" class="attendance-stamp__form" novalidate>
                @csrf
                <button type="submit" class="attendance-stamp__btn attendance-stamp__btn--outline-gray">休憩入</button>
            </form>

        @elseif(($status ?? '勤務外') === '休憩中')
            <form action="/attendance/rest/end" method="POST" class="attendance-stamp__form" novalidate>
                @csrf
                @method('PATCH')
                <button type="submit" class="attendance-stamp__btn attendance-stamp__btn--outline-gray">休憩戻</button>
            </form>

        @elseif(($status ?? '勤務外') === '退勤済')
            <p class="attendance-stamp__thanks-text">お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection