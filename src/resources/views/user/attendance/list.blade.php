@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/common.demo.css') }}">
@endsection

@section('content')
<div class="demo-cockpit">
    <div class="demo-main">
    @php
        $status = request('status', 'A');
        $imageName = "img/screenshot_" . $status . ".png";
    @endphp
    <img src="{{ asset('storage/' . $imageName) }}" alt="画面イメージ" class="responsive-img">
    </div>

    <div class="demo-sidebar">
        <div class="control-section">
            <p class="section-title">【ページ遷移】</p>
            <div class="btn-list">
                <a href="/attendance" class="btn btn-nav">画面に戻る</a>
                <a href="/attendance/detail/1" class="btn btn-nav">詳細画面へ</a>
            </div>
        </div>

        <div class="control-section">
            <p class="section-title">【状態切替】</p>
            <div class="btn-list">
                <a href="?status=before_work" class="btn btn-status {{ request('status') == 'before_work' || !request('status') ? 'active' : '' }}">① A</a>
                <a href="?status=working" class="btn btn-status {{ request('status') == 'working' ? 'active' : '' }}">② B</a>
                <a href="?status=resting" class="btn btn-status {{ request('status') == 'resting' ? 'active' : '' }}">③ C</a>
                <a href="?status=finished" class="btn btn-status {{ request('status') == 'finished' ? 'active' : '' }}">④ D</a>
                <a href="?status=error" class="btn btn-status {{ request('status') == 'error' ? 'active' : '' }}">⑤ E</a>
            </div>
        </div>
    </div>
</div>
@endsection