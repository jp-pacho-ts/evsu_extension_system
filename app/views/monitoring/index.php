<?php include "app/views/layouts/header.php"; ?>
<?php $monitoringRedirect = 'index.php?' . http_build_query($_GET); ?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <p class="text-muted mb-0">Add monitoring entries and update project status.</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMonitoringModal">Add Monitoring Entry</button>
</div>

<?php if(isset($_GET['saved'])): ?>
    <div class="alert alert-success">Monitoring entry saved successfully.</div>
<?php endif; ?>
<?php if(isset($_GET['updated'])): ?>
    <div class="alert alert-success">Monitoring entry updated successfully.</div>
<?php endif; ?>
<?php if(isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Monitoring entry deleted successfully.</div>
<?php endif; ?>
<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger">Unable to complete the monitoring action.</div>
<?php endif; ?>

<div class="card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Monitoring Records</h5>
        <button onclick="window.print()" class="btn btn-outline-primary no-print">Print / Save as PDF</button>
    </div>

    <div class="table-responsive monitoring-table-wrap">
        <table class="table table-bordered table-hover align-middle monitoring-records-table">
            <thead class="table-light">
                <tr>
                    <th>Program</th>
                    <th>Project & Location</th>
                    <th>Classification</th>
                    <th>Team</th>
                    <th>Monitoring</th>
                    <th>Status</th>
                    <th>Update & Remarks</th>
                    <th class="no-print">Actions</th>
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
                        <td>
                            <strong class="monitoring-cell-title"><?= htmlspecialchars($m['program_title'] ?? '') ?></strong>
                            <span class="monitoring-meta">S.O. <?= htmlspecialchars($m['special_order_no'] ?? '') ?></span>
                        </td>
                        <td>
                            <strong class="monitoring-cell-title"><?= htmlspecialchars($m['project_title'] ?? '') ?></strong>
                            <span class="monitoring-meta"><?= htmlspecialchars($m['barangay'] ?? '') ?></span>
                            <span class="monitoring-meta"><?= htmlspecialchars(trim(($m['municipality'] ?? '').', '.($m['province'] ?? ''), ', ')) ?></span>
                        </td>
                        <td>
                            <strong class="monitoring-cell-title"><?= htmlspecialchars($m['sdg'] ?? '') ?></strong>
                            <span class="monitoring-meta">Partner: <?= htmlspecialchars($m['partner'] ?? $m['partners'] ?? '') ?></span>
                            <span class="monitoring-meta">Clientele: <?= htmlspecialchars($m['type_of_clientele'] ?? $m['clientele_type'] ?? '') ?></span>
                        </td>
                        <td>
                            <strong class="monitoring-cell-title"><?= htmlspecialchars($m['leader'] ?? '') ?></strong>
                            <span class="monitoring-meta">Assistant: <?= htmlspecialchars($m['assistant'] ?? $m['assistant_leader'] ?? '') ?></span>
                            <span class="monitoring-meta">Members: <?= htmlspecialchars($m['members'] ?? '') ?></span>
                        </td>
                        <td>
                            <strong class="monitoring-cell-title"><?= htmlspecialchars($m['activity_title'] ?? '') ?></strong>
                            <span class="monitoring-meta"><?= htmlspecialchars($m['monitoring_date'] ?? '') ?></span>
                        </td>
                        <td>
                            <form method="POST" action="index.php?page=update_monitoring_status" style="margin:0;">
                                <input type="hidden" name="monitoring_id" value="<?= htmlspecialchars($m['id']) ?>">
                                <input type="hidden" name="redirect" value="<?= htmlspecialchars($monitoringRedirect) ?>">
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm status-dropdown-inline">
                                    <?php foreach(['On-going','Completed','Inactive','Expired','Terminated'] as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $currentStatus == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td>
                            <strong class="monitoring-cell-title"><?= htmlspecialchars($m['activity_description'] ?? '') ?></strong>
                            <span class="monitoring-meta">Remarks: <?= htmlspecialchars($m['remarks'] ?? '') ?></span>
                        </td>
                        <td class="monitoring-actions no-print">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editMonitoringModal<?= intval($m['id']) ?>">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteMonitoringModal<?= intval($m['id']) ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if(empty($monitoring ?? $records ?? [])): ?>
                    <tr><td colspan="8" class="text-muted text-center">No monitoring records yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= renderPagination($pagination ?? [], 'monitoring records') ?>
</div>

<div class="modal fade" id="addMonitoringModal" tabindex="-1" aria-labelledby="addMonitoringModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="form_action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMonitoringModalLabel">Add Monitoring Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php $monitoringForm = []; include "app/views/monitoring/_form.php"; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save Monitoring Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach(($monitoring ?? $records ?? []) as $m): ?>
    <?php $monitoringId = intval($m['id']); ?>
    <div class="modal fade" id="editMonitoringModal<?= $monitoringId ?>" tabindex="-1" aria-labelledby="editMonitoringModalLabel<?= $monitoringId ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="form_action" value="update">
                    <input type="hidden" name="monitoring_id" value="<?= $monitoringId ?>">
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($monitoringRedirect) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMonitoringModalLabel<?= $monitoringId ?>">Edit Monitoring Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php $monitoringForm = $m; include "app/views/monitoring/_form.php"; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Update Monitoring Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteMonitoringModal<?= $monitoringId ?>" tabindex="-1" aria-labelledby="deleteMonitoringModalLabel<?= $monitoringId ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="form_action" value="delete">
                    <input type="hidden" name="monitoring_id" value="<?= $monitoringId ?>">
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($monitoringRedirect) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMonitoringModalLabel<?= $monitoringId ?>">Delete Monitoring Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">Delete this monitoring entry?</p>
                        <p class="fw-bold mb-1"><?= htmlspecialchars($m['activity_title'] ?? '') ?></p>
                        <p class="text-muted small mb-0"><?= htmlspecialchars($m['project_title'] ?? '') ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger">Delete Monitoring Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<style>
.monitoring-table-wrap {
    overflow-x: visible;
}

.monitoring-records-table {
    width: 100%;
    table-layout: fixed;
    font-size: 12px;
    line-height: 1.35;
}

.monitoring-records-table th,
.monitoring-records-table td {
    padding: 8px 7px !important;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
    vertical-align: top !important;
}

.monitoring-records-table th:nth-child(1) { width: 12%; }
.monitoring-records-table th:nth-child(2) { width: 17%; }
.monitoring-records-table th:nth-child(3) { width: 14%; }
.monitoring-records-table th:nth-child(4) { width: 15%; }
.monitoring-records-table th:nth-child(5) { width: 13%; }
.monitoring-records-table th:nth-child(6) { width: 9%; }
.monitoring-records-table th:nth-child(7) { width: 12%; }
.monitoring-records-table th:nth-child(8) { width: 8%; }

.monitoring-cell-title {
    display: block;
    line-height: 1.25;
}

.monitoring-meta {
    display: block;
    margin-top: 3px;
    color: #6b7280;
    font-size: 11px;
    line-height: 1.3;
}

.status-dropdown-inline {
    width: 100%;
    min-width: 0;
    font-size: 11px;
    font-weight: 700;
    border-radius: 6px;
    padding: 3px 4px;
}

.monitoring-actions {
    display: grid;
    gap: 5px;
}

.monitoring-actions .btn {
    width: 100%;
    padding: 3px 4px;
    font-size: 11px;
    line-height: 1.2;
}

@media (max-width: 900px) {
    .monitoring-records-table {
        font-size: 10px;
    }

    .monitoring-records-table th,
    .monitoring-records-table td {
        padding: 6px 5px !important;
    }

    .monitoring-meta {
        font-size: 10px;
    }

    .monitoring-actions .btn,
    .status-dropdown-inline {
        font-size: 10px;
    }
}
@media print {
    .sidebar, .no-print, form button { display:none !important; }
    .content { margin:0 !important; padding:10px !important; }
    body { background:white !important; }
    .monitoring-records-table { font-size:9px; }
}
</style>

<?php include "app/views/layouts/footer.php"; ?>
