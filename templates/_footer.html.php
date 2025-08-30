<?php
// print some stats
$time = round((microtime(true) - $GLOBALS['time_app_start']) * 1000.0, 1);
$memory = round(memory_get_peak_usage() / 1024 / 1024, 1);
?>
<p class="text-muted fs-6">
    Page generated in <?= esc($time); ?> ms. Peak memory use was <?= esc($memory); ?> MB.
</p>

</body></html>
