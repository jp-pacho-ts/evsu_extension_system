<?php include 'app/views/layouts/header.php'; ?>

<h2 class="fw-bold">Executive Monitoring Dashboard</h2>
<p class="text-muted">University-wide consolidated analytics, GIS-ready monitoring, and prescriptive analytics based on Extension Office monitoring status.</p>

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card p-3 bg-primary text-white">
            <small>Total Projects</small>
            <h2><?= $total ?></h2>
        </div>
    </div>

    <?php foreach(['On-going','Completed','Terminated','Inactive','Expired'] as $s): ?>
        <div class="col-md-2">
            <div class="card p-3">
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

<div class="card p-3 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Quarterly Report Phases</h5>
        <a href="index.php?page=quarterly_reports" class="btn btn-sm btn-outline-primary">View Reports</a>
    </div>

    <div class="row g-3 mb-3">
        <?php foreach(range(1, 7) as $phase): ?>
            <div class="col-6 col-md-3 col-xl">
                <div class="p-2 h-100">
                    <small class="text-muted">Phase <?= $phase ?></small>
                    <h4 class="mb-0"><?= $quarterlyPhaseCounts[(string)$phase] ?? 0 ?></h4>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Extension Project</th>
                    <th>Period Covered</th>
                    <th>Phase</th>
                    <th>Status</th>
                    <th>Report Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(($quarterlyPhaseItems ?? []) as $item): ?>
                    <?php
                        $phase = trim((string)($item['project_phase'] ?? ''));
                        if($phase !== '' && stripos($phase, 'phase') !== 0) $phase = 'Phase '.$phase;
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['title_of_extension_project'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($item['period_covered'] ?? '') ?></td>
                        <td><?= htmlspecialchars($phase) ?></td>
                        <td><?= htmlspecialchars($item['submission_status'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['report_date'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if(empty($quarterlyPhaseItems ?? [])): ?>
                    <tr><td colspan="5" class="text-muted text-center">No quarterly report phases recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card p-3 mt-4">
    <h5 class="fw-bold">Prescriptive Decision Support</h5>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
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
