<?php include "app/views/layouts/header.php"; ?>

<style>
.report-form{background:white;padding:10px;border:1px solid #111;font-size:12px;color:#111}
.report-form table{width:100%;border-collapse:collapse}
.report-form th,.report-form td{border:1px solid #111;padding:6px;vertical-align:middle}
.center{text-align:center}.bold{font-weight:bold}.report-title{font-size:22px;font-weight:bold}
.report-logo{display:block;width:92px;height:92px;object-fit:contain;margin:0 auto}
.signature-line{border-top:1px solid #111;width:220px;margin:55px auto 5px}
.phase-box{text-align:center;font-size:20px;font-weight:bold}
@media print{.sidebar,.no-print{display:none!important}.content{margin-left:0!important;padding:0!important}body{background:white}.report-form{border:none;font-size:11px}}
</style>

<div class="card p-3 mb-3 no-print">
    <h5 class="fw-bold">Submission Status</h5>
    <?php
        $status = $report['submission_status'] ?? 'Draft';
        $badge = ($status == 'Approved' || strpos($status, 'Approved') !== false) ? 'success' : (($status == 'For Revision' || $status == 'Not Approved') ? 'danger' : ($status == 'Submitted' ? 'primary' : ($status == 'Under Review' ? 'warning' : 'secondary')));
        $approvedLevels = [];
        foreach(($approvals ?? []) as $approval) {
            $level = intval($approval['approval_level'] ?? 0);
            if($level >= 1 && $level <= 4 && ($approval['status'] ?? '') === 'Approved') {
                $approvedLevels[$level] = true;
            }
        }
        $approvalMonitoringContribution = count($approvedLevels);
    ?>
    <p class="mb-1"><b>Status:</b> <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($status) ?></span></p>
    <p class="mb-1"><b>Monitoring Count Contribution:</b> +<?= $approvalMonitoringContribution ?> of 4 approved stages</p>
    <p class="text-muted small mb-2">Submitting the report does not increase the count. Each completed approval stage adds 1 to every project selected in this report.</p>
    <p class="mb-1"><b>Submitted By:</b> <?= htmlspecialchars($report['submitted_by_name'] ?? '') ?></p>
    <p class="mb-1"><b>Submitted At:</b> <?= htmlspecialchars($report['submitted_at'] ?? '') ?></p>
    <p class="mb-1"><b>Reviewed By:</b> <?= htmlspecialchars($report['reviewed_by_name'] ?? '') ?></p>
    <p class="mb-1"><b>Remarks:</b> <?= htmlspecialchars($report['approval_remarks'] ?? '') ?></p>
    <?php if(($canManageQuarterlyReports ?? false) && in_array(($report['submission_status'] ?? 'Draft'), ['Draft','For Revision','Not Approved'])): ?>
        <a href="index.php?page=submit_quarterly_report&id=<?= $report['id'] ?>" class="btn btn-success btn-sm mt-2">Submit for Review</a>
    <?php endif; ?>
</div>


<div class="card p-3 mb-3 no-print">
    <h5 class="fw-bold">Approval Timeline / History</h5>
    <div class="table-responsive mb-3">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Level</th>
                    <th>Required Role</th>
                    <th>Status</th>
                    <th>Approver</th>
                    <th>Signed At</th><th>Signature</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach(($approvals ?? []) as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['approval_level']) ?></td>
                    <td><?= htmlspecialchars($a['approval_role']) ?></td>
                    <td><span class="badge bg-<?= $a['status'] == 'Approved' ? 'success' : (($a['status'] == 'For Revision' || $a['status'] == 'Not Approved') ? 'danger' : 'secondary') ?>"><?= htmlspecialchars($a['status']) ?></span></td>
                    <td><?= htmlspecialchars($a['approver_name'] ?? $a['fullname'] ?? '') ?></td>
                    <td><?= htmlspecialchars($a['signed_at'] ?? '') ?></td><td><?php if(!empty($a['approver_signature_image'])): ?><span class="badge bg-success">Image</span><?php else: ?><span class="badge bg-secondary">SGD</span><?php endif; ?></td>
                    <td><?= htmlspecialchars($a['remarks'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($approvals)): ?>
                <tr><td colspan="6" class="text-muted">No approval route recorded yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h6 class="fw-bold">Action History</h6>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date/Time</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach(($history ?? []) as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($h['created_at']) ?></td>
                    <td><?= htmlspecialchars($h['fullname'] ?? $h['username'] ?? '') ?></td>
                    <td><?= htmlspecialchars($h['action_role'] ?? '') ?></td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($h['action_taken']) ?></span></td>
                    <td><?= htmlspecialchars($h['previous_status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($h['new_status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($h['remarks'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($history)): ?>
                <tr><td colspan="7" class="text-muted">No action history yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="no-print mb-3">
    <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
    <?php if(($canManageQuarterlyReports ?? false) && in_array(($report['submission_status'] ?? 'Draft'), ['Draft','Recalled','For Revision','Not Approved'])): ?><a href="index.php?page=edit_quarterly_report&id=<?= $report['id'] ?>" class="btn btn-success">Edit Report</a><?php endif; ?>
    <?php if(($canManageQuarterlyReports ?? false) && in_array(($report['submission_status'] ?? 'Draft'), ['Submitted','Under Review'])): ?><a href="index.php?page=recall_quarterly_report&id=<?= $report['id'] ?>" onclick="return confirm('Recall this submission for correction?')" class="btn btn-warning">Recall Submission</a><?php endif; ?>
    <?php if(($canManageQuarterlyReports ?? false) && (in_array(($report['submission_status'] ?? 'Draft'), ['Draft','Recalled','For Revision','Not Approved']) || hasRole(['Super Admin']))): ?><a href="index.php?page=delete_quarterly_report&id=<?= $report['id'] ?>" onclick="return confirm('Delete this report?')" class="btn btn-danger">Delete</a><?php endif; ?>
    <a href="index.php?page=quarterly_reports" class="btn btn-secondary">Back to Quarterly Report</a>
    <?php if(hasRole(['Super Admin','Admin','Extension Director','Reviewer'])): ?><a href="index.php?page=dashboard" class="btn btn-outline-primary">Back to Dashboard</a><?php endif; ?>
</div>

<?php
function approvalByRole($approvals,$role){ foreach(($approvals??[]) as $a){ if($a['approval_role']==$role && $a['status']=='Approved') return $a; } return null; }
function sgdBlock($approvals,$role){ $a=approvalByRole($approvals,$role); if($a) return '<div class="bold">SGD.</div><div class="small">Signed: '.htmlspecialchars($a['signed_at']).'</div>'; return ''; }
?>
<?php
function statusReached($currentStatus, $requiredStatus) {
    $order = [
        'Draft' => 0,
        'Recalled' => 0,
        'For Revision' => 0,
        'Not Approved' => 0,
        'Submitted' => 1,
        'Under Review' => 1,
        'Department Coordinator Approved' => 2,
        'School Coordinator Approved' => 3,
        'Campus Director Approved' => 4,
        'Extension Office Approved' => 4,
        'VP ORIES Approved' => 5,
        'Approved' => 5,
        'Archived' => 6
    ];
    return ($order[$currentStatus] ?? 0) >= ($order[$requiredStatus] ?? 99);
}

function sgdByStatus($report, $requiredStatus, $label = 'Approved') {
    $status = $report['submission_status'] ?? 'Draft';
    if(statusReached($status, $requiredStatus)) {
        $date = htmlspecialchars($report['updated_at'] ?? $report['submitted_at'] ?? '');
        return '<div class="bold">SGD.</div><div class="small">'.$label.($date ? ': '.$date : '').'</div>';
    }
    return '';
}

function preparedSgd($report) {
    if(!empty($report['submitted_at'])) {
        return '<div class="bold">SGD.</div><div class="small">Submitted: '.htmlspecialchars($report['submitted_at']).'</div>';
    }
    return '';
}
?>

<?php
function approvalRecordByRole2($approvals, $role) {
    foreach(($approvals ?? []) as $a) {
        if(($a['approval_role'] ?? '') == $role && ($a['status'] ?? '') == 'Approved') return $a;
    }
    return null;
}
function signatureDisplayByRole2($approvals, $role) {
    $a = approvalRecordByRole2($approvals, $role);
    if(!$a) return '';
    $html = '';
    if(!empty($a['approver_signature_image'])) {
        $html .= '<img src="'.htmlspecialchars($a['approver_signature_image']).'" style="max-height:45px;max-width:130px;display:block;margin:0 auto 2px auto;">';
    } else {
        $html .= '<div class="bold">SGD.</div>';
    }
    $html .= '<div class="small">Signed by: '.htmlspecialchars($a['approver_name'] ?? $a['fullname'] ?? '').'</div>';
    $html .= '<div class="small">Signed at: '.htmlspecialchars($a['signed_at'] ?? '').'</div>';
    return $html;
}
?>
<div class="report-form">
<table>
<tr>
<td rowspan="3" style="width:110px;" class="center bold"><img src="public/assets/images/evsu-official-logo.png" alt="EVSU Official Logo" class="report-logo"></td>
<td rowspan="3" class="center">
<div class="report-title">EASTERN VISAYAS STATE UNIVERSITY</div>
<div class="bold">Tacloban City</div>
<div style="font-size:18px;margin-top:8px;">Title of Form: Quarterly Monitoring Report</div>
<div class="bold">EXTENSION PROJECT</div>
</td>
<td class="bold">Control No.</td><td><?= htmlspecialchars($report['control_no']) ?></td>
</tr>
<tr><td class="bold">Revision No.</td><td><?= htmlspecialchars($report['revision_no']) ?></td></tr>
<tr><td class="bold">Date</td><td><?= date('F d, Y', strtotime($report['report_date'])) ?></td></tr>
</table>

<table>
<tr>
<td><b>College:</b> <?= htmlspecialchars($report['college']) ?></td>
<td><b>Campus:</b> <?= htmlspecialchars($report['campus']) ?></td>
<td><b>Department:</b> <?= htmlspecialchars($report['department']) ?></td>
<td><b>Period Covered:</b> <?= htmlspecialchars($report['period_covered']) ?></td>
</tr>
</table>

<table>
<thead>
<tr class="center bold">
<th rowspan="2">NO.</th><th rowspan="2">TITLE OF<br>EXTENSION PROJECT</th><th rowspan="2">PROPONENT/S</th><th rowspan="2">DATE<br>CONDUCTED</th><th rowspan="2">LOCATION</th><th colspan="2">FUNDING</th><th colspan="7">STATUS OF THE PROJECT</th>
</tr>
<tr class="center bold">
<th>SOURCE OF<br>FUND</th><th>TOTAL<br>PROJECT COST</th>
<?php for($p=1;$p<=7;$p++): ?><th>Phase <?= $p ?></th><?php endfor; ?>
</tr>
</thead>
<tbody>
<?php for($i=0;$i<4;$i++): $item=$items[$i] ?? null; ?>
<tr>
<td class="center"><?= $i+1 ?></td>
<td><?= nl2br(htmlspecialchars($item['title_of_extension_project'] ?? '')) ?></td>
<td><?= nl2br(htmlspecialchars($item['proponents'] ?? '')) ?></td>
<td class="center"><?= htmlspecialchars($item['date_conducted'] ?? '') ?></td>
<td><?= nl2br(htmlspecialchars($item['location'] ?? '')) ?></td>
<td><?= nl2br(htmlspecialchars($item['source_of_fund'] ?? '')) ?></td>
<td class="center"><?= $item ? '₱ '.number_format($item['total_project_cost'],2) : '' ?></td>
<?php for($p=1;$p<=7;$p++): ?><td class="phase-box"><?= ($item && intval($item['project_phase'])==$p) ? '✓' : '' ?></td><?php endfor; ?>
</tr>
<?php endfor; ?>
</tbody>
</table>

<table>
<tr>
<td class="center" style="height:130px;">
<div style="text-align:left;">Prepared by:</div>
<div class="signature-line"></div>
<?= preparedSgd($report) ?>
<b><?= htmlspecialchars($report['prepared_by']) ?></b><br><?= htmlspecialchars($report['prepared_title']) ?>
</td>
<td class="center">
<div style="text-align:left;">Noted by:</div>
<div class="signature-line"></div>
<?= signatureDisplayByRole2($approvals ?? [], 'Campus Director / Dean') ?>
<b><?= htmlspecialchars($report['noted_by_dean']) ?></b><br><?= htmlspecialchars($report['noted_by_dean_title']) ?>
<div class="signature-line"></div>
<?= signatureDisplayByRole2($approvals ?? [], 'Extension Office') ?>
<b><?= htmlspecialchars($report['noted_by_extension_director']) ?></b><br><?= htmlspecialchars($report['noted_by_extension_director_title']) ?>
</td>
<td class="center">
<div style="text-align:left;">Approved:</div>
<div class="signature-line"></div>
<?= signatureDisplayByRole2($approvals ?? [], 'VP ORIES') ?>
<b><?= htmlspecialchars($report['approved_by']) ?></b><br><?= htmlspecialchars($report['approved_title']) ?>
</td>
</tr>
</table>

<table>
<tr>
<td style="width:45%;">
<b>PHASES:</b><br>
Phase 1 – Needs assessment<br>
Phase 2 – Early project implementation<br>
Phase 3 – Mid project implementation<br>
Phase 4 – Late project implementation<br>
Phase 5 – Completion of project<br>
Phase 6 – Evaluation of input and outcome<br>
Phase 7 – Impact evaluation
</td>
<td>
<b>INSTRUCTIONS:</b><br>
1. Accomplish all necessary information.<br>
2. Use check (✓) mark to indicate the current status/phase of the project.<br>
3. Submit this form quarterly to the Office of Extension Services.
</td>
</tr>
</table>
</div>

<?php include "app/views/layouts/footer.php"; ?>
