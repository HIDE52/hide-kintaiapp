@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendance/detail.css') }}">
@endsection

@section('content')
<div class="admin-detail">
    <h2 class="admin-detail__title">勤怠詳細</h2>

    @if($isPending)
        <div class="admin-detail__alert-pending">
            承認待ちのため修正はできません。
        </div>
    @endif

    @if(session('error'))
        <div class="admin-detail__alert-error">
            {{ session('error') }}
        </div>
    @endif

    <table class="admin-detail-table">
        <form action="/admin/attendance/{{ $attendance->id }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            
            <tbody>

                <tr class="admin-detail-table__tr">
                    <td class="admin-detail-table__td admin-detail-table__label">名前</td>
                    <td class="admin-detail-table__td admin-detail-table__content admin-detail-table__content--relative">
                        <div class="admin-detail__name-group">
                            <div class="admin-detail__name-box-absolute">
                                {{ $attendance->user->name }}
                            </div>
                        </div>
                    </td>
                    <td class="admin-detail-table__td admin-detail-table__empty"></td>
                </tr>

                @php
                    $carbonDate = \Carbon\Carbon::parse($attendance->date);
                @endphp
                <tr class="admin-detail-table__tr">
                    <td class="admin-detail-table__td admin-detail-table__label">日付</td>
                    <td class="admin-detail-table__td admin-detail-table__content">
                        <div class="admin-detail__date-group">
                            <div class="admin-detail__date-box-year">
                                {{ $carbonDate->format('Y年') }}
                            </div>
                            <span class="admin-detail__separator-invisible">〜</span>
                            <div class="admin-detail__date-box-month-day">
                                {{ $carbonDate->format('m月d日') }}
                            </div>
                        </div>
                    </td>
                    <td class="admin-detail-table__td admin-detail-table__empty"></td>
                </tr>

                <tr class="admin-detail-table__tr">
                    <td class="admin-detail-table__td admin-detail-table__label">出勤・退勤</td>
                    <td class="admin-detail-table__td admin-detail-table__content">
                        <div class="admin-detail__time-group">
                            <input type="text" name="punch_in" class="admin-detail__input-time" value="{{ old('punch_in', \Carbon\Carbon::parse($attendance->punch_in)->format('H:i')) }}" {{ $isPending ? 'disabled' : '' }}>
                            <span class="admin-detail__separator">〜</span>
                            <input type="text" name="punch_out" class="admin-detail__input-time" value="{{ old('punch_out', $attendance->punch_out ? \Carbon\Carbon::parse($attendance->punch_out)->format('H:i') : '') }}" {{ $isPending ? 'disabled' : '' }}>
                        </div>
                        @error('punch_in')
                            <div class="admin-detail__error-message">{{ $message }}</div>
                        @enderror
                        @error('punch_out')
                            <div class="admin-detail__error-message">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="admin-detail-table__td admin-detail-table__empty"></td>
                </tr>

                @foreach($attendance->rests as $index => $rest)
                <tr class="admin-detail-table__tr">
                    <td class="admin-detail-table__td admin-detail-table__label">
                        休憩{{ $index + 1 }}
                    </td>
                    <td class="admin-detail-table__td admin-detail-table__content">
                        <div class="admin-detail__time-group">
                            <input type="text" name="rest_id[{{ $index }}][break_in]" class="admin-detail__input-time" value="{{ old('rest_id.' . $index . '.break_in', \Carbon\Carbon::parse($rest->break_in)->format('H:i')) }}" {{ $isPending ? 'disabled' : '' }}>
                            <span class="admin-detail__separator">〜</span>
                            <input type="text" name="rest_id[{{ $index }}][break_out]" class="admin-detail__input-time" value="{{ old('rest_id.' . $index . '.break_out', $rest->break_out ? \Carbon\Carbon::parse($rest->break_out)->format('H:i') : '') }}" {{ $isPending ? 'disabled' : '' }}>
                        </div>
                        @error('rest_id.' . $index . '.break_in')
                            <div class="admin-detail__error-message">{{ $message }}</div>
                        @enderror
                        @error('rest_id.' . $index . '.break_out')
                            <div class="admin-detail__error-message">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="admin-detail-table__td admin-detail-table__empty"></td>
                </tr>
                @endforeach

                <tr class="admin-detail-table__tr">
                    <td class="admin-detail-table__td admin-detail-table__label">
                        休憩{{ count($attendance->rests) + 1 }}
                    </td>
                    <td class="admin-detail-table__td admin-detail-table__content">
                        <div class="admin-detail__time-group">
                            <input type="text" name="rest_id[{{ count($attendance->rests) }}][break_in]" class="admin-detail__input-time" value="{{ old('rest_id.' . count($attendance->rests) . '.break_in') }}" placeholder="00:00" {{ $isPending ? 'disabled' : '' }}>
                            <span class="admin-detail__separator">〜</span>
                            <input type="text" name="rest_id[{{ count($attendance->rests) }}][break_out]" class="admin-detail__input-time" value="{{ old('rest_id.' . count($attendance->rests) . '.break_out') }}" placeholder="00:00" {{ $isPending ? 'disabled' : '' }}>
                        </div>
                    </td>
                    <td class="admin-detail-table__td admin-detail-table__empty"></td>
                </tr>

                <tr class="admin-detail-table__tr">
                    <td class="admin-detail-table__td admin-detail-table__label admin-detail-table__label--valign-top">備考</td>
                    <td class="admin-detail-table__td admin-detail-table__content">
                        <textarea name="remark" class="admin-detail__textarea" {{ $isPending ? 'disabled' : '' }}>{{ old('remark', $attendance->note) }}</textarea>
                        @error('remark')
                            <div class="admin-detail__error-message">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="admin-detail-table__td admin-detail-table__empty"></td>
                </tr>

                <tr class="admin-detail-table__tr-button">
                    <td colspan="3" class="admin-detail-table__td-button">
                        <button type="submit" class="admin-detail__submit-btn {{ $isPending ? 'admin-detail__submit-btn--disabled' : '' }}" {{ $isPending ? 'disabled' : '' }}>修正</button>
                    </td>
                </tr>

            </tbody>
        </form>
    </table>
</div>
@endsection