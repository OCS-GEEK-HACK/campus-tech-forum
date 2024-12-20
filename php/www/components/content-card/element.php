<?php if (isset($event)): ?>
    <a href="/event/detail?event_id=<?= htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none text-dark">
        <section class="mw-100 rounded p-3 mb-2 border">
            <h5 class="m-0"><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8'); ?></h5>

            <?php
            // もしtagsが文字列として保存されている場合は配列に変換
            $tags = is_string($event['tags']) ? explode(',', trim($event['tags'], '{}')) : $event['tags'];
            ?>

            <h6 class="m-0 pt-2 fw-light"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars(implode(', ', $tags), ENT_QUOTES, 'UTF-8'); ?></h6>

            <p class="w-75 pt-2 m-0">
                <?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <h7 class="pt-2">場所：<?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8'); ?> | 日程：<?= htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8'); ?></h7>
        </section>
    </a>
<?php endif; ?>
