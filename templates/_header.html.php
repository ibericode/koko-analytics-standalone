<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php $this->e($title ?? ''); ?></title>
        <link rel="stylesheet" href="/styles.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>

<?php
foreach ($this->getFlashMessages() as $type => $messages) {
    echo '<div class="container my-4">';
    foreach ($messages as $message) {
        echo '<div class="alert alert-'.$type.' alert-dismissible fade show">';
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.remove();"></button>';
        echo '</div>';
    }
    echo '</div>';
}
?>
