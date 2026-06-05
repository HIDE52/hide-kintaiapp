@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
@endsection

@section('content')
    <div class="attendance-list">

        <div class="attendance-list__nav">
            <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="attendance-list__nav-btn">
                &larr; 前月
            </a>

            <form action="{{ route('attendance.list') }}" method="GET" class="attendance-list__month-form" id="monthForm">
                <div class="attendance-list__input-container">
                    <input type="month" name="month" id="month-picker" value="{{ \Carbon\Carbon::parse($currentMonth)->format('Y-m') }}" onchange="document.getElementById('monthForm').submit();" class="attendance-list__month-input">
                    <span class="attendance-list__current-month-text">
                        {{ \Carbon\Carbon::parse($currentMonth)->format('Y/m') }}
                    </span>
                </div>
            </form>

            @if($showNextButton)
                <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="attendance-list__nav-btn">
                    翌月 &rarr;
                </a>
            @else
                <span class="attendance-list__nav-btn" style="visibility: hidden;">翌月 &rarr;</span>
            @endif
        </div>

        <table class="attendance-list-table">
            <thead>
                <tr>
                    <th class="attendance-list-table__th">日付</th>
                    <th class="attendance-list-table__th">出勤</th>
                    <th class="attendance-list-table__th">退勤</th>
                    <th class="attendance-list-table__th">休憩</th>
                    <th class="attendance-list-table__th">合計</th>
                    <th class="attendance-list-table__th">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    @php
                        $carbonDate = \Carbon\Carbon::parse($attendance->date);
                        $weeks = ['日', '月', '火', '水', '木', '金', '土'];
                        $weekStr = $weeks[$carbonDate->dayOfWeek];
                    @endphp
                    <tr class="attendance-list-table__tr">
                        <td class="attendance-list-table__td">{{ $carbonDate->format('m/d') }}({{ $weekStr }})</td>
                        <td class="attendance-list-table__td">
                            {{ $attendance->punch_in ? \Carbon\Carbon::parse($attendance->punch_in)->format('H:i') : '' }}
                        </td>
                        <td class="attendance-list-table__td">
                            {{ $attendance->punch_out ? \Carbon\Carbon::parse($attendance->punch_out)->format('H:i') : '' }}
                        </td>
                        <td class="attendance-list-table__td">
                            {{ $attendance->display_rest }}
                        </td>
                        <td class="attendance-list-table__td">
                            {{ $attendance->display_total }}
                        </td>
                        <td class="attendance-list-table__td">
                            <a href="{{ route('attendance.show', ['id' => $attendance->id]) }}" class="attendance-list-table__detail-btn">
                                詳細
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection