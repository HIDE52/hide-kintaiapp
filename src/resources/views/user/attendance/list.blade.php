@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/attendance/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <h2 class="attendance-list__title">勤怠一覧</h2>


    <div class="attendance-list__month-nav">
        <a href="#" class="month-nav__link">← 前月</a>
        <span class="month-nav__current">2026/05</span>
        <a href="#" class="month-nav__link">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>05/01(金)</td>
                <td>09:00</td>
                <td>18:00</td>
                <td>01:00</td>
                <td>08:00</td>
                <td>
                    <a href="/attendance/detail/1" class="btn--detail">詳細</a>
                </td>
            </tr>
            <tr>
                <td>05/02(土)</td>
                <td>08:30</td>
                <td>17:30</td>
                <td>01:00</td>
                <td>08:00</td>
                <td>
                    <a href="/attendance/detail/2" class="btn--detail">詳細</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection