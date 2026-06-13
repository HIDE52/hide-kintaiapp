@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/correction/approve.css') }}">
@endsection

@section('content')
<div class="admin-correction-approve">
    <div class="admin-correction-approve__container">

        <h2 class="admin-correction-approve__title">勤怠詳細</h2>

        <table class="admin-correction-approve__table">
            <form action="{{ route('admin.correction.approve', ['attendance_correct_request_id' => $requestItem->id]) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <tbody>

                    <tr class="admin-correction-approve__tr">
                        <td class="admin-correction-approve__td">名前</td>
                        <td class="admin-correction-approve__td">
                            <div class="admin-correction-approve__text-container">
                                <span class="admin-correction-approve__plain-text">{{ $requestItem->user->name }}</span>
                            </div>
                        </td>
                        <td class="admin-correction-approve__td"></td>
                    </tr>

                    @php
                        $carbonDate = \Carbon\Carbon::parse($requestItem->attendance->date);
                    @endphp
                    <tr class="admin-correction-approve__tr">
                        <td class="admin-correction-approve__td">日付</td>
                        <td class="admin-correction-approve__td">
                            <div class="admin-correction-approve__date-group">
                                <div class="admin-correction-approve__date-block-left">
                                    {{ $carbonDate->format('Y') }}年
                                </div>
                                <span class="admin-correction-approve__date-separator"></span>
                                <div class="admin-correction-approve__date-block-right">
                                    {{ $carbonDate->format('n') }}月{{ $carbonDate->format('j') }}日
                                </div>
                            </div>
                        </td>
                        <td class="admin-correction-approve__td"></td>
                    </tr>

                    <tr class="admin-correction-approve__tr">
                        <td class="admin-correction-approve__td">出勤・退勤</td>
                        <td class="admin-correction-approve__td">
                            <div class="admin-correction-approve__time-group">
                                <span class="admin-correction-approve__text-time">{{ \Carbon\Carbon::parse($requestItem->requested_punch_in)->format('H:i') }}</span>
                                <span class="admin-correction-approve__separator">〜</span>
                                <span class="admin-correction-approve__text-time">{{ $requestItem->requested_punch_out ? \Carbon\Carbon::parse($requestItem->requested_punch_out)->format('H:i') : '' }}</span>
                            </div>
                        </td>
                        <td class="admin-correction-approve__td"></td>
                    </tr>

                    @foreach($requestItem->correctionRests as $index => $correctionRest)
                    <tr class="admin-correction-approve__tr">
                        <td class="admin-correction-approve__td">
                            休憩{{ $index + 1 }}
                        </td>
                        <td class="admin-correction-approve__td">
                            <div class="admin-correction-approve__time-group">
                                <span class="admin-correction-approve__text-time">{{ \Carbon\Carbon::parse($correctionRest->requested_break_in)->format('H:i') }}</span>
                                <span class="admin-correction-approve__separator">〜</span>
                                <span class="admin-correction-approve__text-time">{{ $correctionRest->requested_break_out ? \Carbon\Carbon::parse($correctionRest->requested_break_out)->format('H:i') : '' }}</span>
                            </div>
                        </td>
                        <td class="admin-correction-approve__td"></td>
                    </tr>
                    @endforeach

                    <tr class="admin-correction-approve__tr admin-correction-approve__tr--last">
                        <td class="admin-correction-approve__td admin-correction-approve__label--valign-top">備考</td>
                        <td class="admin-correction-approve__td">
                            <div class="admin-correction-approve__text-container">
                                <textarea name="remark" class="admin-correction-approve__textarea" readonly>{{ old('remark', $requestItem->remark) }}</textarea>
                                @error('remark')
                                    <p class="admin-correction-approve__error-message">{{ $message }}</p>
                                @enderror
                            </div>
                        </td>
                        <td class="admin-correction-approve__td"></td>
                    </tr>

                    <tr class="admin-correction-approve__tr-button">
                        <td colspan="3" class="admin-correction-approve__td-button">
                            @if($requestItem->status === 0)
                                <button type="submit" class="admin-correction-approve__btn-base admin-correction-approve__submit-btn">承認</button>
                            @else
                                <span class="admin-correction-approve__btn-base admin-correction-approve__status-approved">承認済み</span>
                            @endif
                        </td>
                    </tr>

                </tbody>
            </form>
        </table>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.querySelector('.admin-correction-approve__textarea');
        if (textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
    });
</script>
@endsection