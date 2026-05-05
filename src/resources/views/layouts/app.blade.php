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
                <a href="/" class="common-header__logo-link">
                    <img src="{{ asset('css/img/COACHTECHヘッダーロゴ (1).png') }}" alt="coachtech" class="common-header__logo-img">
                </a>
            </div>
<<nav class="common-header__nav">
    <div class="common-header__nav-groups">
        <ul class="common-header__nav-list user-nav">
            <li class="common-header__nav-item"><a href="/attendance" class="common-header__nav-link">勤怠</a></li>
            <li class="common-header__nav-item"><a href="/attendance/list" class="common-header__nav-link">勤怠一覧</a></li>
            <li class="common-header__nav-item"><a href="/stamp_correction_request/list" class="common-header__nav-link">申請一覧</a></li>
        </ul>

        <ul class="common-header__nav-list admin-nav">
            <li class="common-header__nav-item"><a href="/admin/attendance/list" class="common-header__nav-link">【管】勤怠一覧</a></li>
            <li class="common-header__nav-item"><a href="/admin/staff/list" class="common-header__nav-link">【管】スタッフ一覧</a></li>
            <li class="common-header__nav-item"><a href="/admin/stamp_correction_request/list" class="common-header__nav-link">【管】申請一覧</a></li>
            <li class="common-header__nav-item">
                <form action="/logout" method="POST" style="display: inline;">
                    @csrf
                    <button class="common-header__nav-link" style="background:none; border:none; cursor:pointer;">ログアウト</button>
                </form>
            </li>
        </ul>
    </div>
</nav>
        </div>
    </header>

    <main class="main">
        <div class="main__container">
            @yield('content')
        </div>
    </main>
</body>

</html>