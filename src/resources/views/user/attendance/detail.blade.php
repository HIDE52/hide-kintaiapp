@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/attendance_detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <h2 class="attendance-detail__title">勤怠詳細</h2>

    <div class="attendance-detail__card">
        <div class="attendance-detail__status">
            @if(request('status') === 'pending')
                <span class="status-badge status-badge--pending">承認待ち</span>
            @endif
        </div>

        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>テスト 太郎</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>2026年5月1日</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <div class="time-input-group">
                        <input type="text" value="09:00" class="input-time" {{ request('status') === 'pending' ? 'readonly' : '' }}>
                        <span>〜</span>
                        <input type="text" value="18:00" class="input-time" {{ request('status') === 'pending' ? 'readonly' : '' }}>
                    </div>
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td>
                    <input type="text" value="01:00" class="input-time" {{ request('status') === 'pending' ? 'readonly' : '' }}>
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td>
                    <textarea class="input-textarea" {{ request('status') === 'pending' ? 'readonly' : '' }}>電車遅延のため</textarea>
                </td>
            </tr>
        </table>

        <div class="detail-action">
            @if(request('status') === 'pending')
                <p class="detail-message">※承認待ちのため、修正はできません。</p>
            @else
                <a href="/attendance/detail/1?status=pending" class="btn--update">修正</a>
            @endif
        </div>
    </div>
</div>
@endsection