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

<?php $this->partial('_header.html.php', [ 'title' => "{$domain->getName()} - Koko Analytics"]); ?>

<div class="container py-3">
    <div class="d-md-flex flex-row flex-wrap justify-content-between mb-3">
    <?php /* Datepicker */ ?>
    <div class="d-flex">
        <details class="datepicker position-relative">
            <summary class="bg-body-tertiary py-2 px-3 bordered rounded me-3"><?php $this->e($date_start->format('M j, Y')); ?> &mdash; <?php $this->e($date_end->format('M j, Y')); ?></summary>
            <div class="mt-2 position-absolute bg-white p-3 border rounded shadow" style="width: 320px;">
                <form method="get" action="" class="mb-0">
                    <div class="mb-2">
                        <label class="form-label" for="date-range-input">Date range</label>
                        <select class="form-select" name="date-range" id="date-range-input">
                            <option value="custom" <?= $date_range === 'custom' ? 'selected' : ''; ?> disabled>Custom</option>
                            <?php foreach ($date_ranges as $value => $label) { ?>
                                <option value="<?php $this->e($value); ?>" <?= $date_range === $value ? 'selected' : ''; ?>><?php $this->e($label); ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                    <div class="row row-cols-2 mb-2">
                        <div class="col">
                            <label class="form-label" for="date-start-input">Start date</label>
                            <input class="form-control" type="date" name="date-start" id="date-start-input" value="<?php $this->e($date_start->format('Y-m-d')); ?>" required>
                        </div>
                        <div class="col">
                            <label class="form-label" for="date-end-input">End date</label>
                            <input class="form-control" type="date" name="date-end" id="date-end-input" value="<?php $this->e($date_end->format('Y-m-d')); ?>" required>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-secondary">View</button>
                    </div>
                </form>
            </div>
        </details>

        <?php if ($path) { ?>
            <div class="bg-body-tertiary py-2 px-3 bordered rounded">
                Path = <span class="fw-bold"><?php $this->e($path) ?></span>
                <a class="btn-close ms-3" href="<?php $this->e($this->generateUrl('app_dashboard', ['path' => null, ...$url_params ])) ?>"></a>
            </div>
        <?php } ?>
    </div>

        <div class="mb-3 mb-md-0">
            <select class="form-select d-inline-block me-2 w-auto" onchange="window.location = this.value;">
            <?php foreach ($domains as $d) : ?>
                <option value="<?php $this->e($this->generateUrl('app_dashboard', [ 'domain' => $d->getName() ])); ?>" <?= $domain->getName() == $d->getName() ? 'selected' : '' ?>><?php $this->e($d->getName()); ?></option>
            <?php endforeach; ?>
            </select>

            <a href="/<?= $domain->getName(); ?>/settings"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000" class="bi bi-gear" viewBox="0 0 16 16"><path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/></svg></a>
        </div>
    </div>


    <?php
    /* Site wide totals */
    $visitors_change = $totals_previous->visitors == 0 ? 0 : ($totals->visitors / $totals_previous->visitors) - 1;
    $pageviews_change = $totals_previous->pageviews == 0 ? 0 : ($totals->pageviews / $totals_previous->pageviews) - 1;
    ?>
    <table class="mb-4 bg-dark text-white p-4 w-100 d-block rounded">
        <tbody class="d-flex flex-row flex-wrap gap-5">
        <tr class="me-5">
            <th class="d-block mb-2">Total visitors</th>
            <td class="d-block mb-2">
                <div class="fs-2"><?= number_format($totals->visitors); ?>
                    <span class="fs-6 align-middle ms-2 <?= $visitors_change > 0 ? 'text-green' : 'text-red'; ?>">
                        <?= $this->percent_format($visitors_change); ?>
                    </span>
                </div>
            </td>
            <td class="d-block text-white-80">
                <?= number_format(abs($totals->visitors - $totals_previous->visitors)); ?>
                <?= $totals->visitors > $totals_previous->visitors ? 'more' : 'less'; ?>
                than in previous period
            </td>
        </tr>
        <tr class="me-5">
            <th class="d-block mb-2">Total pageviews</th>
            <td class="d-block mb-2">
                <div class="fs-2"><?= number_format($totals->pageviews); ?>
                    <span class="fs-6 align-middle ms-2 <?= $visitors_change > 0 ? 'text-green' : 'text-red'; ?>">
                        <?= $this->percent_format($pageviews_change); ?>
                    </span>
                </div>
            </td>
            <td class="d-block text-white-80">
                <?= number_format(abs($totals->pageviews - $totals_previous->pageviews)); ?>
                <?= $totals->pageviews > $totals_previous->pageviews ? 'more' : 'less'; ?>
                than in previous period
            </td>
        </tr>
        <tr>
            <th class="d-block mb-2">Realtime pageviews</th>
            <td class="d-block mb-2 fs-2">
                <?= number_format($realtime_count); ?>
            </td>
            <td class="d-block text-white-80">
                pageviews in the last hour
            </td>
        </tr>
        </tbody>
    </table>

    <?php /* Chart */ ?>
    <div class="mb-4 chart">
        <?php $chart->render(); ?>
    </div>

    <div class="row row-cols-lg-2 g-4">
        <?php /* Page stats */ ?>
        <div class="">
        <table class="table table-fixed">
            <thead>
                <tr>
                    <th class="text-muted" style="width: 3ch;">#</th>
                    <th>Page</th>
                    <th class="text-end d-none d-sm-table-cell" style="width: 6ch; min-width: fit-content;">Visitors</th>
                    <th class="text-end" style="width: 6ch; min-width: fit-content;">Pageviews</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $rank => $p) { ?>
                    <tr>
                        <td class="text-muted"><?= $rank + 1; ?></td>
                        <td class="text-truncate"><a href="<?php $this->e($this->generateUrl('app_dashboard', ['path' => $p->url, ...$url_params])) ?>"><?php $this->e($p->url); ?></a></td>
                        <td class="text-end d-none d-sm-table-cell"><?= number_format($p->visitors); ?></td>
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
        <table class="table table-fixed">
            <thead>
                <tr>
                    <th class="text-muted" style="width: 3ch;">#</th>
                    <th>Referrer</th>
                    <th class="text-end d-none d-sm-table-cell" style="width: 6ch; min-width: fit-content;">Visitors</th>
                    <th class="text-end" style="width: 6ch; min-width: fit-content;">Pageviews</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($referrers as $rank => $p) : ?>
                    <tr>
                        <td class="text-muted"><?= $rank + 1; ?></td>
                        <td class="text-truncate"><?php $this->e($this->get_referrer_url_label($p->url)); ?></td>
                        <td class="text-end d-none d-sm-table-cell"><?= number_format($p->visitors); ?></td>
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
</div>
<?php $this->partial('_footer.html.php', []); ?>
