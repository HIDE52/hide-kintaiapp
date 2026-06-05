@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff/list.css') }}">
@endsection

@section('content')
<div class="admin-staff">
    <h2 class="admin-staff__title">スタッフ一覧</h2>
    <form action="#" method="GET" novalidate class="admin-staff__novalidate-guard admin-staff__safe-protection"></form>

    <table class="admin-staff-table">
        <thead>
            <tr class="admin-staff-table__tr-head">
                <th class="admin-staff-table__th admin-staff-table__th--name">名前</th>
                <th class="admin-staff-table__th admin-staff-table__th--email">メールアドレス</th>
                <th class="admin-staff-table__th admin-staff-table__th--detail">月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffs as $staff)
                <tr class="admin-staff-table__tr">
                    <td class="admin-staff-table__td admin-staff-table__td--name">{{ $staff->name }}</td>
                    <td class="admin-staff-table__td admin-staff-table__td--email">{{ $staff->email }}</td>
                    <td class="admin-staff-table__td admin-staff-table__td--detail">
                        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}" class="admin-staff-table__detail-btn">詳細</a>
                    </td>
                </tr>
            @empty
                <tr class="admin-staff-table__tr">
                    <td colspan="3" class="admin-staff-table__empty">
                        登録されているスタッフ（一般ユーザー）が存在しません。
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection