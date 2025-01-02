<?php
/**
 * @var \App\Entity\Domain[] $domains
 */

$title = 'Dashboards - Koko Analytics';
require __DIR__ . '/_header.html.php'; ?>


<h1>Choose a domain</h1>

<ul>
<?php foreach ($domains as $domain) : ?>
    <li><a href="<?= $this->generateUrl('app_dashboard', [ 'domain' => $domain->getName() ]); ?>"><?= esc($domain->getName()); ?></a></li>
<?php endforeach; ?>
</ul>

<?php require __DIR__ . '/_footer.html.php'; ?>
