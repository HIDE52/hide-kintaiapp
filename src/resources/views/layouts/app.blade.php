<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>coachtech 勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    @yield('css')
</head>
<body>
    <header class="common-header">
        <div class="common-header__inner">
            <div class="common-header__logo">
                <a href="{{ Auth::check() ? (Auth::user()->role === 1 ? '/admin/attendance/list' : '/attendance') : '/login' }}" class="common-header__logo-link">
                    <img src="{{ asset('css/img/COACHTECHヘッダーロゴ (1).png') }}" alt="coachtech" class="common-header__logo-img">
                </a>
            </div>
            @if (Auth::check())
            <nav class="common-header__nav">
                <div class="common-header__nav-groups">
                    @if (Auth::user()->role === 1)
                    <ul class="common-header__nav-list admin-nav">
                        <li><a href="/admin/attendance/list" class="common-header__nav-link">勤怠一覧</a></li>
                        <li><a href="/admin/staff/list" class="common-header__nav-link">スタッフ一覧</a></li>
                        <li><a href="/admin/stamp_correction_request/list" class="common-header__nav-link">申請一覧</a></li>
                    </ul>
                    @endif
                    <ul class="common-header__nav-list user-nav">
                        @if (Auth::user()->role === 2)
                            @if(isset($status) && $status === 'left')
                                <li><a href="/attendance/list" class="common-header__nav-link">今月の出勤一覧</a></li>
                                <li><a href="/stamp_correction_request/list" class="common-header__nav-link">申請一覧</a></li>
                            @else
                                <li><a href="/attendance" class="common-header__nav-link">勤怠</a></li>
                                <li><a href="/attendance/list" class="common-header__nav-link">勤怠一覧</a></li>
                                <li><a href="/stamp_correction_request/list" class="common-header__nav-link">申請</a></li>
                            @endif
                        @endif
                        <li>
                            <form action="/logout" method="POST" class="common-header__logout-form">
                                @csrf
                                <button type="submit" class="common-header__nav-button">ログアウト</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
            @endif
        </div>
    </header>
    <main class="main">
        <div class="main__container">
            @yield('content')
        </div>
    </main>
</body>
</html>