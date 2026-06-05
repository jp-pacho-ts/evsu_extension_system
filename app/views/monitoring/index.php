<?php include "app/views/layouts/header.php"; ?>

<h2 class="fw-bold">📝 Monitoring</h2>
<p class="text-muted">Add monitoring entries and update project status.</p>
<?php if(isset($_GET['saved'])): ?>
    <div class="alert alert-success">Monitoring entry saved successfully.</div>
<?php endif; ?>

<div class="card p-4 mb-4">
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-4">
                <label>Project</label>
                <select name="project_id" class="form-select" required>
                    <?php foreach(($projects ?? []) as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['project_title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label>Additional Monitoring Title</label>
                <input name="activity_title" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label>Monitoring Date</label>
                <input type="date" name="monitoring_date" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Source of Fund</label>
                <input name="source_of_fund" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Project Status</label>
                <select name="status" class="form-select">
                    <option>On-going</option>
                    <option>Completed</option>
                    <option>Inactive</option>
                    <option>Expired</option>
                    <option>Terminated</option>
                </select>
            </div>

            <div class="col-md-4">
                <label>Terminal Report Date</label>
                <input type="date" name="terminal_report_date" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Recent Update</label>
                <textarea name="activity_description" class="form-control"></textarea>
            </div>

            <div class="col-md-6">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control"></textarea>
            </div>

            <div class="col-12">
                <button class="btn btn-primary">Save Monitoring Entry</button>
            </div>
        </div>
    </form>
</div>

<div class="card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Monitoring Records</h5>
        <button onclick="window.print()" class="btn btn-outline-primary no-print">Print / Save as PDF</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Program</th>
                    <th>Project</th>
                    <th>Barangay</th>
                    <th>Municipality</th>
                    <th>Province</th>
                    <th>SDG</th>
                    <th>Partner</th>
                    <th>Clientele</th>
                    <th>Leader</th>
                    <th>Assistant</th>
                    <th>Members</th>
                    <th>Additional Monitoring</th>
                    <th>Monitoring Date</th>
                    <th>Project Status</th>
                    <th>Recent Update</th>
                    <th>S.O.</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(($monitoring ?? $records ?? []) as $m): ?>
                    <?php
                        $currentStatus = $m['status'] ?? 'On-going';
                        $badge = [
                            'On-going' => 'primary',
                            'Completed' => 'success',
                            'Inactive' => 'warning',
                            'Expired' => 'danger',
                            'Terminated' => 'dark'
                        ][$currentStatus] ?? 'secondary';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($m['program_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['project_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['barangay'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['municipality'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['province'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['sdg'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['partner'] ?? $m['partners'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['type_of_clientele'] ?? $m['clientele_type'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['leader'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['assistant'] ?? $m['assistant_leader'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['members'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['activity_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['monitoring_date'] ?? '') ?></td>
                        <td>
                            <form method="POST" action="index.php?page=update_monitoring_status" style="margin:0;">
                                <input type="hidden" name="monitoring_id" value="<?= htmlspecialchars($m['id']) ?>">
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm status-dropdown-inline">
                                    <?php foreach(['On-going','Completed','Inactive','Expired','Terminated'] as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $currentStatus == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($m['activity_description'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['special_order_no'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['remarks'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if(empty($monitoring ?? $records ?? [])): ?>
                    <tr><td colspan="17" class="text-muted text-center">No monitoring records yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.status-dropdown-inline {
    min-width: 115px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 8px;
    padding: 3px 6px;
}
@media print {
    .sidebar, .no-print, form button { display:none !important; }
    .content { margin:0 !important; padding:10px !important; }
    body { background:white !important; }
    table { font-size:10px; }
}
</style>

<?php include "app/views/layouts/footer.php"; ?>
