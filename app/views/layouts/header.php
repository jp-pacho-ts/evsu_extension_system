<?php
if(session_status()===PHP_SESSION_NONE) session_start();

$displayRole = $_SESSION['role'] ?? 'Guest';
$approvalCount = isset($db) ? pendingApprovalCount($db) : 0;
$totalNoticeCount = $approvalCount;
$currentPage = $_GET['page'] ?? 'dashboard';
$fullName = trim($_SESSION['fullname'] ?? 'User');

$pageTitles = [
    'approval_center' => 'Approval Center',
    'approvals' => 'Approval Queue',
    'dashboard' => 'Executive Monitoring Dashboard',
    'programs' => 'Program Management',
    'projects' => 'Project Management',
    'monitoring' => 'Monitoring Records',
    'quarterly_reports' => 'Quarterly Reports',
    'edit_quarterly_report' => 'Quarterly Report',
    'view_quarterly_report' => 'Quarterly Report',
    'report' => 'Prescriptive Report',
    'map' => 'GIS Map',
    'users' => 'User Management',
    'logs' => 'Activity Logs',
    'notifications' => 'Notifications',
];

$pageTitle = $pageTitles[$currentPage] ?? 'EVSU Extension Services';
$activeClass = function(array $pages) use ($currentPage) {
    return in_array($currentPage, $pages, true) ? ' active' : '';
};
$menuAllowed = function(array $roles) {
    return hasRole($roles);
};

$nameParts = preg_split('/\s+/', $fullName);
$initials = '';
foreach($nameParts as $part) {
    if($part === '') continue;
    $initials .= strtoupper(substr($part, 0, 1));
    if(strlen($initials) >= 2) break;
}
if($initials === '') $initials = 'EV';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>EVSU Extension Services Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="public/assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar no-print" aria-label="Main navigation">
        <div class="sidebar-brand">
            <div class="brand-mark" aria-hidden="true">ES</div>
            <div>
                <h1>EVSU Extension</h1>
                <p>Services Platform</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <p class="sidebar-label">Workspace</p>
            <a class="sidebar-link<?= $activeClass(['approval_center','approvals']) ?>" href="index.php?page=approval_center">
                <span>Approval Center</span>
                <?php if($totalNoticeCount > 0): ?>
                    <span class="notice-badge"><?= $totalNoticeCount ?></span>
                <?php endif; ?>
            </a>

            <?php if($menuAllowed(['Super Admin','Admin','School Coordinator','Campus Director','VP ORIES','Reviewer'])): ?>
                <a class="sidebar-link<?= $activeClass(['dashboard']) ?>" href="index.php?page=dashboard">Dashboard</a>
            <?php endif; ?>

            <?php if($menuAllowed(['Department Coordinator','Extension Staff','Super Admin','Admin'])): ?>
                <p class="sidebar-label">Records</p>
                <a class="sidebar-link<?= $activeClass(['programs']) ?>" href="index.php?page=programs">Programs</a>
                <a class="sidebar-link<?= $activeClass(['projects']) ?>" href="index.php?page=projects">Projects</a>
            <?php endif; ?>

            <?php
                $canViewMonitoringRecords = $menuAllowed(['Department Coordinator','Extension Staff','School Coordinator','Campus Director','VP ORIES','Super Admin','Admin']);
                $canViewQuarterlyReports = $menuAllowed(['Department Coordinator','Extension Staff','School Coordinator','Campus Director','VP ORIES','Super Admin','Admin','Faculty']);
            ?>
            <?php if($canViewMonitoringRecords || $canViewQuarterlyReports): ?>
                <p class="sidebar-label">Monitoring</p>
                <?php if($canViewMonitoringRecords): ?>
                    <a class="sidebar-link<?= $activeClass(['monitoring']) ?>" href="index.php?page=monitoring">Monitoring</a>
                <?php endif; ?>
                <?php if($canViewQuarterlyReports): ?>
                    <a class="sidebar-link<?= $activeClass(['quarterly_reports','edit_quarterly_report','view_quarterly_report']) ?>" href="index.php?page=quarterly_reports">Quarterly Reports</a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($menuAllowed(['Super Admin','Admin','School Coordinator','Campus Director','Extension Director','VP ORIES'])): ?>
                <p class="sidebar-label">Reports</p>
                <a class="sidebar-link<?= $activeClass(['report']) ?>" href="index.php?page=report">Prescriptive Report</a>
            <?php endif; ?>

            <?php if($menuAllowed(['Super Admin','Admin','Campus Director','VP ORIES'])): ?>
                <a class="sidebar-link<?= $activeClass(['map']) ?>" href="index.php?page=map">GIS Map</a>
            <?php endif; ?>

            <?php if($menuAllowed(['Super Admin','Admin'])): ?>
                <p class="sidebar-label">Administration</p>
                <a class="sidebar-link<?= $activeClass(['users']) ?>" href="index.php?page=users">User Management</a>
                <a class="sidebar-link<?= $activeClass(['logs']) ?>" href="index.php?page=logs">Activity Logs</a>
            <?php endif; ?>
        </nav>

    </aside>

    <main class="content">
        <header class="page-header no-print">
            <div>
                <p class="header-kicker">Eastern Visayas State University</p>
                <h1><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
            <div class="header-account">
                <div class="account-avatar" aria-hidden="true"><?= htmlspecialchars($initials) ?></div>
                <div>
                    <p><?= htmlspecialchars($fullName) ?></p>
                    <span><?= htmlspecialchars($displayRole) ?></span>
                </div>
                <a class="header-logout" href="index.php?page=logout">Logout</a>
            </div>
        </header>
