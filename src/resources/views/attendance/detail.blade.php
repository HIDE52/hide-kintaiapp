@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section('content')
    <div class="attendance-detail">
        <h2 class="attendance-detail__title">勤怠詳細</h2>

        <table class="attendance-detail-table">
            <form action="/attendance/detail/{{ $attendance->id }}" method="POST" novalidate>
                @csrf
                <tbody>
                    
                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td attendance-detail-table__label">名前</td>
                        <td class="attendance-detail-table__td attendance-detail-table__content attendance-detail-table__content--relative">
                            <div class="attendance-detail__name-group">
                                <div class="attendance-detail__name-box-absolute">
                                    {{ $attendance->user->name }}
                                </div>
                            </div>
                        </td>
                        <td class="attendance-detail-table__td attendance-detail-table__empty"></td>
                    </tr>

                    @php
                        $carbonDate = \Carbon\Carbon::parse($attendance->date);
                    @endphp
                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td attendance-detail-table__label">日付</td>
                        <td class="attendance-detail-table__td attendance-detail-table__content">
                            <div class="attendance-detail__date-group">
                                <div class="attendance-detail__date-box-year">
                                    {{ $carbonDate->format('Y年') }}
                                </div>
                                <span class="attendance-detail__separator-invisible">〜</span>
                                <div class="attendance-detail__date-box-month-day">
                                    {{ $carbonDate->format('m月d日') }}
                                </div>
                            </div>
                        </td>
                        <td class="attendance-detail-table__td attendance-detail-table__empty"></td>
                    </tr>

                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td attendance-detail-table__label">出勤・退勤</td>
                        <td class="attendance-detail-table__td attendance-detail-table__content">
                            <div class="attendance-detail__time-group">
                                <input type="text" name="punch_in" class="attendance-detail__input-time" value="{{ old('punch_in', \Carbon\Carbon::parse($attendance->punch_in)->format('H:i')) }}" {{ $isPending ? 'readonly' : '' }}>
                                <span class="attendance-detail__separator">〜</span>
                                <input type="text" name="punch_out" class="attendance-detail__input-time" value="{{ old('punch_out', $attendance->punch_out ? \Carbon\Carbon::parse($attendance->punch_out)->format('H:i') : '') }}" {{ $isPending ? 'readonly' : '' }}>
                            </div>
                            @error('punch_in')
                                <div class="attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                            @error('punch_out')
                                <div class="attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                        </td>
                        <td class="attendance-detail-table__td attendance-detail-table__empty"></td>
                    </tr>

                    @foreach($attendance->rests as $index => $rest)
                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td attendance-detail-table__label">
                            休憩{{ $index + 1 }}
                        </td>
                        <td class="attendance-detail-table__td attendance-detail-table__content">
                            <div class="attendance-detail__time-group">
                                <input type="text" name="rest_id[{{ $index }}][break_in]" class="attendance-detail__input-time" value="{{ old('rest_id.' . $index . '.break_in', \Carbon\Carbon::parse($rest->break_in)->format('H:i')) }}" {{ $isPending ? 'readonly' : '' }}>
                                <span class="attendance-detail__separator">〜</span>
                                <input type="text" name="rest_id[{{ $index }}][break_out]" class="attendance-detail__input-time" value="{{ old('rest_id.' . $index . '.break_out', $rest->break_out ? \Carbon\Carbon::parse($rest->break_out)->format('H:i') : '') }}" {{ $isPending ? 'readonly' : '' }}>
                            </div>
                            @error('rest_id.' . $index . '.break_in')
                                <div class="attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                            @error('rest_id.' . $index . '.break_out')
                                <div class="attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                        </td>
                        <td class="attendance-detail-table__td attendance-detail-table__empty"></td>
                    </tr>
                    @endforeach

                    @if(!$isPending)
                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td attendance-detail-table__label">
                            休憩{{ count($attendance->rests) + 1 }}
                        </td>
                        <td class="attendance-detail-table__td attendance-detail-table__content">
                            <div class="attendance-detail__time-group">
                                <input type="text" name="rest_id[{{ count($attendance->rests) }}][break_in]" class="attendance-detail__input-time" value="{{ old('rest_id.' . count($attendance->rests) . '.break_in') }}" placeholder="00:00">
                                <span class="attendance-detail__separator">〜</span>
                                <input type="text" name="rest_id[{{ count($attendance->rests) }}][break_out]" class="attendance-detail__input-time" value="{{ old('rest_id.' . count($attendance->rests) . '.break_out') }}" placeholder="00:00">
                            </div>
                        </td>
                        <td class="attendance-detail-table__td attendance-detail-table__empty"></td>
                    </tr>
                    @endif

                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td attendance-detail-table__label attendance-detail-table__label--valign-top">備考</td>
                        <td class="attendance-detail-table__td attendance-detail-table__content">
                            <textarea name="remark" class="attendance-detail__textarea" {{ $isPending ? 'readonly' : '' }}>{{ old('remark', $attendance->note) }}</textarea>
                            @error('remark')
                                <div class="attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                        </td>
                        <td class="attendance-detail-table__td attendance-detail-table__empty"></td>
                    </tr>

                    @if(!$isPending)
                    <tr class="attendance-detail-table__tr-button">
                        <td colspan="3" class="attendance-detail-table__td-button">
                            <button type="submit" class="attendance-detail__submit-btn">修正</button>
                        </td>
                    </tr>
                    @endif

                </tbody>
            </form>
        </table>

        @if($isPending)
            <div class="attendance-detail__waiting-error">*承認待ちのため修正はできません。</div>
        @endif
    </div>
@endsection