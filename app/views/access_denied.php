<?php include "app/views/layouts/header.php"; ?>
<div class="card p-5 text-center">
    <h2 class="fw-bold text-danger">Access Denied</h2>
    <p class="text-muted">Your current account role is not allowed to access this module.</p>
    <p><b>Your Role:</b> <?= htmlspecialchars($_SESSION['role'] ?? 'Guest') ?></p>
    <a href="<?= roleLandingPage() ?>" class="btn btn-primary">Back</a>
</div>
<?php include "app/views/layouts/footer.php"; ?>
