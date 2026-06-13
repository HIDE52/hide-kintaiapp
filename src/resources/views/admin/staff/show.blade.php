@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff/show.css') }}">
@endsection

@section('content')
<div class="admin-staff-show">

    <h2 class="admin-staff-show__title"><span>{{ str_replace([' ', ' '], '', $staff->name) }}</span>さんの勤怠</h2>

    <div class="admin-staff-show__nav">
        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'tab' => $prevMonth]) }}" class="admin-staff-show__nav-btn">
            &larr; 前月
        </a>

        <form action="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}" method="GET" class="admin-staff-show__month-form" id="monthForm">
            <div class="admin-staff-show__input-container">
                <input type="month" name="tab" id="month-picker" value="{{ \Carbon\Carbon::parse($currentMonth)->format('Y-m') }}" onchange="document.getElementById('monthForm').submit();" class="admin-staff-show__month-input">
                <span class="admin-staff-show__current-month-text">
                    {{ \Carbon\Carbon::parse($currentMonth)->format('Y/m') }}
                </span>
            </div>
        </form>

        @if($showNextButton)
            <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'tab' => $nextMonth]) }}" class="admin-staff-show__nav-btn">
                翌月 &rarr;
            </a>
        @else
            <span class="admin-staff-show__nav-btn" style="visibility: hidden;">翌月 &rarr;</span>
        @endif
    </div>

    <table class="admin-staff-show__table">
        <thead>
            <tr class="admin-staff-show__tr-head">
                <th class="admin-staff-show__th">日付</th>
                <th class="admin-staff-show__th">出勤</th>
                <th class="admin-staff-show__th">退勤</th>
                <th class="admin-staff-show__th">休憩</th>
                <th class="admin-staff-show__th">合計</th>
                <th class="admin-staff-show__th">詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
                <tr class="admin-staff-show__tr">
                    <td class="admin-staff-show__td admin-staff-show__td--date">
                        {{ $attendance->display_date }}
                    </td>
                    <td class="admin-staff-show__td">
                        {{ $attendance->punch_in ? \Carbon\Carbon::parse($attendance->punch_in)->format('H:i') : '' }}
                    </td>
                    <td class="admin-staff-show__td">
                        {{ $attendance->punch_out ? \Carbon\Carbon::parse($attendance->punch_out)->format('H:i') : '' }}
                    </td>
                    <td class="admin-staff-show__td">
                        {{ $attendance->display_rest }}
                    </td>
                    <td class="admin-staff-show__td">
                        {{ $attendance->display_total }}
                    </td>
                    <td class="admin-staff-show__td">
                        @if($attendance->id)
                            <a href="{{ route('admin.attendance.show', ['id' => $attendance->id]) }}" class="admin-staff-show__detail-btn">
                                詳細
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="admin-staff-show__actions">
        <a href="{{ route('admin.staff.export', ['id' => $staff->id, 'tab' => $currentMonth]) }}" class="admin-staff-show__csv-btn">
            CSV出力
        </a>
    </div>

</div>
@endsection