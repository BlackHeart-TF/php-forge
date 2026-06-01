<?php
/** @var string $heading */
/** @var string $collection */
/** @var list<array{slug: string, data: array}> $entries */
?>
<section>
    <h1><?= e($heading) ?></h1>

    <?php if ($entries === []): ?>
        <p class="muted">Nothing here yet.</p>
    <?php else: ?>
        <ul class="entry-list">
            <?php foreach ($entries as $entry): ?>
                <?php $data = $entry['data']; ?>
                <li class="entry-card">
                    <h2>
                        <a href="/<?= e($collection) ?>/<?= e($entry['slug']) ?>">
                            <?= e($data['title'] ?? $entry['slug']) ?>
                        </a>
                    </h2>
                    <?php if (!empty($data['date'])): ?>
                        <time class="meta" datetime="<?= e($data['date']) ?>"><?= e($data['date']) ?></time>
                    <?php endif ?>
                    <?php if ($collection === 'updates'):
                        $projectSlug = update_project_slug($data);
                        if ($projectSlug !== null):
                            $project = find_entry('projects', $projectSlug);
                            if ($project !== null): ?>
                        <p class="project-link project-link--list">
                            <a href="/projects/<?= e($project['slug']) ?>">
                                <?= e($project['data']['title'] ?? $project['slug']) ?>
                            </a>
                        </p>
                    <?php endif;
                        endif;
                    endif ?>
                    <?php if (!empty($data['stub'])): ?>
                        <p><?= e($data['stub']) ?></p>
                    <?php endif ?>
                    <?php if (!empty($data['tags']) && is_array($data['tags'])): ?>
                        <ul class="tags">
                            <?php foreach ($data['tags'] as $tag): ?>
                                <li><?= e((string) $tag) ?></li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
