<?php
// print some stats
$time = round((microtime(true) - $GLOBALS['time_app_start']) * 1000.0, 2);
$memory = round(memory_get_peak_usage() / 1024 / 1024, 2);
?>
<p style="color: #444; font-size: 14px;">
    Page generated in <?= esc($time); ?> ms. Peak memory use was <?= esc($memory); ?> MB.
</p>

</body></html>
