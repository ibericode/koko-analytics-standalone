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
    private array $data;
    private int $y_max = 0;

    /**
     * @param SiteStats[] $data
     */
    public function __construct(
        array $data,
        protected \DateTimeImmutable $date_start,
        protected \DateTimeImmutable $date_end
    ) {
        $this->prepareChartData($data);
    }

    public function render(int $height = 200): void
    {
        $data = $this->data;
        $y_max = $this->y_max;
        $date_start = $this->date_start;
        $date_end = $this->date_end;
        $y_max_nice = $this->getMagnitude();

        $padding_top = 6;
        $padding_bottom = 24;
        $padding_left = 4 + \strlen(\number_format($y_max_nice)) * 8;
        $inner_height = $height - $padding_top - $padding_bottom;
        $height_modifier = $y_max_nice > 0 ? $inner_height / $y_max_nice : 1;
        $date_format =  'Y-m-d';
        $empty = new SiteStats();

        require \dirname(__DIR__, 1) . '/templates/_chart.html.php';
    }

    /**
     * Transform chart data into an associative array index by the date propery
     */
    private function prepareChartData(array $data): void
    {
        $this->data = [];
        foreach ($data as $tick) {
            $this->data[$tick->date->format('Y-m-d')] = $tick;
            $this->y_max = \max($this->y_max, $tick->pageviews);
        }
    }

    private function getMagnitude(): int
    {
        $n = $this->y_max;

        if ($n < 10) {
            return 10;
        }

        if ($n > 100000) {
            return (int) \ceil($n / 10000.0) * 10000;
        }

        $e = \floor(\log10($n));
        $pow = \pow(10, $e);
        return (int) (\ceil($n / $pow) * $pow);
    }
}
