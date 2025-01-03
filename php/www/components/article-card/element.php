<?php if (isset($title) && !empty($title) && isset($date) && !empty($date) && isset($content) && !empty($content) && isset($link) && !empty($link)): ?>
    <div class="col-md-4 mb-3">
        <div class="card border rounded p-3 h-100">
            <h5 class="mb-2"><?= htmlspecialchars($title) ?></h5>
            <p class="text-muted"><?= htmlspecialchars($date) ?></p>
            <p><?= htmlspecialchars($content) ?></p>
            <a href="<?= htmlspecialchars($link) ?>" class="btn btn-dark btn-sm mt-auto">詳細を見る</a>
        </div>
    </div>
<?php endif; ?>