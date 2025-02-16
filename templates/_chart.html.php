<div class="ka-chart">
    <svg width="100%" height="<?= $height; ?>">
      <g class="axes-y" transform="translate(<?= $padding_left; ?>, <?= $padding_top; ?>)" text-anchor="end">
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
        <?php for ($dt = $date_start; $dt <= $date_end; $dt = $dt->modify('+1 day')) :
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
        endfor; ?>
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
    </div><?php /* end .ka-chart--tooltip */ ?>
</div><?php /* end .ka-chart */
