<?php
$title = 'Koko Analytics';
require __DIR__ . '/_header.html.php'; ?>


<?php /* Site wide totals */ ?>
<table class="totals">
    <tbody>
    <tr>
        <th>Total visitors</th>
        <td><?= number_format($totals->visitors); ?></td>
    </tr>
    <tr>
        <th>Total pageviews</th>
        <td><?= number_format($totals->pageviews); ?></td>
    </tr>
    </tbody>
</table>

<div class="boxes">
    <?php /* Page stats */ ?>
    <div class="box">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Page</th>
                <th>Visitors</th>
                <th>Pageviews</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $rank => $p) : ?>
                <tr>
                    <td><?= $rank + 1; ?></td>
                    <td><a href=""><?= esc($p->url); ?></a></td>
                    <td><?= number_format($p->visitors); ?></td>
                    <td><?= number_format($p->pageviews); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php /* Referrer stats */ ?>
    <div class="box">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Referrer</th>
                <th>Visitors</th>
                <th>Pageviews</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($referrers as $rank => $p) : ?>
                <tr>
                    <td><?= $rank + 1; ?></td>
                    <td><a href="<?= esc($p->url); ?>"><?= esc($p->url); ?></a></td>
                    <td><?= number_format($p->visitors); ?></td>
                    <td><?= number_format($p->pageviews); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php require __DIR__ . '/_footer.html.php'; ?>

<?php
// print some stats
$time = round((microtime(true) - $GLOBALS['time_app_start']) * 1000.0, 2);
$memory = round(memory_get_peak_usage() / 1024 / 1024, 2);
?>
<p style="color: #444; font-size: 14px;">
    Page generated in <?= esc($time); ?> ms. Peak memory use was <?= $memory; ?> MB.
</p>
