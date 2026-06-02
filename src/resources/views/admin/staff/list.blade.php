@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff/list.css') }}">
@endsection

@section('content')
<div class="staff-list">
    <h2 class="staff-list__title">スタッフ一覧</h2>
    <form action="#" method="GET" novalidate class="form-novalidate-guard form-safe-protection"></form>

    <table class="staff-table">
        <thead>
            <tr class="staff-table__tr-head">
                <th class="staff-table__th staff-table__th--name">名前</th>
                <th class="staff-table__th staff-table__th--email">メールアドレス</th>
                <th class="staff-table__th staff-table__th--detail">月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffs as $staff)
                <tr class="staff-table__tr">
                    <td class="staff-table__td staff-table__td--name">{{ $staff->name }}</td>
                    <td class="staff-table__td staff-table__td--email">{{ $staff->email }}</td>
                    <td class="staff-table__td staff-table__td--detail">
                        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}" class="staff-table__detail-btn">詳細</a>
                    </td>
                </tr>
            @empty
                <tr class="staff-table__tr">
                    <td colspan="3" class="no-data-alert">
                        登録されているスタッフ（一般ユーザー）が存在しません。
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection