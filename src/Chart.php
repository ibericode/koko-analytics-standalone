<?php

/**
 * @package koko-analytics
 * @license AGPL-3.0+
 * @author Danny van Kooten
 */

namespace App;

use App\Entity\SiteStats;

class Chart
{
    public function __construct(array $data, \DateTimeImmutable $date_start, \DateTimeImmutable $date_end, int $height = 280)
    {
        $data = $this->transformData($data);
        $n = \count($data);
        $tick_width = $n > 0 ? 100.0 / (float) $n : 100.0;
        $y_max = 0;
        foreach ($data as $tick) {
            $y_max = \max($y_max, $tick->pageviews);
        }
        $y_max_nice = $this->getMagnitude($y_max);
        $padding_top = 6;
        $padding_bottom = 24;
        $padding_left = 4 + \strlen(\number_format($y_max_nice)) * 8;
        $inner_height = $height - $padding_top - $padding_bottom;
        $height_modifier = $y_max_nice > 0 ? $inner_height / $y_max_nice : 1;
        $date_format =  'Y-m-d';
        $empty = new SiteStats;
        ?>
        <div class="ka-chart">
            <svg width="100%" height="<?= $height; ?>" id="ka-chart">
              <g class="axes-y" transform="translate(<?= $padding_left; ?>, <?= $padding_top; ?>)" text-anchor="end" data-padding="<?= $padding_left; ?>">
                <text x="0" y="<?= $inner_height; ?>" fill="#757575" dy="0.3em" >0</text>
                <text x="0" y="<?= $inner_height / 2; ?>" fill="#757575" dy="0.3em"><?= \number_format($y_max_nice / 2); ?></text>
                <text x="0" y="0" fill="#757575" dy="0.3em"><?= \number_format($y_max_nice); ?></text>
                <line stroke="#eee" x1="8" x2="100%" y1="<?= $inner_height; ?>" y2="<?= $inner_height; ?>"></line>
                <line stroke="#eee" x1="8" x2="100%" y1="<?= $inner_height / 2; ?>" y2="<?= $inner_height / 2; ?>"></line>
                <line stroke="#eee" x1="8" x2="100%" y1="0" y2="0"></line>
              </g>
              <g class="axes-x" text-anchor="start" transform="translate(0, <?= $inner_height + 4; ?>)">
                <text fill="#757575" x="<?= $padding_left; ?>" y="10" dy="1em" text-anchor="start"><?= $date_start->format($date_format); ?></text>
                <text fill="#757575" x="100%" y="10" dy="1em" text-anchor="end"><?= $date_end->format($date_format); ?></text>
              </g>
               <g class="bars" transform="translate(0, <?= $padding_top; ?>)" style="display: none;">
                <?php for ($dt = $date_start; $dt <= $date_end; $dt = $dt->modify('+1 day')) {
                    $key = $dt->format('Y-m-d');
                    $tick = $data[$key] ?? $empty;
                    $is_weekend = (int) $dt->format('N') >= 6;
                    $class_attr = $is_weekend ? 'class="weekend" ' : '';
                    // data attributes are for the hover tooltip, which is handled in JS
                    echo '<g ', $class_attr, 'data-date="', $dt->format($date_format), '" data-pageviews="', \number_format($tick->pageviews), '" data-visitors="', \number_format($tick->visitors),'">';
                    echo '<rect class="ka--pageviews" height="', ($tick->pageviews * $height_modifier),'" y="', ($inner_height - $tick->pageviews * $height_modifier),'"></rect>';
                    echo '<rect class="ka--visitors" height="', ($tick->visitors * $height_modifier), '" y="', ($inner_height - $tick->visitors * $height_modifier), '"></rect>';
                    echo '<line stroke="#ddd" y1="', $inner_height, '" y2="', ($inner_height + 6),'"></line>';
                    echo '</g>';
                } ?>
               </g>
            </svg>
            <div class="ka-chart--tooltip" style="display: none;">
                <div class="ka-chart--tooltip-box">
                  <div class="ka-chart--tooltip-heading"></div>
                  <div style="display: flex">
                    <div class="ka-chart--tooltip-content ka--visitors">
                      <div class="ka-chart--tooltip-amount"></div>
                      <div>Visitors</div>
                    </div>
                    <div class="ka-chart--tooltip-content ka--pageviews">
                      <div class="ka-chart--tooltip-amount"></div>
                      <div>Pageviews</div>
                    </div>
                  </div>
                </div>
                <div class="ka-chart--tooltip-arrow"></div>
            </div>
        </div><?php
    }

    /**
     * Transform data into an associative array index by the date propery
     * TODO: Check if we can make PDO do this?
     */
    private function transformData(array $data): array
    {
        $result = [];
        foreach ($data as $tick) {
            $result[$tick->date->format('Y-m-d')] = $tick;
        }
        return $result;
    }

    private function getMagnitude(int $n): int
    {
        if ($n < 10) {
            return 10;
        }

        if ($n > 100000) {
            return (int) \ceil($n / 10000.0) * 10000;
        }

        $e = \floor(\log10($n));
        $pow = \pow(10, $e);
        return (int) \ceil($n / $pow) * $pow;
    }
}
