<?php $this->partial('_header.html.php', [ 'title' => 'Settings - Koko Analytics']); ?>

<div class="container py-3">

    <p class="mb-3"><a href="<?= esc("/{$domain->getName()}"); ?>">← Back to analytics dashboard</a>.</p>

    <h1>Settings</h1>
    <p class="mb-4">Configuration settings for <strong><?= esc($domain->getName()); ?></strong>.</p>

    <form method="post" action="" class="mb-5">
        <table class="table table-borderless mb-5">
            <tbody>
                <tr>
                    <th><label class="form-label" for="select-timezone">Timezone</label></th>
                    <td>
                        <select class="form-select" name="settings[timezone]" id="select-timezone">
                            <?php foreach (\DateTimeZone::listIdentifiers() as $timezone) { ?>
                                <option <?= $settings['timezone'] === $timezone ? 'selected' : ''; ?>><?= esc($timezone); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label class="form-label" for="textarea-excluded-ips">Ignored IP addresses</label></th>
                    <td>
                        <textarea class="form-control" name="settings[excluded_ip_addresses]" id="textarea-excluded-ips" rows="8'"><?php echo esc($settings['excluded_ip_addresses']); ?></textarea>
                        <div class="text-muted">Enter a list of IP addresses to ignore. Separate addresses by a new line.</div>
                    </td>
                </tr>

                <tr>
                    <th><label class="form-label" for="input-retention">Retention</label></th>
                    <td>
                        <input class="form-control" name="settings[purge_treshold]" id="input-retention" type="number" step="1" min="30" value="<?php echo esc($settings['purge_treshold']); ?>">
                        <div class="text-muted">After how many days should data be purged? </div>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td><input class="btn btn-primary" type="submit" value="Save Changes"></td>
                </tr>
            </tbody>
        </table>
    </form>

    <div class="mb-5">
        <h3>Delete domain</h3>
        <p>You can completely remove this domain and all of its data using the button below.</p>
        <form method="POST" action="<?= esc("/{$domain->getName()}/delete"); ?>" onsubmit="return confirm('Are you sure you want to completely delete this domain and all accompanying data?')">
            <button type="submit" class="btn btn-danger btn-sm">Delete <?= esc($domain->getName()) ?></button>
        </form>
    </div>

    <?php require __DIR__ . '/_footer.html.php'; ?>

</div>
