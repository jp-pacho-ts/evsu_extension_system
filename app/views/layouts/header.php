<?php
if(session_status()===PHP_SESSION_NONE) session_start();
$displayRole=$_SESSION['role']??'Guest';
$approvalCount = isset($db) ? pendingApprovalCount($db) : 0;
$totalNoticeCount = $approvalCount;
function menuAllowed($r){ return hasRole($r); }
?>
<!DOCTYPE html><html><head><title>EVSU Extension Services Platform</title><meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="public/assets/css/style.css"></head><body>
<div class="sidebar no-print">
<h4 class="fw-bold">EVSU Extension</h4><p class="small text-secondary">Monitoring & Analytics</p><hr>
<a href="index.php?page=approval_center">🔔 Pending Approvals <?= $totalNoticeCount > 0 ? '<span class="badge bg-danger">'.$totalNoticeCount.'</span>' : '' ?></a>
<?php if(menuAllowed(['Super Admin','Admin','School Coordinator','Campus Director','VP ORIES','Reviewer'])): ?><a href="index.php?page=dashboard">📊 Dashboard</a><?php endif; ?>
<?php if(menuAllowed(['Department Coordinator','Extension Staff','Super Admin','Admin'])): ?><a href="index.php?page=programs">📌 Programs</a><a href="index.php?page=projects">📁 Projects</a><?php endif; ?>
<?php if(menuAllowed(['Department Coordinator','Extension Staff','School Coordinator','Campus Director','VP ORIES','Super Admin','Admin'])): ?><a href="index.php?page=monitoring">📝 Monitoring</a><a href="index.php?page=quarterly_reports">📑 Quarterly Report</a><?php endif; ?>
<?php if(menuAllowed(['Super Admin','Admin','School Coordinator','Campus Director','Extension Director','VP ORIES'])): ?><a href="index.php?page=report">📄 Prescriptive Report</a><?php endif; ?>
<?php if(menuAllowed(['Super Admin','Admin','Campus Director','VP ORIES'])): ?><a href="index.php?page=map">🗺️ GIS Map</a><?php endif; ?>
<?php if(menuAllowed(['Super Admin','Admin'])): ?><a href="index.php?page=users">👥 User Management</a><a href="index.php?page=logs">📜 Activity Logs</a><?php endif; ?>
<hr><p class="small mb-1">Logged in as:</p><p class="fw-bold mb-1"><?= htmlspecialchars($_SESSION['fullname']??'') ?></p><p class="badge bg-light text-dark"><?= htmlspecialchars($displayRole) ?></p><a href="index.php?page=logout" class="mt-3">🚪 Logout</a>
</div><div class="content">
