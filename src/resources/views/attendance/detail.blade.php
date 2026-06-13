@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section('content')
    <div class="attendance-detail">
        @if (session('error_message'))
            <div class="attendance-detail__alert-error">
                {{ session('error_message') }}
            </div>
        @endif

        <h2 class="attendance-detail__title">勤怠詳細</h2>

        <table class="attendance-detail-table">
            <form action="/attendance/detail/{{ $attendance->id }}" method="POST" novalidate>
                @csrf
                <tbody>
                    
                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td">名前</td>
                        <td class="attendance-detail-table__td">
                            <div class="attendance-detail__text-container">
                                <span class="attendance-detail__plain-text">{{ $attendance->user->name }}</span>
                            </div>
                        </td>
                        <td class="attendance-detail-table__td"></td>
                    </tr>

                    @php
                        $carbonDate = \Carbon\Carbon::parse($attendance->date);
                    @endphp
                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td">日付</td>
                        <td class="attendance-detail-table__td">
                            <div class="attendance-detail__date-group">
                                <div class="attendance-detail__date-block-left">
                                    {{ $carbonDate->format('Y') }}年
                                </div>
                                <span class="attendance-detail__date-separator"></span>
                                <div class="attendance-detail__date-block-right">
                                    {{ $carbonDate->format('n') }}月{{ $carbonDate->format('j') }}日
                                </div>
                            </div>
                        </td>
                        <td class="attendance-detail-table__td"></td>
                    </tr>

                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td">出勤・退勤</td>
                        <td class="attendance-detail-table__td">
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
                        <td class="attendance-detail-table__td"></td>
                    </tr>

                    @foreach($attendance->rests as $index => $rest)
                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td">
                            休憩{{ $index + 1 }}
                        </td>
                        <td class="attendance-detail-table__td">
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
                        <td class="attendance-detail-table__td"></td>
                    </tr>
                    @endforeach

                    @if(!$isPending)
                    <tr class="attendance-detail-table__tr">
                        <td class="attendance-detail-table__td">
                            休憩{{ count($attendance->rests) + 1 }}
                        </td>
                        <td class="attendance-detail-table__td">
                            <div class="attendance-detail__time-group">
                                <input type="text" name="rest_id[{{ count($attendance->rests) }}][break_in]" class="attendance-detail__input-time" value="{{ old('rest_id.' . count($attendance->rests) . '.break_in') }}">
                                <span class="attendance-detail__separator">〜</span>
                                <input type="text" name="rest_id[{{ count($attendance->rests) }}][break_out]" class="attendance-detail__input-time" value="{{ old('rest_id.' . count($attendance->rests) . '.break_out') }}">
                            </div>
                        </td>
                        <td class="attendance-detail-table__td"></td>
                    </tr>
                    @endif

                    <tr class="attendance-detail-table__tr attendance-detail-table__tr--last">
                        <td class="attendance-detail-table__td attendance-detail-table__label--valign-top">備考</td>
                        <td class="attendance-detail-table__td">
                            <div class="attendance-detail__text-container">
                                <textarea name="remark" class="attendance-detail__textarea" {{ $isPending ? 'readonly' : '' }}>{{ old('remark', $attendance->note) }}</textarea>
                            </div>
                            @error('remark')
                                <div class="attendance-detail__error-message">{{ $message }}</div>
                            @enderror
                        </td>
                        <td class="attendance-detail-table__td"></td>
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.querySelector('.attendance-detail__textarea');
        
        if (textarea) {
            function autoResize() {
                textarea.style.height = '43px';
                textarea.style.height = Math.max(43, textarea.scrollHeight) + 'px';
            }

            textarea.addEventListener('input', autoResize);
            autoResize();
        }
    });
    </script>
@endsection