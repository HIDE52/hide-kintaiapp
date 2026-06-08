@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/correction/approve.css') }}">
@endsection

@section('content')
<div class="admin-approve">
    <div class="admin-approve__container">

        <h2 class="admin-approve__title">勤怠詳細</h2>

        <table class="admin-approve-table">
            <form action="{{ route('admin.correction.approve', ['attendance_correct_request_id' => $requestItem->id]) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <tbody>

                    <tr class="admin-approve-table__tr">
                        <td class="admin-approve-table__td">名前</td>
                        <td class="admin-approve-table__td">
                            <div class="admin-approve__text-container">
                                <span class="admin-approve__plain-text">{{ $requestItem->user->name }}</span>
                            </div>
                        </td>
                        <td class="admin-approve-table__td"></td>
                    </tr>

                    @php
                        $carbonDate = \Carbon\Carbon::parse($requestItem->attendance->date);
                    @endphp
                    <tr class="admin-approve-table__tr">
                        <td class="admin-approve-table__td">日付</td>
                        <td class="admin-approve-table__td">
                            <div class="admin-approve__date-group">
                                <div class="admin-approve__date-block-left">
                                    {{ $carbonDate->format('Y') }}年
                                </div>
                                <span class="admin-approve__date-separator"></span>
                                <div class="admin-approve__date-block-right">
                                    {{ $carbonDate->format('n') }}月{{ $carbonDate->format('j') }}日
                                </div>
                            </div>
                        </td>
                        <td class="admin-approve-table__td"></td>
                    </tr>

                    <tr class="admin-approve-table__tr">
                        <td class="admin-approve-table__td">出勤・退勤</td>
                        <td class="admin-approve-table__td">
                            <div class="admin-approve__time-group">
                                <span class="admin-approve__text-time">{{ \Carbon\Carbon::parse($requestItem->requested_punch_in)->format('H:i') }}</span>
                                <span class="admin-approve__separator">〜</span>
                                <span class="admin-approve__text-time">{{ $requestItem->requested_punch_out ? \Carbon\Carbon::parse($requestItem->requested_punch_out)->format('H:i') : '' }}</span>
                            </div>
                        </td>
                        <td class="admin-approve-table__td"></td>
                    </tr>

                    @foreach($requestItem->correctionRests as $index => $correctionRest)
                    <tr class="admin-approve-table__tr">
                        <td class="admin-approve-table__td">
                            休憩{{ $index + 1 }}
                        </td>
                        <td class="admin-approve-table__td">
                            <div class="admin-approve__time-group">
                                <span class="admin-approve__text-time">{{ \Carbon\Carbon::parse($correctionRest->requested_break_in)->format('H:i') }}</span>
                                <span class="admin-approve__separator">〜</span>
                                <span class="admin-approve__text-time">{{ $correctionRest->requested_break_out ? \Carbon\Carbon::parse($correctionRest->requested_break_out)->format('H:i') : '' }}</span>
                            </div>
                        </td>
                        <td class="admin-approve-table__td"></td>
                    </tr>
                    @endforeach

                    <tr class="admin-approve-table__tr admin-approve-table__tr--last">
                        <td class="admin-approve-table__td admin-approve-table__label--valign-top">備考</td>
                        <td class="admin-approve-table__td">
                            <div class="admin-approve__text-container">
                                <textarea name="remark" class="admin-approve__textarea" readonly>{{ old('remark', $requestItem->remark) }}</textarea>
                                @error('remark')
                                    <p class="admin-approve__error-message">{{ $message }}</p>
                                @enderror
                            </div>
                        </td>
                        <td class="admin-approve-table__td"></td>
                    </tr>

                    <tr class="admin-approve-table__tr-button">
                        <td colspan="3" class="admin-approve-table__td-button">
                            @if($requestItem->status === 0)
                                <button type="submit" class="admin-approve__btn-base admin-approve__submit-btn">承認</button>
                            @else
                                <span class="admin-approve__btn-base admin-approve__status-approved">承認済み</span>
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
        const textarea = document.querySelector('.admin-approve__textarea');
        if (textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
    });
</script>
@endsection