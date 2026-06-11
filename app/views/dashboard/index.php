<?php include 'app/views/layouts/header.php'; ?>

<p class="text-muted">University-wide consolidated analytics, GIS-ready monitoring, and prescriptive analytics based on Extension Office monitoring status.</p>

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card p-3 bg-primary text-white dashboard-summary-card">
            <small>Total Projects</small>
            <h2><?= $total ?></h2>
        </div>
    </div>

    <?php foreach(['On-going','Completed','Terminated','Inactive','Expired'] as $s): ?>
        <div class="col-md-2">
            <div class="card p-3 dashboard-summary-card">
                <small><?= $s ?></small>
                <h2><?= $statusCounts[$s] ?? 0 ?></h2>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-3 chart-card">
            <h6>Projects by Program</h6>
            <canvas id="programChart"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 chart-card">
            <h6>Projects by Municipality</h6>
            <canvas id="municipalityChart"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 chart-card">
            <h6>Status Distribution</h6>
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-xxl-8">
        <div class="card p-3 h-100 dashboard-panel">
            <h5 class="fw-bold">Prescriptive Decision Support</h5>
            <div class="table-responsive dashboard-table-scroll">
                <table class="table table-sm table-hover align-middle dashboard-compact-table dashboard-prescriptive-table">
                    <thead class="table-light">
                        <tr>
                            <th>Project Title</th>
                            <th>Status</th>
                            <th>Latest Monitoring</th>
                            <th>Recent Update</th>
                            <th>Updated</th>
                            <th>Prescriptive Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($projects as $p): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['project_title'] ?? '') ?></strong></td>
                                <td><span class="badge bg-<?= statusBadge($p['status'] ?? '') ?>"><?= htmlspecialchars($p['status'] ?? '') ?></span></td>
                                <td><?= htmlspecialchars($p['latest_monitoring_title'] ?? 'No monitoring entry yet') ?></td>
                                <td><?= htmlspecialchars($p['latest_update'] ?? '') ?></td>
                                <td><?= htmlspecialchars($p['latest_monitoring_date'] ?? '') ?></td>
                                <td><?= htmlspecialchars(decisionSupport($p['status'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xxl-4">
        <div class="card p-3 h-100 dashboard-panel">
            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                <h5 class="fw-bold mb-0">Quarterly Reports</h5>
                <a class="btn btn-sm btn-outline-primary" href="index.php?page=quarterly_reports">View All</a>
            </div>
            <div class="table-responsive dashboard-table-scroll">
                <table class="table table-sm table-hover align-middle dashboard-compact-table dashboard-quarterly-table">
                    <thead class="table-light">
                        <tr>
                            <th>Project Title</th>
                            <th>Phase</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(($quarterlyReportItems ?? []) as $item): ?>
                            <?php
                                $phase = trim((string)($item['project_phase'] ?? ''));
                                if($phase === '') $phase = 'No phase';
                                elseif(stripos($phase, 'phase') !== 0) $phase = 'Phase '.$phase;
                                $status = $item['submission_status'] ?? 'Draft';
                                $badge = ($status == 'Approved' || strpos($status, 'Approved') !== false) ? 'success' : (($status == 'For Revision' || $status == 'Not Approved') ? 'danger' : ($status == 'Submitted' ? 'primary' : ($status == 'Under Review' ? 'warning' : 'secondary')));
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($item['title_of_extension_project'] ?? '') ?></strong></td>
                                <td><?= htmlspecialchars($phase) ?></td>
                                <td><span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($status) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($quarterlyReportItems ?? [])): ?>
                            <tr><td colspan="3" class="text-muted">No quarterly report entries yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const analyticsData = {
    programLabels: <?= json_encode(array_keys($programCounts)) ?>,
    programValues: <?= json_encode(array_values($programCounts)) ?>,
    municipalityLabels: <?= json_encode(array_keys($municipalityCounts)) ?>,
    municipalityValues: <?= json_encode(array_values($municipalityCounts)) ?>,
    statusLabels: <?= json_encode(array_keys($statusCounts)) ?>,
    statusValues: <?= json_encode(array_values($statusCounts)) ?>
};
</script>
<script src="public/assets/js/dashboard.js"></script>

<?php include 'app/views/layouts/footer.php'; ?>
