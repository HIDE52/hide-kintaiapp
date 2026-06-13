@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/correction/list.css') }}">
@endsection

@section('content')
<div class="admin-correction-list">
    <div class="admin-correction-list__container">

        <h2 class="admin-correction-list__title">申請一覧</h2>

        <div class="admin-correction-list__tabs">
            <a href="?tab=waiting" class="admin-correction-list__tab {{ $currentTab === 'waiting' ? 'admin-correction-list__tab--active' : '' }}">承認待ち</a>
            <a href="?tab=approved" class="admin-correction-list__tab {{ $currentTab === 'approved' ? 'admin-correction-list__tab--active' : '' }}">承認済み</a>
        </div>

        <table class="admin-correction-list__table">
            <thead>
                <tr class="admin-correction-list__thead-tr">
                    <th class="admin-correction-list__th admin-correction-list__th--status">状態</th>
                    <th class="admin-correction-list__th admin-correction-list__th--name">名前</th>
                    <th class="admin-correction-list__th admin-correction-list__th--date">対象日時</th>
                    <th class="admin-correction-list__th admin-correction-list__th--reason">申請理由</th>
                    <th class="admin-correction-list__th admin-correction-list__th--request-date">申請日時</th>
                    <th class="admin-correction-list__th admin-correction-list__th--detail">詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $requestItem)
                    <tr class="admin-correction-list__tr">
                        <td class="admin-correction-list__td admin-correction-list__td--status-cell">
                            @if($requestItem->status === 0)
                                <span class="admin-correction-list__status admin-correction-list__status--waiting">承認待ち</span>
                            @elseif($requestItem->status === 1)
                                <span class="admin-correction-list__status admin-correction-list__status--approved">承認済み</span>
                            @endif
                        </td>
                        <td class="admin-correction-list__td admin-correction-list__td--name-text">
                            <span class="admin-correction-list__name-string">
                                {{ str_replace(' ', '', str_replace(' ', '', $requestItem->user->name)) }}
                            </span>
                        </td>
                        <td class="admin-correction-list__td">
                            {{ \Carbon\Carbon::parse($requestItem->attendance->date)->format('Y/m/d') }}
                        </td>
                        <td class="admin-correction-list__td admin-correction-list__td--reason-text">
                            {{ $requestItem->remark }}
                        </td>
                        <td class="admin-correction-list__td">
                            {{ $requestItem->created_at->format('Y/m/d') }}
                        </td>
                        <td class="admin-correction-list__td admin-correction-list__td--detail-cell">
                            <a href="{{ route('admin.correction.approve', ['attendance_correct_request_id' => $requestItem->id]) }}" class="admin-correction-list__link-btn">詳細</a>
                        </td>
                    </tr>
                @empty
                    <tr class="admin-correction-list__tr">
                        <td colspan="6" class="admin-correction-list__td admin-correction-list__td--empty">
                            該当する申請データはありません。
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endsection