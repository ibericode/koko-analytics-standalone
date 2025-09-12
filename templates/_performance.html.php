<p class="text-body-secondary mt-3">
Page generated in <?= round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 1) ?> ms.<br>
Peak memory <?= memory_get_peak_usage(true) >> 20; ?> MB.
</p>
