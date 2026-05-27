@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/correction/list.css') }}">
@endsection

@section('content')
<div class="correction-list">
    <div class="correction-list__container">
        
        <h2 class="correction-list__title">申請一覧</h2>

        <div class="correction-list__tabs">
            <a href="?tab=waiting" class="correction-list__tab {{ $currentTab === 'waiting' ? 'correction-list__tab--active' : '' }}">承認待ち</a>
            <a href="?tab=approved" class="correction-list__tab {{ $currentTab === 'approved' ? 'correction-list__tab--active' : '' }}">承認済み</a>
        </div>

        <table class="correction-table">
            <thead>
                <tr>
                    <th class="correction-table__th correction-table__th--status">状態</th>
                    <th class="correction-table__th correction-table__th--name">名前</th>
                    <th class="correction-table__th correction-table__th--date">対象日時</th>
                    <th class="correction-table__th correction-table__th--reason">申請理由</th>
                    <th class="correction-table__th correction-table__th--request-date">申請日時</th>
                    <th class="correction-table__th correction-table__th--detail">詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $requestItem)
                    <tr class="correction-table__tr">
                        <td class="correction-table__td">
                            @if($requestItem->status === 0)
                                <span class="correction-table__status correction-table__status--waiting">承認待ち</span>
                            @elseif($requestItem->status === 1)
                                <span class="correction-table__status correction-table__status--approved">承認済み</span>
                            @endif
                        </td>
                        <td class="correction-table__td correction-table__td--name-text">{{ $requestItem->user->name }}</td>
                        <td class="correction-table__td">{{ \Carbon\Carbon::parse($requestItem->attendance->date)->format('Y/m/d') }}</td>
                        <td class="correction-table__td correction-table__td--reason-text">{{ $requestItem->remark }}</td>
                        <td class="correction-table__td">{{ $requestItem->created_at->format('Y/m/d') }}</td>
                        <td class="correction-table__td">
                            <a href="{{ route('attendance.show', ['id' => $requestItem->attendance_id]) }}" class="correction-table__detail-link">詳細</a>
                        </td>
                    </tr>
                @empty
                    <tr class="correction-table__tr">
                        <td colspan="6" class="correction-table__td correction-table__td--empty">
                            該当する申請データはありません。
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endsection