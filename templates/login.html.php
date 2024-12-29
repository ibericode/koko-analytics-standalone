<?php require __DIR__ . '/_header.html.php'; ?>
<h1>Login</h1>

<?php if ($error) { ?>
    <p><?= esc($error); ?></p>
<?php } ?>

<form method="post" action="">
    <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="_username" value="<?= esc($last_username); ?>" required>
    </div>

    <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="_password" required>
    </div>

    <div>
        <button type="submit">Log in</button>
    </div>
</form>
<?php require __DIR__ . '/_footer.html.php'; ?>
