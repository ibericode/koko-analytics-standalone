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
 */

$title = 'Koko Analytics';
require __DIR__ . '/_header.html.php'; ?>

<?php /* Datepicker */ ?>

<details class="datepicker">
    <summary><?= esc($date_start->format('M j, Y')); ?> &mdash; <?= esc($date_end->format('M j, Y')); ?></summary>
    <div class="datepicker-dropdown">
        <div class="datepicker-title">
        <?= esc($date_start->format('M j, Y')); ?> &mdash; <?= esc($date_end->format('M j, Y')); ?>
        </div>
        <div class="datepicker-inner">
            <form>
                <div>
                    <label for="date-range-input">Date range</label>
                    <select name="date-range" id="date-range-input">
                        <option value="custom" disabled="">Custom</option>
                        <?php foreach ($date_ranges as $value => $label) : ?>
                            <option value="<?= esc($value); ?>" <?= $date_range === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display: flex; margin-top: 12px;">
                    <div>
                        <label for="date-start-input">Start date</label>
                        <input type="date" name="date-start" id="date-start-input" value="<?= esc($date_start->format('Y-m-d')); ?>" required>

                        &nbsp;&mdash;&nbsp;
                    </div>

                    <div>
                        <label for="date-end-input">End date</label>
                        <input type="date" name="date-end" id="date-end-input" value="<?= esc($date_end->format('Y-m-d')); ?>" required>
                    </div>
                </div>
                <div>
                    <button type="submit">View</button>
                </div>
            </form>
        </div>
    </div>
</details>


<?php
/* Site wide totals */
$visitors_change = $totals_previous->visitors == 0 ? 0 : ($totals->visitors / $totals_previous->visitors) - 1;
$pageviews_change = $totals_previous->pageviews == 0 ? 0 : ($totals->pageviews / $totals_previous->pageviews) - 1;
?>
<table class="totals">
    <tbody>

    <tr>
        <th>Total visitors</th>
        <td class="totals-amount">
            <?= number_format($totals->visitors); ?>
            <span class="totals-change <?php echo $visitors_change > 0 ? 'up' : 'down'; ?>">
                <?php echo percent_format($visitors_change); ?>
            </span>
        </td>
        <td class="totals-subtext">
            <?= number_format(abs($totals->visitors - $totals_previous->visitors)); ?>
            <?= $totals->visitors > $totals_previous->visitors ? 'more' : 'less'; ?>
            than in previous period
        </td>
    </tr>
    <tr>
        <th>Total pageviews</th>
        <td class="totals-amount">
            <?= number_format($totals->pageviews); ?>
            <span class="totals-change <?php echo $pageviews_change > 0 ? 'up' : 'down'; ?>">
                <?php echo percent_format($pageviews_change); ?>
            </span>
        </td>
        <td class="totals-subtext">
            <?= number_format(abs($totals->pageviews - $totals_previous->pageviews)); ?>
            <?= $totals->pageviews > $totals_previous->pageviews ? 'more' : 'less'; ?>
            than in previous period
        </td>
    </tr>
    </tbody>
</table>

<?php // Chart ?>
<div class="box" style="padding: 16px;">
    <div class="ka-chart">
        <?php new \App\Chart($chart, $date_start, $date_end); ?>
    </div>
</div>

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
                    <td><a href="<?= esc($p->url); ?>"><?= get_referrer_url_label(esc($p->url)); ?></a></td>
                    <td><?= number_format($p->visitors); ?></td>
                    <td><?= number_format($p->pageviews); ?></td>
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

<script src="/chart.js"></script>

<?php require __DIR__ . '/_footer.html.php'; ?>

<?php
// print some stats
$time = round((microtime(true) - $GLOBALS['time_app_start']) * 1000.0, 2);
$memory = round(memory_get_peak_usage() / 1024 / 1024, 2);
?>
<p style="color: #444; font-size: 14px;">
    Page generated in <?= esc($time); ?> ms. Peak memory use was <?= $memory; ?> MB.
</p>
