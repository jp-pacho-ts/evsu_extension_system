<?php include "app/views/layouts/header.php"; ?>

<h2 class="fw-bold">📑 Quarterly Monitoring Report</h2>
<p class="text-muted"><?= ($canManageQuarterlyReports ?? false) ? 'Create quarterly monitoring report and submit it for review/approval.' : 'View saved quarterly monitoring reports.' ?></p>

<?php if(isset($message) && $message): ?>
    <div class="alert alert-danger"><?= $message ?></div>
<?php endif; ?>

<?php if($canManageQuarterlyReports ?? false): ?>
<div class="card p-4 mb-4">
<form method="POST">
    <h5 class="fw-bold">Report Header</h5>
    <div class="row g-3">
        <div class="col-md-4"><label>College</label><input name="college" class="form-control" value="School of Engineering"></div>
        <div class="col-md-4"><label>Campus</label><input name="campus" class="form-control" value="Main"></div>
        <div class="col-md-4"><label>Department</label><input name="department" class="form-control" value="Information Technology"></div>
        <div class="col-md-3"><label>Period Covered</label><input name="period_covered" class="form-control" value="1st Quarter 2026"></div>
        <div class="col-md-3"><label>Control No.</label><input name="control_no" class="form-control" value="EVSU-OPRDEX-F-028"></div>
        <div class="col-md-3"><label>Revision No.</label><input name="revision_no" class="form-control" value="01"></div>
        <div class="col-md-3"><label>Date</label><input type="date" name="report_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
    </div>

    <hr>

    <h5 class="fw-bold">Project Entries</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Title of Extension Project</th>
                    <th>Proponent/s</th>
                    <th>Date Conducted</th>
                    <th>Location</th>
                    <th>Source of Fund</th>
                    <th>Total Cost</th>
                    <th>Phase</th>
                </tr>
            </thead>
            <tbody>
            <?php for($i=0; $i<4; $i++): ?>
                <tr>
                    <td><textarea name="title_of_extension_project[]" class="form-control" rows="3"><?= $i==0 ? 'SMART DEWS: A Mobile-Based Disaster Early Warning System for Storm Surge' : '' ?></textarea></td>
                    <td><textarea name="proponents[]" class="form-control" rows="3"><?= $i==0 ? 'Selected Faculty, Information Technology Department, EVSU\nProject Leader: Dr. Lyra K. Nuevas' : '' ?></textarea></td>
                    <td><input name="date_conducted[]" class="form-control" value="<?= $i==0 ? 'January 28, 2026' : '' ?>"></td>
                    <td><textarea name="location[]" class="form-control" rows="2"><?= $i==0 ? 'IGF Extension Fund and LGU-Abuyog' : '' ?></textarea></td>
                    <td><textarea name="source_of_fund[]" class="form-control" rows="2"><?= $i==0 ? 'IGF Extension Fund and LGU-Abuyog' : '' ?></textarea></td>
                    <td><input type="number" step="0.01" name="total_project_cost[]" class="form-control" value="<?= $i==0 ? '25000' : '0' ?>"></td>
                    <td>
                        <select name="project_phase[]" class="form-select">
                            <?php for($p=1; $p<=7; $p++): ?>
                                <option value="<?= $p ?>" <?= ($i==0 && $p==3) ? 'selected' : '' ?>>Phase <?= $p ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
    </div>

    <hr>

    <h5 class="fw-bold">Signatories</h5>
    <div class="row g-3">
        <div class="col-md-4"><label>Prepared by</label><input name="prepared_by" class="form-control" value="LYRA K. NUEVAS, PhD"></div>
        <div class="col-md-4"><label>Prepared Title</label><input name="prepared_title" class="form-control" value="Extension Coordinator"></div>
        <div class="col-md-4"><label>Noted by: Campus Director/Dean</label><input name="noted_by_dean" class="form-control" value="VINYL H. OGOLINO, PhD"></div>
        <div class="col-md-4"><label>Dean Title</label><input name="noted_by_dean_title" class="form-control" value="Campus Director/Dean"></div>
        <div class="col-md-4"><label>Noted by: Extension Director</label><input name="noted_by_extension_director" class="form-control" value="RUSTOM D. CLEMENTE, MSIT"></div>
        <div class="col-md-4"><label>Extension Director Title</label><input name="noted_by_extension_director_title" class="form-control" value="Extension Director"></div>
        <div class="col-md-4"><label>Approved by</label><input name="approved_by" class="form-control" value="DANTEB P. PULMA, DM"></div>
        <div class="col-md-4"><label>Approved Title</label><input name="approved_title" class="form-control" value="VP for ORIES"></div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-secondary" name="save_action" value="draft">Save as Draft</button>
        <button class="btn btn-primary" name="save_action" value="submit">Save and Submit for Review</button>
    </div>
</form>
</div>
<?php endif; ?>

<div class="card p-3">
    <h5 class="fw-bold">Saved Quarterly Reports</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>College</th><th>Department</th><th>Period</th><th>Status</th><th>Submitted By</th><th>Date</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach($reports as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['college']) ?></td>
                    <td><?= htmlspecialchars($r['department']) ?></td>
                    <td><?= htmlspecialchars($r['period_covered']) ?></td>
                    <td>
                        <?php
                            $status = $r['submission_status'] ?? 'Draft';
                            $badge = $status == 'Approved' ? 'success' : ($status == 'For Revision' ? 'danger' : ($status == 'Submitted' ? 'primary' : ($status == 'Under Review' ? 'warning' : 'secondary')));
                        ?>
                        <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($status) ?></span>
                    </td>
                    <td><?= htmlspecialchars($r['submitted_by_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['report_date']) ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary" href="index.php?page=view_quarterly_report&id=<?= $r['id'] ?>">View / Print</a>
                        <?php if(($canManageQuarterlyReports ?? false) && in_array(($r['submission_status'] ?? 'Draft'), ['Draft','Recalled','For Revision'])): ?><a class="btn btn-sm btn-outline-success" href="index.php?page=edit_quarterly_report&id=<?= $r['id'] ?>">Edit</a><?php endif; ?>
                        <?php if(($canManageQuarterlyReports ?? false) && in_array(($r['submission_status'] ?? 'Draft'), ['Submitted','Under Review'])): ?><a class="btn btn-sm btn-warning" onclick="return confirm('Recall this submission for correction?')" href="index.php?page=recall_quarterly_report&id=<?= $r['id'] ?>">Recall</a><?php endif; ?>
                        <?php if(($canManageQuarterlyReports ?? false) && (in_array(($r['submission_status'] ?? 'Draft'), ['Draft','Recalled','For Revision']) || hasRole(['Super Admin']))): ?><a class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this report?')" href="index.php?page=delete_quarterly_report&id=<?= $r['id'] ?>">Delete</a><?php endif; ?>
                        <?php if(($canManageQuarterlyReports ?? false) && (($r['submission_status'] ?? 'Draft') == 'Draft' || ($r['submission_status'] ?? '') == 'For Revision')): ?>
                            <a class="btn btn-sm btn-success" href="index.php?page=submit_quarterly_report&id=<?= $r['id'] ?>">Submit</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($reports)): ?>
                <tr><td colspan="7" class="text-muted">No quarterly reports yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "app/views/layouts/footer.php"; ?>
