<script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
<script>
    // Socket.io接続
    const socket = io('<?= getenv('EXPRESS_URL'); ?>'); // ExpressサーバーのURL

    // 新しいイベントが追加された時
    socket.on('new_event', function(event) {
        console.log(event);

        const contentCardContainer = document.getElementById('content-card-container');
        const template = document.getElementById('event-template'); // テンプレート要素を取得

        // タグを文字列に変換
        const tags = event.tags ? event.tags : '';

        // テンプレートのコピーを作成
        const clone = template.content.cloneNode(true); // テンプレートを複製

        // コピーしたテンプレートにイベントデータを埋め込む
        clone.querySelector('h5').textContent = event.title;
        clone.querySelector('a').href = `/event/detail?event_id=${event.id}`;
        const tagElement = clone.querySelector('h6');
        tagElement.querySelector('i').insertAdjacentHTML('afterend', ` ${tags}`); // アイコンの後ろにタグを挿入
        clone.querySelector('p').textContent = event.description;
        clone.querySelector('h7').textContent = `場所：${event.location} | 日程：${event.event_date}`;

        contentCardContainer.appendChild(clone);
    });
</script>