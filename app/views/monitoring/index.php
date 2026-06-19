<?php include "app/views/layouts/header.php"; ?>
<?php
$monitoringRedirectParams = $_GET;
foreach(['saved', 'updated', 'deleted', 'error'] as $flashParam) unset($monitoringRedirectParams[$flashParam]);
if(empty($monitoringRedirectParams['page'])) $monitoringRedirectParams['page'] = 'monitoring';
$monitoringRedirect = 'index.php?' . http_build_query($monitoringRedirectParams);
$monitoringFilters = $monitoringFilters ?? ['q' => '', 'status' => '', 'campus' => '', 'school' => '', 'date_from' => '', 'date_to' => ''];
$filterOptions = $filterOptions ?? ['statuses' => ['On-going','Completed','Inactive','Expired','Terminated'], 'campuses' => [], 'schools' => []];
$statusOptions = $filterOptions['statuses'] ?? ['On-going','Completed','Inactive','Expired','Terminated'];
$hasMonitoringFilters = count(array_filter($monitoringFilters, function($value) {
    return trim((string)$value) !== '';
})) > 0;
?>

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

    <div class="monitoring-filter-bar no-print mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="monitoring">

            <div class="col-12 col-lg-4">
                <label class="form-label" for="monitoringSearch">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" class="form-control" id="monitoringSearch" name="q" value="<?= htmlspecialchars($monitoringFilters['q'] ?? '') ?>" placeholder="Project, program, location, update">
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="monitoringStatusFilter">Status</label>
                <select class="form-select form-select-sm" id="monitoringStatusFilter" name="status">
                    <option value="">All Statuses</option>
                    <?php foreach($statusOptions as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= ($monitoringFilters['status'] ?? '') === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="monitoringCampusFilter">Campus</label>
                <select class="form-select form-select-sm" id="monitoringCampusFilter" name="campus">
                    <option value="">All Campuses</option>
                    <?php foreach(($filterOptions['campuses'] ?? []) as $campus): ?>
                        <option value="<?= htmlspecialchars($campus) ?>" <?= ($monitoringFilters['campus'] ?? '') === $campus ? 'selected' : '' ?>><?= htmlspecialchars($campus) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="monitoringSchoolFilter">School</label>
                <select class="form-select form-select-sm" id="monitoringSchoolFilter" name="school">
                    <option value="">All Schools</option>
                    <?php foreach(($filterOptions['schools'] ?? []) as $school): ?>
                        <option value="<?= htmlspecialchars($school) ?>" <?= ($monitoringFilters['school'] ?? '') === $school ? 'selected' : '' ?>><?= htmlspecialchars($school) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="monitoringDateFrom">From</label>
                <input type="date" class="form-control form-control-sm" id="monitoringDateFrom" name="date_from" value="<?= htmlspecialchars($monitoringFilters['date_from'] ?? '') ?>">
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label" for="monitoringDateTo">To</label>
                <input type="date" class="form-control form-control-sm" id="monitoringDateTo" name="date_to" value="<?= htmlspecialchars($monitoringFilters['date_to'] ?? '') ?>">
            </div>

            <div class="col-12 col-lg-auto">
                <div class="monitoring-filter-actions">
                    <button class="btn btn-sm btn-primary" type="submit">
                        <i class="bi bi-funnel" aria-hidden="true"></i>
                        Apply
                    </button>
                    <a class="btn btn-sm btn-outline-secondary" href="index.php?page=monitoring">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive monitoring-table-wrap">
        <table class="table table-bordered table-hover align-middle monitoring-records-table">
            <colgroup>
                <col class="col-entry-id">
                <col class="col-project-id">
                <col class="col-program">
                <col class="col-project-title">
                <col class="col-special-order">
                <col class="col-location">
                <col class="col-location">
                <col class="col-location">
                <col class="col-sdg">
                <col class="col-partner">
                <col class="col-clientele">
                <col class="col-campus">
                <col class="col-school">
                <col class="col-person">
                <col class="col-person">
                <col class="col-members">
                <col class="col-monitoring-title">
                <col class="col-date">
                <col class="col-fund">
                <col class="col-date">
                <col class="col-status">
                <col class="col-description">
                <col class="col-remarks">
                <col class="col-created">
                <col class="col-actions no-print">
            </colgroup>
            <thead class="table-light">
                <tr>
                    <th>Entry ID</th>
                    <th>Project ID</th>
                    <th>Program</th>
                    <th>Project Title</th>
                    <th>Special Order No.</th>
                    <th>Barangay</th>
                    <th>Municipality</th>
                    <th>Province</th>
                    <th>SDG</th>
                    <th>Partner</th>
                    <th>Clientele</th>
                    <th>EVSU Campus</th>
                    <th>School</th>
                    <th>Leader</th>
                    <th>Assistant Leader</th>
                    <th>Members</th>
                    <th>Additional Monitoring</th>
                    <th>Monitoring Date</th>
                    <th>Source of Fund</th>
                    <th>Terminal Report Date</th>
                    <th>Status</th>
                    <th>Recent Update</th>
                    <th>Remarks</th>
                    <th>Created At</th>
                    <th class="no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(($monitoring ?? $records ?? []) as $m): ?>
                    <?php
                        $currentStatus = $m['status'] ?? 'On-going';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($m['id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['project_id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['program_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['project_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['special_order_no'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['barangay'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['municipality'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['province'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['sdg'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['partner'] ?? $m['partners'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['type_of_clientele'] ?? $m['clientele_type'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['evsu_campus'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['campus_school'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['leader'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['assistant'] ?? $m['assistant_leader'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['members'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['activity_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['monitoring_date'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['source_of_fund'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['terminal_report_date'] ?? '') ?></td>
                        <td>
                            <form method="POST" action="index.php?page=update_monitoring_status" style="margin:0;">
                                <input type="hidden" name="monitoring_id" value="<?= htmlspecialchars($m['id']) ?>">
                                <input type="hidden" name="redirect" value="<?= htmlspecialchars($monitoringRedirect) ?>">
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm status-dropdown-inline">
                                    <?php foreach($statusOptions as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $currentStatus == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($m['activity_description'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['remarks'] ?? '') ?></td>
                        <td><?= htmlspecialchars($m['created_at'] ?? '') ?></td>
                        <td class="monitoring-actions no-print">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editMonitoringModal<?= intval($m['id']) ?>">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteMonitoringModal<?= intval($m['id']) ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if(empty($monitoring ?? $records ?? [])): ?>
                    <tr><td colspan="25" class="text-muted text-center"><?= $hasMonitoringFilters ? 'No monitoring records match the current search/filter.' : 'No monitoring records yet.' ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= renderPagination($pagination ?? [], $hasMonitoringFilters ? 'matching monitoring records' : 'monitoring records') ?>
</div>

<div class="modal fade" id="addMonitoringModal" tabindex="-1" aria-labelledby="addMonitoringModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="form_action" value="create">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($monitoringRedirect) ?>">
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
.monitoring-filter-bar {
    padding: 12px;
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 8px;
}

.monitoring-filter-bar .form-label {
    margin-bottom: 4px;
    color: var(--text);
    font-size: 11px;
    font-weight: 700;
}

.monitoring-filter-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.monitoring-filter-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    min-height: 31px;
}

.monitoring-table-wrap {
    overflow-x: auto;
}

.monitoring-records-table {
    width: 100%;
    min-width: 3300px;
    table-layout: fixed;
    font-size: 11px;
    line-height: 1.35;
}

.monitoring-records-table col.col-entry-id { width: 70px; }
.monitoring-records-table col.col-project-id { width: 78px; }
.monitoring-records-table col.col-program { width: 160px; }
.monitoring-records-table col.col-project-title { width: 220px; }
.monitoring-records-table col.col-special-order { width: 135px; }
.monitoring-records-table col.col-location { width: 135px; }
.monitoring-records-table col.col-sdg { width: 115px; }
.monitoring-records-table col.col-partner { width: 160px; }
.monitoring-records-table col.col-clientele { width: 145px; }
.monitoring-records-table col.col-campus { width: 145px; }
.monitoring-records-table col.col-school { width: 115px; }
.monitoring-records-table col.col-person { width: 155px; }
.monitoring-records-table col.col-members { width: 170px; }
.monitoring-records-table col.col-monitoring-title { width: 200px; }
.monitoring-records-table col.col-date { width: 120px; }
.monitoring-records-table col.col-fund { width: 130px; }
.monitoring-records-table col.col-status { width: 125px; }
.monitoring-records-table col.col-description { width: 240px; }
.monitoring-records-table col.col-remarks { width: 210px; }
.monitoring-records-table col.col-created { width: 150px; }
.monitoring-records-table col.col-actions { width: 90px; }

.monitoring-records-table th,
.monitoring-records-table td {
    padding: 8px 7px !important;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
    vertical-align: top !important;
}

.monitoring-records-table th {
    vertical-align: middle !important;
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

    .monitoring-actions .btn,
    .status-dropdown-inline {
        font-size: 10px;
    }
}
@media print {
    .sidebar, .no-print, form button { display:none !important; }
    .content { margin:0 !important; padding:10px !important; }
    body { background:white !important; }
    .monitoring-records-table {
        min-width: 0;
        width: 100%;
        font-size: 5.5px;
        line-height: 1.1;
    }
    .monitoring-records-table col {
        width: auto !important;
    }
    .monitoring-records-table th,
    .monitoring-records-table td {
        padding: 1px 2px !important;
    }
}
</style>

<?php include "app/views/layouts/footer.php"; ?>
