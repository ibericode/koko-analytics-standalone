<?php

$title = 'Koko Analytics';
require __DIR__ . '/_header.html.php'; ?>


<style>
    .form-table th { text-align: left; }
    .form-table td { padding: 1em; }

    select, textarea, input {
        padding: 6px 12px;
        width: 260px;
        box-sizing: border-box;
    }
    input[type="submit"] {
        background: indianred;
        color: white;
        border: 0;
        width: auto;
    }
</style>

<h1>Settings</h1>
<p>Configuration settings for <strong><?= esc($domain->getName()); ?></strong>.</p>

<form method="post">
<table class="form-table">
    <tbody>
        <tr>
            <th><label>Timezone</label></th>
            <td>
                <select name="settings[timezone]">
                    <option>UTC</option>
                </select>
            </td>
        </tr>

        <tr>
            <th><label>Ignored IP addresses</label></th>
            <td>
                <textarea name="settings[excluded_ip_addresses]"><?php echo esc($settings['excluded_ip_addresses']); ?></textarea>
            </td>
        </tr>

        <tr>
            <th><label>Retention</label></th>
            <td><input name="settings[purge_treshold]" type="number" step="1" min="1" value="<?php echo esc($settings['purge_treshold']); ?>"></td>
        </tr>
        <tr>
            <th></th>
            <td><input type="submit" value="Save Changes"></td>
        </tr>
    </tbody>
</table>
</form>

<p>Back to <a href="/<?= $domain->getName(); ?>">analytics dashboard</a>.</p>

<?php require __DIR__ . '/_footer.html.php';
