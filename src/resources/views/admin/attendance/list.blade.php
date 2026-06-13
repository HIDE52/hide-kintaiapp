@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendance/list.css') }}">
@endsection

@section('content')
    <div class="admin-attendance-list">

        @if (session('success'))
            <div class="admin-attendance-list__alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="admin-attendance-list__title">{{ $titleDate }}の勤怠</h2>

        <div class="admin-attendance-list__nav">
            <a href="{{ route('admin.attendance.index', ['date' => $prevDate]) }}" class="admin-attendance-list__nav-btn">
                &larr; 前日
            </a>

            <form action="{{ route('admin.attendance.index') }}" method="GET" class="admin-attendance-list__date-form" id="dateForm">
                <div class="admin-attendance-list__input-container">
                    <input type="date" name="date" id="date-picker" value="{{ $formattedDateForPicker }}" onchange="document.getElementById('dateForm').submit();" class="admin-attendance-list__date-input">
                    <span class="admin-attendance-list__current-date-text">
                        {{ $displayDate }}
                    </span>
                </div>
            </form>

            @if($showNextButton)
                <a href="{{ route('admin.attendance.index', ['date' => $nextDate]) }}" class="admin-attendance-list__nav-btn">
                    翌日 &rarr;
                </a>
            @else
                <span class="admin-attendance-list__nav-btn" style="visibility: hidden;">翌日 &rarr;</span>
            @endif
        </div>

        <table class="admin-attendance-list-table">
            <thead>
                <tr class="admin-attendance-list-table__tr">
                    <th class="admin-attendance-list-table__th">名前</th>
                    <th class="admin-attendance-list-table__th">出勤</th>
                    <th class="admin-attendance-list-table__th">退勤</th>
                    <th class="admin-attendance-list-table__th">休憩</th>
                    <th class="admin-attendance-list-table__th">合計</th>
                    <th class="admin-attendance-list-table__th">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    <tr class="admin-attendance-list-table__tr">
                        <td class="admin-attendance-list-table__td admin-attendance-list-table__td--name">
                            {{ $attendance->user->name }}
                        </td>
                        <td class="admin-attendance-list-table__td">
                            {{ $attendance->punch_in ? \Carbon\Carbon::parse($attendance->punch_in)->format('H:i') : '' }}
                        </td>
                        <td class="admin-attendance-list-table__td">
                            {{ $attendance->punch_out ? \Carbon\Carbon::parse($attendance->punch_out)->format('H:i') : '' }}
                        </td>
                        <td class="admin-attendance-list-table__td">
                            {{ $attendance->punch_out ? $attendance->total_rest_time : '' }}
                        </td>
                        <td class="admin-attendance-list-table__td">
                            {{ $attendance->punch_out ? $attendance->total_work_time : '' }}
                        </td>
                        <td class="admin-attendance-list-table__td">
                            <a href="{{ route('admin.attendance.show', ['id' => $attendance->id]) }}" class="admin-attendance-list-table__detail-btn">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection