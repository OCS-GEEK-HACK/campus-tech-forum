<div class="header sticky-top bg-white border-bottom d-flex flex-column p-3">
    <div class="d-flex justify-content-between align-items-center w-100">
        <a href="/" class="h4 mb-0 text-decoration-none text-dark d-md-none d-block">学内掲示板アプリ</a>
        <div class="search-box align-items-center gap-2 d-md-flex d-none">
            <input type="text" class="form-control" placeholder="検索...">
            <button class="btn btn-dark"><i class="fas fa-search"></i></button>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-dark d-md-block d-none"><i class="fas fa-plus"></i> 投稿する</button>
            <!-- ハンバーガーメニュー -->
            <div class="d-flex justify-content-between align-items-center d-md-none">
                <button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-expanded="false" aria-controls="sidebarMenu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ハンバーガーメニューが表示される部分 -->
    <div class="collapse" id="sidebarMenu">
        <nav class="sidebar-mobile bg-light d-md-none py-3 px-3 mt-3">
            <div class="search-box d-flex align-items-center gap-2">
                <input type="text" class="form-control" placeholder="検索...">
                <button class="btn btn-dark"><i class="fas fa-search"></i></button>
            </div>
            <ul class="list-unstyled p-0">
                <li>
                    <a href="#" class="d-block py-2 px-3 text-dark text-decoration-none rounded hover-bg"><i class="fas fa-plus"></i> 投稿する</a>
                </li>
                <li>
                    <a href="/" class="d-block py-2 px-3 text-dark text-decoration-none rounded hover-bg"><i class="fas fa-home me-2"></i> ホーム</a>
                </li>
                <li>
                    <a href="/event" class="d-block py-2 px-3 text-dark text-decoration-none rounded hover-bg"><i class="fas fa-share-square me-2"></i> イベント共有</a>
                </li>
                <li>
                    <a href="/idea" class="d-block py-2 px-3 text-dark text-decoration-none rounded hover-bg"><i class="fas fa-lightbulb me-2"></i> アイデア共有</a>
                </li>
                <li>
                    <a href="/tech" class="d-block py-2 px-3 text-dark text-decoration-none rounded hover-bg"><i class="fas fa-search me-2"></i> 技術探求</a>
                </li>
                <li>
                    <a href="/question" class="d-block py-2 px-3 text-dark text-decoration-none rounded hover-bg"><i class="fas fa-question-circle me-2"></i> 質問</a>
                </li>
                <hr>
                <li>
                    <a href="#" class="d-block py-2 px-3 text-dark text-decoration-none rounded hover-bg"><i class="fas fa-user me-2"></i> プロフィール</a>
                </li>
                <li>
                    <a href="/auth/signout" class="d-block py-2 px-3 text-danger text-decoration-none rounded hover-bg-danger"><i class="fas fa-sign-out-alt me-2"></i> ログアウト</a>
                </li>
            </ul>
        </nav>
    </div>
</div>