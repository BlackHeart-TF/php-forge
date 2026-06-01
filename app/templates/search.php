<?php
/** @var string $query */
/** @var list<array{collection: string, slug: string, data: array}> $results */
?>
<section>
    <h1>Search</h1>

    <form class="search-form search-form--page" action="/search" method="get">
        <input type="search" name="q" value="<?= e($query) ?>" placeholder="Search projects and updates…" autofocus>
        <button type="submit">Search</button>
    </form>

    <?php if ($query === ''): ?>
        <p class="muted">Enter a term to search titles, stubs, tags, and content.</p>
    <?php elseif ($results === []): ?>
        <p class="muted">No results for “<?= e($query) ?>”.</p>
    <?php else: ?>
        <ul class="entry-list">
            <?php foreach ($results as $hit): ?>
                <?php $data = $hit['data']; ?>
                <li class="entry-card">
                    <p class="meta"><?= e(ucfirst($hit['collection'])) ?></p>
                    <h2>
                        <a href="/<?= e($hit['collection']) ?>/<?= e($hit['slug']) ?>">
                            <?= e($data['title'] ?? $hit['slug']) ?>
                        </a>
                    </h2>
                    <?php if (!empty($data['stub'])): ?>
                        <p><?= e($data['stub']) ?></p>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
