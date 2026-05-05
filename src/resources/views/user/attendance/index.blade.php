@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/attendance/index.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="attendance-card">
        <div class="attendance__status">
            @if(request('status') === 'finished')
                <span class="status-label">退勤済</span>
            @endif
        </div>

        <div class="attendance__display">
            <h2 class="attendance__date">2026年5月5日(火)</h2>
            <h1 class="attendance__time">08:00</h1>
        </div>

        <div class="attendance__actions">
            @if(request('status') === 'finished')
                <p class="attendance__message">お疲れ様でした。</p>
            @else
                <div class="button-row">
                    <a href="#" class="btn btn--black">出勤</a>
                    <a href="/attendance?status=finished" class="btn btn--black">退勤</a>
                </div>
                <div class="button-row">
                    <a href="#" class="btn btn--black">休憩開始</a>
                    <a href="#" class="btn btn--black">休憩終了</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection