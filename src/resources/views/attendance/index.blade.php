@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/index.css') }}">
@endsection

@section('content')
<div class="attendance">
    <div class="attendance__info">
        <p class="attendance__status">状態：{{ $status ?? '勤務外' }}</p>
        <h1 class="attendance__date">2026年05月18日(月) 14:06</h1>
    </div>

    @if(session('success'))
        <div class="attendance__alert" style="color: #ff0000; font-weight: bold; font-size: 18px; text-align: center; margin-bottom: 30px; width: 100%;">
            {{ session('success') }}
        </div>
    @endif

    <div class="attendance__card-grid">
        <form action="/attendance/start" method="POST" class="attendance__form" novalidate>
            @csrf
            <button type="submit" class="stamp-button stamp-button--attendance {{ ($status ?? '勤務外') !== '勤務外' ? 'is-disabled' : '' }}" {{ ($status ?? '勤務外' ) !== '勤務外' ? 'disabled' : '' }}>出勤</button>
        </form>

        <form action="/attendance/end" method="POST" class="attendance__form" novalidate>
            @csrf
            <button type="submit" class="stamp-button stamp-button--leave {{ ($status ?? '勤務外') !== '出勤中' ? 'is-disabled' : '' }}" {{ ($status ?? '勤務外' ) !== '出勤中' ? 'disabled' : '' }}>退勤</button>
        </form>

        <form action="/attendance/rest/start" method="POST" class="attendance__form" novalidate>
            @csrf
            <csrf></csrf>
            <button type="submit" class="stamp-button stamp-button--rest-start {{ ($status ?? '勤務外') !== '出勤中' ? 'is-disabled' : '' }}" {{ ($status ?? '勤務外' ) !== '出勤中' ? 'disabled' : '' }}>休憩入</button>
        </form>

        <form action="/attendance/rest/end" method="POST" class="attendance__form" novalidate>
            @csrf
            <button type="submit" class="stamp-button stamp-button--rest-end {{ ($status ?? '勤務外') !== '休憩中' ? 'is-disabled' : '' }}" {{ ($status ?? '勤務外' ) !== '休憩中' ? 'disabled' : '' }}>休憩戻</button>
        </form>
    </div>
</div>
@endsection