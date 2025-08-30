<?php template(__DIR__ . '/_header.html.php', [ 'title' => 'Dashboards - Koko Analytics']); ?>

<div class="container py-5">

<h1 class="fs-3"><img class="mb-4 align-top me-2" src="/icon-128x128.png" alt="" width="32" height="32">
 Choose a domain</h1>

<ul class="list-group mb-5">
<?php foreach ($domains as $domain) : ?>
    <li class="list-group-item"><a href="<?= esc($this->generateUrl('app_dashboard', [ 'domain' => $domain->getName() ])); ?>"><?= esc($domain->getName()); ?></a></li>
<?php endforeach; ?>
</ul>

<?php require __DIR__ . '/_footer.html.php'; ?>

</div>
