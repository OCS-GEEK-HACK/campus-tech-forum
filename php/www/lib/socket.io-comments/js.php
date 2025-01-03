<script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
<?php if (isset($mode) && !empty($mode)) : ?>
    <script>
        // Socket.io接続
        const socket = io('<?= getenv('EXPRESS_URL'); ?>'); // ExpressサーバーのURL

        <?php if ($mode === 'event'): ?>
            // 新しいイベントが追加された時
            // 現在のルームIDを取得
            const roomId = '<?= htmlspecialchars($_GET['event_id']) ?>';

            // ルームに参加する
            socket.emit('join_room', roomId);

            // 新しい投稿を受け取ったときの処理
            socket.on('new_event_comment', (data) => {
                addNewPost(data);
            });

            /**
             * テンプレートを使って新しい投稿を追加する関数
             * @param {Object} data - 受け取った投稿データ
             */
            function addNewPost(data) {
                const template = document.getElementById('event-comment-template'); // テンプレートを取得
                const clone = template.content.cloneNode(true); // テンプレートを複製

                // 各要素にデータを挿入
                clone.querySelector('.comment-name').textContent = data.user_name;
                clone.querySelector('.comment-comment').textContent = data.content;
                clone.querySelector('.comment-createdat').textContent = data.created_at;

                // 投稿リストの先頭に追加
                const postList = document.getElementById('comments');
                postList.prepend(clone);
            }
        <?php elseif ($mode === 'idea'): ?>
            // 現在のルームIDを取得
            const roomId = '<?= htmlspecialchars($_GET['idea_id']) ?>';

            // ルームに参加する
            socket.emit('join_room', roomId);

            // 新しい投稿を受け取ったときの処理
            socket.on('new_idea_comment', (data) => {
                addNewPost(data);
            });

            /**
             * テンプレートを使って新しい投稿を追加する関数
             * @param {Object} data - 受け取った投稿データ
             */
            function addNewPost(data) {
                const template = document.getElementById('idea-comment-template'); // テンプレートを取得
                const clone = template.content.cloneNode(true); // テンプレートを複製

                // 各要素にデータを挿入
                clone.querySelector('.comment-name').textContent = data.user_name;
                clone.querySelector('.comment-comment').textContent = data.content;
                clone.querySelector('.comment-createdat').textContent = data.created_at;

                // 投稿リストの先頭に追加
                const postList = document.getElementById('comments');
                postList.prepend(clone);
            }
        <?php endif; ?>
    </script>
<?php endif; ?>