@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff/show.css') }}">
@endsection

@section('content')
<div class="admin-staff">

    <h2 class="admin-staff__title">
        <span>{{ str_replace([' ', '　'], '', $staff->name) }}</span>さんの勤怠
    </h2>

    <div class="admin-staff__nav">
        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'tab' => $prevMonth]) }}" class="admin-staff__nav-btn admin-staff__nav-btn--prev">
            &larr; 前月
        </a>

        <form action="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}" method="GET" class="admin-staff__month-form" id="monthForm">
            <div class="admin-staff__input-container">
                <input type="month" name="tab" id="month-picker" value="{{ $currentMonth }}" onchange="document.getElementById('monthForm').submit();" class="admin-staff__month-input">
                <span class="admin-staff__current-month-text">
                    {{ \Carbon\Carbon::parse($currentMonth)->format('Y/m') }}
                </span>
            </div>
        </form>

        @if($showNextButton)
            <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'tab' => $nextMonth]) }}" class="admin-staff__nav-btn admin-staff__nav-btn--next">
                翌月 &rarr;
            </a>
        @else
            <span class="admin-staff__nav-btn admin-staff__nav-btn--next" style="visibility: hidden;">翌月 &rarr;</span>
        @endif
    </div>

    <table class="admin-staff-table">
        <thead>
            <tr class="admin-staff-table__tr-head">
                <th class="admin-staff-table__th">日付</th>
                <th class="admin-staff-table__th">出勤</th>
                <th class="admin-staff-table__th">退勤</th>
                <th class="admin-staff-table__th">休憩</th>
                <th class="admin-staff-table__th">合計</th>
                <th class="admin-staff-table__th">詳細</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($attendances) && count($attendances) > 0)
                @foreach($attendances as $attendance)
                    @php
                        $carbonDate = \Carbon\Carbon::parse($attendance->date);
                        $weeks = ['日', '月', '火', '水', '木', '金', '土'];
                        $weekStr = $weeks[$carbonDate->dayOfWeek];
                    @endphp
                    <tr class="admin-staff-table__tr">
                        <td class="admin-staff-table__td">{{ $carbonDate->format('m/d') }}({{ $weekStr }})</td>
                        <td class="admin-staff-table__td">
                            {{ $attendance->punch_in ? \Carbon\Carbon::parse($attendance->punch_in)->format('H:i') : '' }}
                        </td>
                        <td class="admin-staff-table__td">
                            {{ $attendance->punch_out ? \Carbon\Carbon::parse($attendance->punch_out)->format('H:i') : '' }}
                        </td>
                        <td class="admin-staff-table__td">
                            {{ $attendance->punch_in && $attendance->punch_out ? $attendance->total_rest_time : '' }}
                        </td>
                        <td class="admin-staff-table__td">
                            {{ $attendance->total_work_time }}
                        </td>
                        <td class="admin-staff-table__td">
                            <a href="{{ route('admin.attendance.show', ['id' => $attendance->id]) }}" class="admin-staff-table__detail-btn">
                                詳細
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="admin-staff-table__empty">
                        勤怠データが登録されていません。
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="admin-staff__actions">
        <a href="{{ route('admin.staff.export', ['id' => $staff->id, 'tab' => $currentMonth]) }}" class="admin-staff__csv-btn">
            CSV出力
        </a>
    </div>

</div>
@endsection