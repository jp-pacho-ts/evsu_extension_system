<?php
$appName = function_exists('systemName') ? systemName() : 'GESESP-DA';
$appSubtitle = function_exists('systemSubtitle') ? systemSubtitle() : 'Extension Services Data Analytics';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - <?= htmlspecialchars($appName) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/assets/css/style.css?v=<?= filemtime('public/assets/css/style.css') ?>">
</head>
<body class="login-page">
<div class="login-shell">
    <div class="login-card">
        <div class="login-brand">
            <div class="login-mark" aria-hidden="true">GE</div>
            <div>
                <h1><?= htmlspecialchars($appName) ?></h1>
                <p><?= htmlspecialchars($appSubtitle) ?></p>
            </div>
        </div>

        <div class="login-heading">
            <h2>Sign in</h2>
            <p>Use your GESESP-DA account to continue.</p>
        </div>

        <?php if($error): ?>
            <div class="login-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <label for="login-username">Username</label>
            <input id="login-username" type="text" name="username" required>

            <label for="login-password">Password</label>
            <input id="login-password" type="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <div class="login-demo">
            admin1/admin123<br>
            staff/staff123<br>
            schoolcoord/school123
        </div>
    </div>
</div>
</body>
</html>
