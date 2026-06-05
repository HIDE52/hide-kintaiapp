@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendance/detail.css') }}">
@endsection

@section('content')
    <div class="admin-attendance-detail">
        <h2 class="admin-attendance-detail__title">勤怠詳細</h2>

        @if($isPending)
            <div class="admin-attendance-detail__alert-pending">
                承認待ちのため修正はできません。
            </div>
        @endif

        @if(session('error'))
            <div class="admin-attendance-detail__alert-error">
                {{ session('error') }}
            </div>
        @endif

        <table class="admin-attendance-detail-table">
            <form action="/admin/attendance/{{ $attendance->id }}" method="POST" novalidate>
                @csrf
                @method('PUT')
                
                <tbody>
                    
                    <tr class="admin-attendance-detail-table__tr">
                        <td class="admin-attendance-detail-table__td">名前</td>
                        <td class="admin-attendance-detail-table__td">
                            <div class="admin-attendance-detail__text-container">
                                <span class="admin-attendance-detail__plain-text">{{ $attendance->user->name }}</span>
                            </div>
                        </td>
                        <td class="admin-attendance-detail-table__td"></td>
                    </tr>

                    @php
                        $carbonDate = \Carbon\Carbon::parse($attendance->date);
                    @endphp
                    <tr class="admin-attendance-detail-table__tr">
                        <td class="admin-attendance-detail-table__td">日付</td>
                        <td class="admin-attendance-detail-table__td">
                            <div class="admin-attendance-detail__date-group">
                                <div class="admin-attendance-detail__date-block-left">
                                    {{ $carbonDate->format('Y') }}年
                                </div>
                                <span class="admin-attendance-detail__date-separator"></span>
                                <div class="admin-attendance-detail__date-block-right">
                                    {{ $carbonDate->format('m') }}月{{ $carbonDate->format('d') }}日
                                </div>
                            </div>
                        </td>
                        <td class="admin-attendance-detail-table__td"></td>
                    </tr>

                    <tr class="admin-attendance-detail-table__tr">
                        <td class="admin-attendance-detail-table__td">出勤・退勤</td>
                        <td class="admin-attendance-detail-table__td">
                            <div class="admin-attendance-detail__time-group">
                                <input type="text" name="punch_in" class="admin-attendance-detail__input-time" value="{{ old('punch_in', \Carbon\Carbon::parse($attendance->punch_in)->format('H:i')) }}" {{ $isPending ? 'disabled' : '' }}>
                                <span class="admin-attendance-detail__separator">〜</span>
                                <input type="text" name="punch_out" class="admin-attendance-detail__input-time" value="{{ old('punch_out', $attendance->punch_out ? \Carbon\Carbon::parse($attendance->punch_out)->format('H:i') : '') }}" {{ $isPending ? 'disabled' : '' }}>
                            </div>
                            @error('punch_in')
                                <div class="admin-attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                            @error('punch_out')
                                <div class="admin-attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                        </td>
                        <td class="admin-attendance-detail-table__td"></td>
                    </tr>

                    @foreach($attendance->rests as $index => $rest)
                    <tr class="admin-attendance-detail-table__tr">
                        <td class="admin-attendance-detail-table__td">
                            休憩{{ $index + 1 }}
                        </td>
                        <td class="admin-attendance-detail-table__td">
                            <div class="admin-attendance-detail__time-group">
                                <input type="text" name="rest_id[{{ $index }}][break_in]" class="admin-attendance-detail__input-time" value="{{ old('rest_id.' . $index . '.break_in', \Carbon\Carbon::parse($rest->break_in)->format('H:i')) }}" {{ $isPending ? 'disabled' : '' }}>
                                <span class="admin-attendance-detail__separator">〜</span>
                                <input type="text" name="rest_id[{{ $index }}][break_out]" class="admin-attendance-detail__input-time" value="{{ old('rest_id.' . $index . '.break_out', $rest->break_out ? \Carbon\Carbon::parse($rest->break_out)->format('H:i') : '') }}" {{ $isPending ? 'disabled' : '' }}>
                            </div>
                            @error('rest_id.' . $index . '.break_in')
                                <div class="admin-attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                            @error('rest_id.' . $index . '.break_out')
                                <div class="admin-attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                        </td>
                        <td class="admin-attendance-detail-table__td"></td>
                    </tr>
                    @endforeach

                    @if(!$isPending)
                    <tr class="admin-attendance-detail-table__tr">
                        <td class="admin-attendance-detail-table__td">
                            休憩{{ count($attendance->rests) + 1 }}
                        </td>
                        <td class="admin-attendance-detail-table__td">
                            <div class="admin-attendance-detail__time-group">
                                <input type="text" name="rest_id[{{ count($attendance->rests) }}][break_in]" class="admin-attendance-detail__input-time" value="{{ old('rest_id.' . count($attendance->rests) . '.break_in') }}" placeholder="00:00">
                                <span class="admin-attendance-detail__separator">〜</span>
                                <input type="text" name="rest_id[{{ count($attendance->rests) }}][break_out]" class="admin-attendance-detail__input-time" value="{{ old('rest_id.' . count($attendance->rests) . '.break_out') }}" placeholder="00:00">
                            </div>
                        </td>
                        <td class="admin-attendance-detail-table__td"></td>
                    </tr>
                    @endif

                    <tr class="admin-attendance-detail-table__tr admin-attendance-detail-table__tr--last">
                        <td class="admin-attendance-detail-table__td admin-attendance-detail-table__label--valign-top">備考</td>
                        <td class="admin-attendance-detail-table__td">
                            <div class="admin-attendance-detail__text-container">
                                <textarea name="remark" class="admin-attendance-detail__textarea" {{ $isPending ? 'disabled' : '' }}>{{ old('remark', $attendance->note) }}</textarea>
                            </div>
                            @error('remark')
                                <div class="admin-attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                        </td>
                        <td class="admin-attendance-detail-table__td"></td>
                    </tr>

                    <tr class="admin-attendance-detail-table__tr-button">
                        <td colspan="3" class="admin-attendance-detail-table__td-button">
                            <button type="submit" class="admin-attendance-detail__submit-btn {{ $isPending ? 'admin-attendance-detail__submit-btn--disabled' : '' }}" {{ $isPending ? 'disabled' : '' }}>修正</button>
                        </td>
                    </tr>

                </tbody>
            </form>
        </table>
    </div>
@endsection