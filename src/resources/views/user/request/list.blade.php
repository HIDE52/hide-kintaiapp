@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/request/list.css') }}">
@endsection

@section('content')
<div class="request-list">
    <h2 class="request-list__title">申請一覧</h2>

    {{-- タブメニュー --}}
    <div class="tab-menu">
        <a href="?tab=pending" class="tab-item {{ request('tab') !== 'approved' ? 'active' : '' }}">承認待ち</a>
        <a href="?tab=approved" class="tab-item {{ request('tab') === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    <table class="request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>
                    <span class="status-badge {{ request('tab') === 'approved' ? 'approved' : 'pending' }}">
                        {{ request('tab') === 'approved' ? '承認済み' : '承認待ち' }}
                    </span>
                </td>
                <td>テスト 太郎</td>
                <td>2026/05/01</td>
                <td>電車遅延のため</td>
                <td>2026/05/01 18:30</td>
                <td>

                    <a href="/attendance/detail/1?status={{ request('tab') === 'approved' ? 'approved' : 'pending' }}" class="link-detail">詳細</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection