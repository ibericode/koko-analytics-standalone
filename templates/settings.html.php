<?php

$title = 'Koko Analytics';
require __DIR__ . '/_header.html.php'; ?>

<div class="container">

<h1>Settings</h1>
<p class="mb-3">Configuration settings for <strong><?= esc($domain->getName()); ?></strong>.</p>

<form method="post">
<table class="table mb-3">
    <tbody>
        <tr>
            <th><label class="form-label" for="select-timezone">Timezone</label></th>
            <td>
                <select class="form-select" name="settings[timezone]" id="select-timezone">
                    <option>UTC</option>
                </select>
            </td>
        </tr>

        <tr>
            <th><label for="textarea-excluded-ips">Ignored IP addresses</label></th>
            <td>
                <textarea class="form-control" name="settings[excluded_ip_addresses]" id="textarea-excluded-ips"><?php echo esc($settings['excluded_ip_addresses']); ?></textarea>
            </td>
        </tr>

        <tr>
            <th><label for="input-retention">Retention</label></th>
            <td>
                <input class="form-control" name="settings[purge_treshold]" id="input-retention" type="number" step="1" min="1" value="<?php echo esc($settings['purge_treshold']); ?>">
            </td>
        </tr>
        <tr>
            <th></th>
            <td><input class="btn btn-primary" type="submit" value="Save Changes"></td>
        </tr>
    </tbody>
</table>
</form>

<p>Back to <a href="/<?= $domain->getName(); ?>">analytics dashboard</a>.</p>

<?php require __DIR__ . '/_footer.html.php'; ?>

</div>
