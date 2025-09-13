<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in - Koko Analytics</title>
    <link rel="stylesheet" href="/styles.css">
    <meta name="theme-color" content="#712cf9">
<style>
html,
body {
  height: 100%;
}

.form-signin {
  max-width: 330px;
  padding: 1rem;
}

.form-signin .form-floating:focus-within {
  z-index: 2;
}

.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}

.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>
</head>
<body class="bg-body-tertiary py-4">

<div class="d-flex align-items-center h-100">
    <main class="form-signin w-100 m-auto">
        <form method="post" action="/login">
            <img class="mb-4" src="/icon-128x128.png" alt="" width="57" height="57">
            <h1 class="h3 mb-3 fw-normal">Log in</h1>
            <div class="mb-3 error"><?php $this->e($error); ?></div>
            <div class="form-floating">
                <input type="email" name="_username" class="form-control" id="floatingInput" placeholder="name@example.com" value="<?php $this->e($last_username); ?>">
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating"> <input type="password" name="_password" class="form-control" id="floatingPassword" placeholder="Password"> <label for="floatingPassword">Password</label> </div>
             <button class="btn btn-primary w-100 py-2" type="submit">Log in</button>
            <p class="mt-5 mb-3 text-body-secondary">&copy; <?php $this->e(date('Y')); ?> &mdash; Koko Analytics</p>
            <?php $this->partial('_performance.html.php'); ?>
        </form>
    </main>
</div>
</body>
</html>
