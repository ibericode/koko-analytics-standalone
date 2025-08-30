<?php template(__DIR__ . '/_header.html.php', [ 'title' => 'Add domain - Koko Analytics']); ?>

<div class="container py-5">

<h1 class="fs-3 mb-4">
    <img class="align-top me-2" src="/icon-128x128.png" alt="" width="32" height="32">
    Add new domain
</h1>

<form method="post" action="">

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input class="form-control" type="text" name="name" minlength="1" pattern="[a-zA-Z0-9\-\.]+" required>
        <div class="text-muted">Enter your domain name. Use only alphanumeric characters, hyphens or dots.</div>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Add</button>
    </div>

</form>


<?php require __DIR__ . '/_footer.html.php'; ?>

</div>
