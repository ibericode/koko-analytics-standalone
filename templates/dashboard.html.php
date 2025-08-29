<?php

/**
 * @var \DateTimeInterface $date_start
 * @var \DateTimeInterface $date_end
 * @var string $date_range
 * @var string[] $date_ranges
 * @var \App\Entity\SiteStats $totals
 * @var \App\Entity\SiteStats $totals_previous
 * @var \App\Entity\PageStats[] $pages
 * @var \App\Entity\ReferrerStats[] $referrers
 * @var \App\Chart $chart
 * @var int $realtime_count
 */

?>

<script src="/dashboard.js" defer></script>

<?php template(__DIR__ . '/_header.html.php', [ 'title' => 'Dashboards - Koko Analytics']); ?>
<div class="container py-3">
    <?php /* Datepicker */ ?>
    <details class="datepicker mb-3">
        <summary><?= esc($date_start->format('M j, Y')); ?> &mdash; <?= esc($date_end->format('M j, Y')); ?></summary>
        <div class="datepicker-dropdown mt-3" style="width: 320px;">
            <form>
                <div class="mb-2">
                    <label class="form-label" for="date-range-input">Date range</label>
                    <select class="form-select" name="date-range" id="date-range-input">
                        <option value="custom" <?= $date_range === 'custom' ? 'selected' : ''; ?> disabled>Custom</option>
                        <?php foreach ($date_ranges as $value => $label) { ?>
                            <option value="<?= esc($value); ?>" <?= $date_range === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                        <?php }; ?>
                    </select>
                </div>
                <div class="row row-cols-2 mb-2">
                    <div class="col">
                        <label class="form-label" for="date-start-input">Start date</label>
                        <input class="form-control" type="date" name="date-start" id="date-start-input" value="<?= esc($date_start->format('Y-m-d')); ?>" required>
                    </div>
                    <div class="col">
                        <label class="form-label" for="date-end-input">End date</label>
                        <input class="form-control" type="date" name="date-end" id="date-end-input" value="<?= esc($date_end->format('Y-m-d')); ?>" required>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-secondary">View</button>
                </div>
            </form>
    </div>
    </details>


    <?php
    /* Site wide totals */
    $visitors_change = $totals_previous->visitors == 0 ? 0 : ($totals->visitors / $totals_previous->visitors) - 1;
    $pageviews_change = $totals_previous->pageviews == 0 ? 0 : ($totals->pageviews / $totals_previous->pageviews) - 1;
    ?>
    <table class="mb-5  bg-dark text-white p-4 w-100 d-block rounded">
        <tbody class="d-flex">
        <tr class="me-5">
            <th class="d-block mb-2">Total visitors</th>
            <td class="d-block mb-2">
                <div class="fs-3"><?= number_format($totals->visitors); ?></div>
                <div class="totals-change <?= $visitors_change > 0 ? 'text-success' : 'text-danger'; ?>">
                    <?= percent_format($visitors_change); ?>
                </div>
            </td>
            <td class="d-block">
                <?= number_format(abs($totals->visitors - $totals_previous->visitors)); ?>
                <?= $totals->visitors > $totals_previous->visitors ? 'more' : 'less'; ?>
                than in previous period
            </td>
        </tr>
        <tr class="me-5">
            <th class="d-block mb-2">Total pageviews</th>
            <td class="d-block mb-2">
                <div class="fs-3"><?= number_format($totals->pageviews); ?></div>
                <div class="totals-change <?= $pageviews_change > 0 ? 'up' : 'down'; ?>">
                    <?= percent_format($pageviews_change); ?>
                </div>
            </td>
            <td class="d-block text-light">
                <?= number_format(abs($totals->pageviews - $totals_previous->pageviews)); ?>
                <?= $totals->pageviews > $totals_previous->pageviews ? 'more' : 'less'; ?>
                than in previous period
            </td>
        </tr>
        <tr>
            <th class="d-block mb-2">Realtime pageviews</th>
            <td class="d-block mb-2 fs-3">
                <?= number_format($realtime_count); ?>
            </td>
            <td class="d-block">
                pageviews in the last hour
            </td>
        </tr>
        </tbody>
    </table>

    <?php /* Chart */ ?>
    <div class="mb-3 chart">
        <?php $chart->render(); ?>
    </div>

    <div class="row row-cols-2 g-3">
        <?php /* Page stats */ ?>
        <div class="box">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-muted" style="width: 3ch;">#</th>
                    <th>Page</th>
                    <th class="text-end" style="width: 6ch; min-width: fit-content;">Visitors</th>
                    <th class="text-end" style="width: 6ch; min-width: fit-content;">Pageviews</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $rank => $p) { ?>
                    <tr>
                        <td class="text-muted"><?= $rank + 1; ?></td>
                        <td><a href=""><?= esc($p->url); ?></a></td>
                        <td class="text-end"><?= number_format($p->visitors); ?></td>
                        <td class="text-end"><?= number_format($p->pageviews); ?></td>
                    </tr>
                <?php }; ?>
                <?php if (empty($pages)) : ?>
                    <tr>
                        <td colspan="4">There is nothing here. Yet!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>

        <?php /* Referrer stats */ ?>
        <div class="box">
        <table class="table">
            <thead>
                <tr>
                    <th class="text-muted" style="width: 3ch;">#</th>
                    <th>Referrer</th>
                    <th class="text-end" style="width: 6ch; min-width: fit-content;">Visitors</th>
                    <th class="text-end" style="width: 6ch; min-width: fit-content;">Pageviews</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($referrers as $rank => $p) : ?>
                    <tr>
                        <td class="text-muted"><?= $rank + 1; ?></td>
                        <td><?= get_referrer_url_label(esc($p->url)); ?></td>
                        <td class="text-end"><?= number_format($p->visitors); ?></td>
                        <td class="text-end"><?= number_format($p->pageviews); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($referrers)) : ?>
                    <tr>
                        <td colspan="4">There is nothing here. Yet!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>


<?php require __DIR__ . '/_footer.html.php'; ?>
</div>
