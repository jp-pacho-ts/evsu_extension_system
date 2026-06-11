<?php
$appName = function_exists('systemName') ? systemName() : 'GESESP-DA';
$appSubtitle = function_exists('systemSubtitle') ? systemSubtitle() : 'Extension Services Data Analytics';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($appName) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar no-print" aria-label="Public navigation">
        <div class="sidebar-brand">
            <div class="brand-mark" aria-hidden="true">GE</div>
            <div>
                <h1><?= htmlspecialchars($appName) ?></h1>
                <p><?= htmlspecialchars($appSubtitle) ?></p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <p class="sidebar-label">Public Access</p>
            <a class="sidebar-link active" href="quarterly_report.php">Quarterly Report</a>
            <a class="sidebar-link" href="index.php?page=dashboard">Dashboard</a>
            <a class="sidebar-link" href="index.php?page=login">Login</a>
        </nav>
    </aside>

    <main class="content">
        <header class="page-header no-print">
            <div>
                <p class="header-kicker">Eastern Visayas State University</p>
                <h1>Quarterly Report</h1>
            </div>
        </header>
