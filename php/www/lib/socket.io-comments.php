<script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
<script>
    // Socket.io接続
    const socket = io('<?= getenv('EXPRESS_URL'); ?>'); // ExpressサーバーのURL

    // 現在のルームIDを取得
    const roomId = '<?= htmlspecialchars($_GET['id']) ?>';

    // ルームに参加する
    socket.emit('join_room', roomId);

    // 新しい投稿を受け取ったときの処理
    socket.on('new_comment', (data) => {
        // console.log('新しい投稿を受信しました:', data);
        
        addNewPost(data);
    });

    /**
     * テンプレートを使って新しい投稿を追加する関数
     * @param {Object} data - 受け取った投稿データ
     */
    function addNewPost(data) {
        const template = document.getElementById('comment-template'); // テンプレートを取得
        const clone = template.content.cloneNode(true); // テンプレートを複製

        // 各要素にデータを挿入
        clone.querySelector('.comment-name').textContent = data.name;
        clone.querySelector('.comment-comment').textContent = data.comment;
        clone.querySelector('.comment-createdat').textContent = data.createdAt;

        // 投稿リストの先頭に追加
        const postList = document.getElementById('comments');
        postList.prepend(clone);
    }
</script>
