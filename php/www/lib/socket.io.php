<script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
<script>
    // Socket.io接続
    const socket = io('<?= getenv('EXPRESS_URL'); ?>'); // ExpressサーバーのURL

    // 新しい投稿を受け取ったときの処理
    socket.on('new_room', (data) => {
        addNewPost(data);
    });

    /**
     * テンプレートを使って新しい投稿を追加する関数
     * @param {Object} data - 受け取った投稿データ
     */
    function addNewPost(data) {
        const template = document.getElementById('room-template'); // テンプレートを取得
        const clone = template.content.cloneNode(true); // テンプレートを複製

        // 名前、コメント、時刻を動的に設定
        clone.querySelector('.room-link').href = `/rooms?id=${data.roomId}`;
        clone.querySelector('.title').textContent = data.title;
        clone.querySelector('.desc').textContent = `（作成者: ${data.createdBy}, ${data.createdAt}）`;

        // 投稿リストの先頭に挿入
        const postList = document.getElementById('rooms');
        postList.prepend(clone);
    }
</script>