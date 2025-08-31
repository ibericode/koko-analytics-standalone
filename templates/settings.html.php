<?php $this->partial('_header.html.php', [ 'title' => 'Settings - Koko Analytics']); ?>

<div class="container py-3">

    <p class="mb-3"><a href="<?= esc($this->generateUrl('app_dashboard', [ 'domain' => $domain->getName() ])); ?>">← Back to analytics dashboard</a>.</p>

    <h1>Settings</h1>
    <p class="mb-4">Configuration settings for <strong><?= esc($domain->getName()); ?></strong>.</p>

    <form method="post" action="" class="mb-5">
        <table class="table table-borderless mb-5">
            <tbody>
                <tr>
                    <th><label class="form-label" for="select-timezone">Timezone</label></th>
                    <td>
                        <select class="form-select mb-2" name="domain[timezone]" id="select-timezone">
                            <?php foreach (\DateTimeZone::listIdentifiers() as $timezone) { ?>
                                <option <?= $domain->getTimezone() === $timezone ? 'selected' : ''; ?>><?= esc($timezone); ?></option>
                            <?php } ?>
                        </select>
                        <div class="text-muted">Select your site's timezone.</div>
                    </td>
                </tr>
                <tr>
                    <th><label class="form-label" for="textarea-excluded-ips">Ignored IP addresses</label></th>
                    <td>
                        <textarea class="form-control mb-2" name="domain[excluded_ip_addresses]" id="textarea-excluded-ips" rows="8'"><?php echo esc(join("\n", $domain->getExcludedIpAddresses())); ?></textarea>
                        <div class="text-muted">Enter a list of IP addresses to ignore. Separate addresses by a new line.</div>
                    </td>
                </tr>

                <tr>
                    <th><label class="form-label" for="input-retention">Retention</label></th>
                    <td>
                        <input class="form-control mb-2" name="domain[purge_treshold]" id="input-retention" type="number" step="1" min="30" value="<?php echo esc($domain->getPurgeTreshold()); ?>">
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
        <form method="POST" action="<?= esc($this->generateUrl('app_dashboard_delete', [ 'domain' => $domain->getName() ])); ?>" onsubmit="return confirm('Are you sure you want to completely delete this domain and all accompanying data?')">
            <button type="submit" class="btn btn-danger btn-sm">Delete <?= esc($domain->getName()) ?></button>
        </form>
    </div>

    <?php require __DIR__ . '/_footer.html.php'; ?>

</div>
