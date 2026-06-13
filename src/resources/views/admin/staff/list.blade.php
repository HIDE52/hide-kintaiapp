@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff/list.css') }}">
@endsection

@section('content')
<div class="admin-staff-list">
    <h2 class="admin-staff-list__title">スタッフ一覧</h2>

    <table class="admin-staff-list__table">
        <thead>
            <tr class="admin-staff-list__tr-head">
                <th class="admin-staff-list__th admin-staff-list__th--name">名前</th>
                <th class="admin-staff-list__th admin-staff-list__th--email">メールアドレス</th>
                <th class="admin-staff-list__th admin-staff-list__th--detail">月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffs as $staff)
                <tr class="admin-staff-list__tr">
                    <td class="admin-staff-list__td admin-staff-list__td--name">{{ $staff->name }}</td>
                    <td class="admin-staff-list__td admin-staff-list__td--email">{{ $staff->email }}</td>
                    <td class="admin-staff-list__td admin-staff-list__td--detail">
                        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}" class="admin-staff-list__detail-btn">詳細</a>
                    </td>
                </tr>
            @empty
                <tr class="admin-staff-list__tr">
                    <td colspan="3" class="admin-staff-list__empty">
                        登録されているスタッフ（一般ユーザー）が存在しません。
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection