<?php
if(session_status()===PHP_SESSION_NONE) session_start();

$displayRole = $_SESSION['role'] ?? 'Guest';
$approvalCount = isset($db) ? pendingApprovalCount($db) : 0;
$notificationCount = isset($db) ? unreadNotificationCount($db) : 0;
$headerNotifications = [];
$totalNoticeCount = $approvalCount;
$currentPage = $_GET['page'] ?? 'dashboard';
$currentUrl = $_SERVER['REQUEST_URI'] ?? 'index.php?page='.$currentPage;
$fullName = trim($_SESSION['fullname'] ?? 'User');

if(isset($db) && isset($_SESSION['user_id'])) {
    require_once 'app/models/Notification.php';
    $notificationModel = new Notification($db);
    $headerNotifications = $notificationModel->forUser($_SESSION['user_id'], 8);
}

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

$pageTitle = $pageTitles[$currentPage] ?? systemName();
$activeClass = function(array $pages) use ($currentPage) {
    return in_array($currentPage, $pages, true) ? ' active' : '';
};
$menuAllowed = function(array $roles) {
    return hasRole($roles);
};
$canUseApprovalCenter = canAccessApprovalCenter();

$nameParts = preg_split('/\s+/', $fullName);
$initials = '';
foreach($nameParts as $part) {
    if($part === '') continue;
    $initials .= strtoupper(substr($part, 0, 1));
    if(strlen($initials) >= 2) break;
}
if($initials === '') $initials = 'GE';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars(systemName()) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="public/assets/css/style.css?v=<?= filemtime('public/assets/css/style.css') ?>">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar no-print" aria-label="Main navigation">
        <div class="sidebar-brand">
            <div class="brand-mark" aria-hidden="true">GE</div>
            <div>
                <h1><?= htmlspecialchars(systemName()) ?></h1>
                <p><?= htmlspecialchars(systemSubtitle()) ?></p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <p class="sidebar-label">Workspace</p>
            <?php if($canUseApprovalCenter): ?>
                <a class="sidebar-link<?= $activeClass(['approval_center','approvals']) ?>" href="index.php?page=approval_center">
                    <span>Approval Center</span>
                    <?php if($totalNoticeCount > 0): ?>
                        <span class="notice-badge"><?= $totalNoticeCount ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <?php if($menuAllowed(['Super Admin','Admin','Extension Director','Reviewer'])): ?>
                <a class="sidebar-link<?= $activeClass(['dashboard']) ?>" href="index.php?page=dashboard">Dashboard</a>
            <?php endif; ?>

            <?php if($menuAllowed(['Super Admin','Admin'])): ?>
                <p class="sidebar-label">Records</p>
                <a class="sidebar-link<?= $activeClass(['programs']) ?>" href="index.php?page=programs">Programs</a>
                <a class="sidebar-link<?= $activeClass(['projects']) ?>" href="index.php?page=projects">Projects</a>
            <?php endif; ?>

            <?php
                $canViewMonitoringRecords = canAccessMonitoringRecords();
                $canViewQuarterlyReports = canAccessQuarterlyReports();
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

            <?php if($menuAllowed(['Super Admin','Admin','Extension Director'])): ?>
                <p class="sidebar-label">Reports</p>
                <a class="sidebar-link<?= $activeClass(['report']) ?>" href="index.php?page=report">Prescriptive Report</a>
            <?php endif; ?>

            <?php if(canAccessGisMap()): ?>
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
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="notification-dropdown-wrap">
                        <button class="header-notification" type="button" data-notification-dropdown-toggle aria-expanded="false" aria-controls="notificationDropdown" aria-label="Notifications" title="Notifications">
                            <span class="header-notification-icon" aria-hidden="true">!</span>
                            <?php if($notificationCount > 0): ?>
                                <span class="header-notification-badge"><?= $notificationCount ?></span>
                            <?php endif; ?>
                        </button>

                        <div class="notification-dropdown-panel" id="notificationDropdown" data-notification-dropdown hidden>
                            <div class="notification-dropdown-header">
                                <div>
                                    <h5>Notifications</h5>
                                    <p><?= $notificationCount ?> unread notification<?= $notificationCount == 1 ? '' : 's' ?></p>
                                </div>
                                <button type="button" class="btn-close" data-notification-dropdown-close aria-label="Close"></button>
                            </div>

                            <div class="notification-dropdown-body">
                                <?php if(!empty($headerNotifications)): ?>
                                    <div class="notification-list">
                                        <?php foreach($headerNotifications as $n): ?>
                                            <?php $isUnread = intval($n['is_read'] ?? 0) === 0; ?>
                                            <div class="notification-item<?= $isUnread ? ' unread' : '' ?>">
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <h6><?= htmlspecialchars($n['title'] ?? '') ?></h6>
                                                    <?php if($isUnread): ?><span class="badge bg-danger">New</span><?php endif; ?>
                                                </div>
                                                <p><?= htmlspecialchars($n['message'] ?? '') ?></p>
                                                <small><?= htmlspecialchars($n['created_at'] ?? '') ?></small>
                                                <div class="notification-actions">
                                                    <?php if(trim((string)($n['link'] ?? '')) !== ''): ?>
                                                        <form method="post" action="index.php?page=notification_action">
                                                            <input type="hidden" name="notification_action" value="open">
                                                            <input type="hidden" name="notification_id" value="<?= intval($n['id'] ?? 0) ?>">
                                                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($currentUrl) ?>">
                                                            <button class="btn btn-sm btn-outline-primary" type="submit">Open</button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <?php if($isUnread): ?>
                                                        <form method="post" action="index.php?page=notification_action">
                                                            <input type="hidden" name="notification_action" value="read">
                                                            <input type="hidden" name="notification_id" value="<?= intval($n['id'] ?? 0) ?>">
                                                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($currentUrl) ?>">
                                                            <button class="btn btn-sm btn-outline-secondary" type="submit">Read</button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form method="post" action="index.php?page=notification_action">
                                                        <input type="hidden" name="notification_action" value="delete">
                                                        <input type="hidden" name="notification_id" value="<?= intval($n['id'] ?? 0) ?>">
                                                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($currentUrl) ?>">
                                                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this notification?')">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="notification-empty">No notifications yet.</p>
                                <?php endif; ?>
                            </div>

                            <div class="notification-dropdown-footer">
                                <form method="post" action="index.php?page=notification_action">
                                    <input type="hidden" name="notification_action" value="mark_all">
                                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($currentUrl) ?>">
                                    <button class="btn btn-outline-secondary btn-sm" type="submit" <?= empty($headerNotifications) ? 'disabled' : '' ?>>Mark All Read</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <a class="header-logout" href="index.php?page=logout">Logout</a>
            </div>
        </header>
