<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login - Koko Analytics</title>
        <link rel="stylesheet" href="/style.css">
        <style>
body, html { height: 100%; }
body {
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    height: auto;
    min-height: 100%;
}

.login {
    width: 320px;
    padding: 24px;
    margin: 72px auto;
    background: #fff;
    position: relative;
    -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, .2);
    box-shadow: 0 1px 2px rgba(0, 0, 0, .2);
    text-wrap: pretty;
}

.login h1 {
    text-align: center;
    margin-bottom: 24px;
    position: absolute;
    top: -100px;
    width: 100%;
    left: 0;
}

.login h1 a {
  background-image: none, url('/icon-128x128.png');
  background-size: 64px;
  background-position: center top;
  background-repeat: no-repeat;
  color: #999;
  height: 64px;
  margin: 0 auto;
  padding: 0;
  width: auto;
  text-indent: -9999px;
  outline: none;
  overflow: hidden;
  display: block;
  transition: none;
}

form > div { margin-bottom: 16px; }
form > div:last-of-type { margin-bottom: 0; }
input {
    width: 100%;
    font-size: 18px;
}
button {
    width: 100%;
    padding: 8px 16px;
}
#nav {
  font-size: 14px;
  padding: 0;
  margin: 24px auto;
  text-align: center;
  position: absolute;
  left: 0;
  bottom: -68px;
  width: 100%;
}

#nav a {
    color: #50575e;
}
</style>
</head>
<body>
<div class="login">
    <h1><a href="">Powered by Koko Analytics</a></h1>
    <?php if ($error) { ?>
        <p><?= esc($error); ?></p>
    <?php } ?>

    <form method="post" action="">
        <div>
            Log into your Koko Analytics dashboard to view your website statistics.
        </div>
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

    <p id="nav">
        <a href="">Lost password?</a>
    </p>
</div>

<?php require __DIR__ . '/_footer.html.php'; ?>
