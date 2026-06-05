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
