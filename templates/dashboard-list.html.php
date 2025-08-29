<?php (require __DIR__ . '/_header.html.php')('Dashboards - Koko Analytics'); ?>

<h1>Choose a domain</h1>

<ul>
<?php foreach ($domains as $domain) : ?>
    <li><a href="<?= esc($this->generateUrl('app_dashboard', [ 'domain' => $domain->getName() ])); ?>"><?= esc($domain->getName()); ?></a></li>
<?php endforeach; ?>
</ul>

<?php require __DIR__ . '/_footer.html.php'; ?>
