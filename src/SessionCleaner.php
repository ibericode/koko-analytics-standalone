<?php

namespace App;

/**
 * @package koko-analytics
 *
 * SessionCleaner removes all files from /var/sessions which are older than 6 hours.
 *
 * This ensures that a visitor that did not visit any new paths within this 6 hour window is treated as a new visitor and/or unique pageview.
 */
class SessionCleaner {
    public function __invoke() {
        $session_directory = \dirname(__DIR__, 1) . '/var/sessions';
        if (!\is_dir($session_directory)) {
            \mkdir($session_directory, 0755);
            return;
        }

        $files = \glob("{$session_directory}/*");
        $cutoff_time =  \time() - (6 * 3600);
        foreach ($files as $filename) {
            if (\filemtime($filename) < $cutoff_time) {
                \unlink($filename);
            }
        }
    }
}
