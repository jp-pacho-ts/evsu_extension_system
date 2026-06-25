<?php include "app/views/layouts/header.php"; ?>
<?php
$status=$report['submission_status'] ?? 'Draft';
$editable=in_array($status,['Draft','Recalled','For Revision','Not Approved'],true);
$recallable=in_array($status,['Submitted','Under Review'],true);
$ordinal=[1=>'1st',2=>'2nd',3=>'3rd',4=>'4th'];
$formatDate=function($date){ return $date?date('F j, Y',strtotime($date)):''; };
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 no-print">
    <div><span class="badge bg-<?= $status==='Approved'?'success':($editable?'secondary':'primary') ?>"><?= htmlspecialchars($status) ?></span><?php if(!empty($report['revision_notes'])): ?><span class="text-muted ms-2"><?= htmlspecialchars($report['revision_notes']) ?></span><?php endif; ?></div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-secondary" href="index.php?page=quarterly_accomplishments">Back</a>
        <?php if($canManage && $editable): ?><a class="btn btn-outline-success" href="index.php?page=edit_quarterly_accomplishment&id=<?= intval($report['id']) ?>">Edit</a><form method="post" action="index.php?page=submit_quarterly_accomplishment"><input type="hidden" name="id" value="<?= intval($report['id']) ?>"><button class="btn btn-primary">Submit</button></form><?php endif; ?>
        <?php if($canManage && $recallable): ?><form method="post" action="index.php?page=recall_quarterly_accomplishment" onsubmit="return confirm('Recall this submission for editing?')"><input type="hidden" name="id" value="<?= intval($report['id']) ?>"><button class="btn btn-warning">Recall</button></form><?php endif; ?>
        <button class="btn btn-dark" onclick="window.print()"><i class="bi bi-printer"></i> Print / Save PDF</button>
    </div>
</div>

<div class="document-sheet accomplishment-print">
    <table class="form-heading"><tr><td class="logo-cell"><img src="public/assets/images/evsu-official-logo.png" alt="EVSU"></td><td class="university"><strong>EASTERN VISAYAS STATE UNIVERSITY</strong><br>Tacloban City<br><b>Title of Form: QUARTERLY ACCOMPLISHMENT REPORT</b></td><td class="meta"><b>Control No.</b> <?= htmlspecialchars($report['control_no'] ?? '') ?><br><b>Revision No.</b> <?= htmlspecialchars($report['revision_no'] ?? '') ?><br><b>Effectivity Date</b> <?= htmlspecialchars($formatDate($report['effectivity_date'] ?? '')) ?></td></tr></table>
    <h2>QUARTERLY ACCOMPLISHMENT REPORT</h2>
    <h3><?= htmlspecialchars(($ordinal[intval($report['quarter'])] ?? '').' Quarter, '.intval($report['report_year'])) ?></h3>
    <p><b>College/School:</b> <?= htmlspecialchars($report['college'] ?? '') ?> &nbsp; <b>Department/Unit:</b> <?= htmlspecialchars($report['department'] ?? '') ?> &nbsp; <b>Campus:</b> <?= htmlspecialchars($report['campus'] ?? '') ?></p>

    <table class="data-table">
        <thead><tr><th>Inclusive Dates</th><th>Extension Program</th><th>Extension Project</th><th>Title of Activity</th><th>Beneficiaries</th><th>M</th><th>F</th><th>Total</th><th>Rating</th><th>Duration</th><th>Service Rendered</th><th>Partner Agency / Community</th><th>Faculty / Staff Involved</th><th>Students</th><th>Nature of Participation</th><th>Cost / Funding Source</th></tr></thead>
        <tbody>
        <?php foreach($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($formatDate($item['inclusive_date_start'] ?? '')) ?><?= !empty($item['inclusive_date_end']) && $item['inclusive_date_end']!==$item['inclusive_date_start']?'<br>to<br>'.htmlspecialchars($formatDate($item['inclusive_date_end'])):'' ?></td>
                <td><?= nl2br(htmlspecialchars($item['program_title'] ?? '')) ?></td><td><?= nl2br(htmlspecialchars($item['project_title'] ?? '')) ?></td><td><?= nl2br(htmlspecialchars($item['activity_title'] ?? '')) ?></td><td><?= nl2br(htmlspecialchars($item['beneficiary_type'] ?? '')) ?></td>
                <td><?= intval($item['male_count'] ?? 0) ?></td><td><?= intval($item['female_count'] ?? 0) ?></td><td><?= intval($item['male_count'] ?? 0)+intval($item['female_count'] ?? 0) ?></td><td><?= htmlspecialchars($item['quality_rating'] ?? '') ?></td><td><?= htmlspecialchars($item['duration_hours'] ?? 0) ?> hrs</td>
                <td><?= nl2br(htmlspecialchars($item['service_type'] ?? '')) ?></td><td><?= nl2br(htmlspecialchars($item['partner_agency'] ?? '')) ?></td><td><?= nl2br(htmlspecialchars($item['faculty_staff_involved'] ?? '')) ?></td><td><?= intval($item['students_involved'] ?? 0) ?></td><td><?= nl2br(htmlspecialchars($item['nature_of_participation'] ?? '')) ?></td><td>₱<?= number_format(floatval($item['project_cost'] ?? 0),2) ?><br><?= htmlspecialchars($item['funding_source'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if(empty($items)): ?><tr><td colspan="16">No activity entries.</td></tr><?php endif; ?>
        </tbody>
    </table>

    <div class="signature-grid">
        <div><span>Prepared by:</span><div class="signature-space"></div><b><?= htmlspecialchars($report['prepared_by'] ?? '') ?></b><br><?= htmlspecialchars($report['prepared_title'] ?? '') ?></div>
        <div><span>Checked and Verified:</span><div class="signature-space"></div><b><?= htmlspecialchars($report['checked_by'] ?? '') ?></b><br><?= htmlspecialchars($report['checked_title'] ?? '') ?></div>
        <div><span>Noted:</span><div class="signature-space"></div><b><?= htmlspecialchars($report['noted_by'] ?? '') ?></b><br><?= htmlspecialchars($report['noted_title'] ?? '') ?></div>
        <div><span>Approved:</span><div class="signature-space"></div><b><?= htmlspecialchars($report['approved_by'] ?? '') ?></b><br><?= htmlspecialchars($report['approved_title'] ?? '') ?></div>
    </div>
</div>

<style>
.document-sheet{background:#fff;color:#111;padding:24px;border:1px solid #d8dee8;overflow-x:auto}.document-sheet table{width:100%;border-collapse:collapse}.form-heading td,.data-table th,.data-table td{border:1px solid #111;padding:5px;vertical-align:top}.form-heading .logo-cell{width:80px;text-align:center}.form-heading img{width:62px}.form-heading .university{text-align:center;font-size:14px}.form-heading .university strong{font-size:18px}.form-heading .meta{width:250px;font-size:11px}.document-sheet h2,.document-sheet h3{text-align:center;margin:14px 0 4px}.document-sheet h3{font-size:16px;margin-top:0}.data-table{font-size:9px;min-width:1800px}.data-table th{text-align:center;vertical-align:middle}.signature-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;text-align:center;margin-top:34px;font-size:11px}.signature-grid span{display:block;text-align:left}.signature-space{height:45px;border-bottom:1px solid #111;margin-bottom:5px}
@media print{ @page{size:A4 landscape;margin:7mm} .content{padding:0!important}.page-header,.sidebar,.no-print{display:none!important}.app-shell{display:block}.document-sheet{border:0;padding:0;overflow:visible}.data-table{min-width:0;font-size:6.5px}.form-heading .university strong{font-size:14px}.signature-grid{break-inside:avoid;margin-top:20px}.signature-space{height:28px} }
</style>

<?php include "app/views/layouts/footer.php"; ?>
