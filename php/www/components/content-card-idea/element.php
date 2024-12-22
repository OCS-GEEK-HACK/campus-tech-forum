<?php if (isset($idea)): ?>
    <a href="/idea/detail?idea_id=<?= htmlspecialchars($idea['id'], ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none text-dark">
        <section class="mw-100 rounded p-3 mb-2 border">
            <h5 class="m-0"><?= htmlspecialchars($idea['title'], ENT_QUOTES, 'UTF-8'); ?></h5>

            <?php
            // もしtagsが文字列として保存されている場合は配列に変換
            $tags = is_string($idea['tags']) ? explode(',', trim($idea['tags'], '{}')) : $idea['tags'];
            ?>

            <h6 class="m-0 pt-2 fw-light"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars(implode(', ', $tags), ENT_QUOTES, 'UTF-8'); ?></h6>

            <p class="w-75 pt-2 m-0">
                <?= htmlspecialchars($idea['description'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </section>
    </a>
<?php endif; ?>
