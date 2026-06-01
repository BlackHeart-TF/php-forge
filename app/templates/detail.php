<?php
/** @var string $collection */
/** @var array{slug: string, data: array} $entry */
/** @var list<array{slug: string, data: array}>|null $relatedUpdates */
/** @var list<array{slug: string, data: array}>|null $linkedProjects */
$data = $entry['data'];
$relatedUpdates = $relatedUpdates ?? [];
$linkedProjects = $linkedProjects ?? [];
?>
<article class="entry-detail">
    <p class="back"><a href="/<?= e($collection) ?>">← <?= e(ucfirst($collection)) ?></a></p>

    <h1><?= e($data['title'] ?? $entry['slug']) ?></h1>

    <?php if (!empty($data['date'])): ?>
        <time class="meta" datetime="<?= e($data['date']) ?>"><?= e($data['date']) ?></time>
    <?php endif ?>

    <?php if ($collection === 'updates' && $linkedProjects !== []): ?>
        <p class="project-link">
            <?php foreach ($linkedProjects as $project): ?>
                <a href="/projects/<?= e($project['slug']) ?>">
                    <?= e($project['data']['title'] ?? $project['slug']) ?>
                </a>
            <?php endforeach ?>
        </p>
    <?php endif ?>

    <?php if (!empty($data['tags']) && is_array($data['tags'])): ?>
        <ul class="tags">
            <?php foreach ($data['tags'] as $tag): ?>
                <li><?= e((string) $tag) ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>

    <?php if ($collection === 'projects'):
        $progress = project_progress($data);
        if ($progress !== null): ?>
        <div class="progress-block">
            <label class="progress-label" for="project-progress">Progress</label>
            <div class="progress-track">
                <input
                    type="range"
                    id="project-progress"
                    class="progress-slider"
                    min="0"
                    max="100"
                    value="<?= $progress ?>"
                    disabled
                    aria-valuenow="<?= $progress ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                >
            </div>
            <span class="progress-value"><?= $progress ?>%</span>
        </div>
    <?php endif;

        $finished = project_list_items($data, 'finished');
        $todo = project_list_items($data, 'todo');
        if ($finished !== [] || $todo !== []): ?>
        <div class="project-lists">
            <?php if ($finished !== []): ?>
            <section class="project-list project-list--finished">
                <h2>Finished</h2>
                <ul>
                    <?php foreach ($finished as $item): ?>
                        <li><?= e($item) ?></li>
                    <?php endforeach ?>
                </ul>
            </section>
            <?php endif ?>
            <?php if ($todo !== []): ?>
            <section class="project-list project-list--todo">
                <h2>To-do</h2>
                <ul>
                    <?php foreach ($todo as $item): ?>
                        <li><?= e($item) ?></li>
                    <?php endforeach ?>
                </ul>
            </section>
            <?php endif ?>
        </div>
    <?php endif;
    endif ?>

    <?php if (!empty($data['images']) && is_array($data['images'])): ?>
        <div class="gallery">
            <?php foreach ($data['images'] as $image): ?>
                <img src="<?= e((string) $image) ?>" alt="">
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <div class="prose">
        <?= $data['content'] ?? '' ?>
    </div>

    <?php if ($collection === 'projects' && $relatedUpdates !== []): ?>
        <section class="related">
            <h2>Updates</h2>
            <ul class="entry-list">
                <?php foreach ($relatedUpdates as $update): ?>
                    <?php $updateData = $update['data']; ?>
                    <li class="entry-card">
                        <h3>
                            <a href="/updates/<?= e($update['slug']) ?>">
                                <?= e($updateData['title'] ?? $update['slug']) ?>
                            </a>
                        </h3>
                        <?php if (!empty($updateData['date'])): ?>
                            <time class="meta" datetime="<?= e($updateData['date']) ?>"><?= e($updateData['date']) ?></time>
                        <?php endif ?>
                        <?php if (!empty($updateData['stub'])): ?>
                            <p><?= e($updateData['stub']) ?></p>
                        <?php endif ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>
</article>
