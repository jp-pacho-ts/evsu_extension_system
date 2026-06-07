<?php include "app/views/layouts/header.php"; ?>

<p class="text-muted">Filter and export system activity logs.</p>

<?php
$searchLog = strtolower($_GET['search_log'] ?? '');
$logType = $_GET['log_type'] ?? '';
$filteredLogs = $logs ?? [];

if($searchLog !== '') {
    $filteredLogs = array_filter($filteredLogs, function($l) use ($searchLog) {
        return str_contains(strtolower(($l['user_name'] ?? '').' '.($l['username'] ?? '').' '.($l['action'] ?? '').' '.($l['module'] ?? '').' '.($l['description'] ?? '').' '.($l['details'] ?? '')), $searchLog);
    });
}
if($logType !== '') {
    $filteredLogs = array_filter($filteredLogs, function($l) use ($logType) {
        return stripos(($l['action'] ?? '').' '.($l['module'] ?? '').' '.($l['log_category'] ?? ''), $logType) !== false;
    });
}
?>

<div class="card p-3 mb-4">
    <form method="GET" class="row g-2">
        <input type="hidden" name="page" value="logs">
        <div class="col-md-4">
            <input type="text" name="search_log" value="<?= htmlspecialchars($_GET['search_log'] ?? '') ?>" class="form-control" placeholder="Search logs...">
        </div>
        <div class="col-md-3">
            <select name="log_type" class="form-select">
                <option value="">All Logs</option>
                <?php foreach(['Login','Approval','Monitoring','User','Delete','Update'] as $type): ?>
                    <option value="<?= $type ?>" <?= $logType==$type?'selected':'' ?>><?= $type ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <button class="btn btn-primary">Filter Logs</button>
            <a href="index.php?page=logs" class="btn btn-outline-secondary">Reset</a>
            <a href="export_logs.php" class="btn btn-success">Export CSV</a>
        </div>
    </form>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date/Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($filteredLogs as $l): ?>
                <tr>
                    <td><?= htmlspecialchars($l['created_at'] ?? '') ?></td>
                    <td><?= htmlspecialchars($l['user_name'] ?? $l['username'] ?? '') ?></td>
                    <td><?= htmlspecialchars($l['action'] ?? '') ?></td>
                    <td><?= htmlspecialchars($l['module'] ?? '') ?></td>
                    <td><?= htmlspecialchars($l['description'] ?? $l['details'] ?? '') ?></td>
                    <td><?= htmlspecialchars($l['ip_address'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($filteredLogs)): ?>
                <tr><td colspan="6" class="text-muted text-center">No logs found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "app/views/layouts/footer.php"; ?>
