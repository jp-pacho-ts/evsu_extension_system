<?php
$isEdit=!empty($report['id']);
$value=function($key,$default='') use($report,$profile) {
    if(isset($_POST[$key]) && !is_array($_POST[$key])) return $_POST[$key];
    if(array_key_exists($key,$report)) return $report[$key];
    return $profile[$key] ?? $default;
};
$rows=!empty($items)?$items:[[]];
$currentQuarter=intval(ceil(date('n')/3));
?>

<div class="d-flex justify-content-between align-items-start gap-3 mb-3">
    <div><p class="text-muted mb-0">EVSU-ORDE&amp;S-F-027 · Quarterly Accomplishment Report</p></div>
    <a class="btn btn-outline-secondary" href="index.php?page=quarterly_accomplishments">Back to Reports</a>
</div>
<?php if($message): ?><div class="alert alert-danger"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<form method="post" id="accomplishmentForm">
    <div class="card p-4 mb-3">
        <h5 class="fw-bold">Report Header</h5>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">College / School</label><input class="form-control" name="college" required value="<?= htmlspecialchars($value('college','School of Education')) ?>"></div>
            <div class="col-md-4"><label class="form-label">Campus</label><input class="form-control" name="campus" required value="<?= htmlspecialchars($value('campus','EVSU Main Campus')) ?>"></div>
            <div class="col-md-4"><label class="form-label">Department / Unit</label><input class="form-control" name="department" required value="<?= htmlspecialchars($value('department','')) ?>"></div>
            <div class="col-md-2"><label class="form-label">Quarter</label><select class="form-select" name="quarter"><?php for($q=1;$q<=4;$q++): ?><option value="<?= $q ?>" <?= intval($value('quarter',$currentQuarter))===$q?'selected':'' ?>>Quarter <?= $q ?></option><?php endfor; ?></select></div>
            <div class="col-md-2"><label class="form-label">Year</label><input type="number" min="2000" max="2100" class="form-control" name="report_year" required value="<?= intval($value('report_year',date('Y'))) ?>"></div>
            <div class="col-md-3"><label class="form-label">Control No.</label><input class="form-control" name="control_no" value="<?= htmlspecialchars($value('control_no','EVSU-ORDE&S-F-027')) ?>"></div>
            <div class="col-md-2"><label class="form-label">Revision No.</label><input class="form-control" name="revision_no" value="<?= htmlspecialchars($value('revision_no','03')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Effectivity Date</label><input type="date" class="form-control" name="effectivity_date" value="<?= htmlspecialchars($value('effectivity_date','2024-02-20')) ?>"></div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div><h5 class="fw-bold mb-0">Accomplishment Entries</h5><p class="text-muted small mb-0">Add one entry for every extension activity conducted during the quarter.</p></div>
        <button class="btn btn-outline-primary" type="button" id="addAccomplishmentRow"><i class="bi bi-plus-lg"></i> Add Activity</button>
    </div>
    <div id="accomplishmentRows">
        <?php foreach($rows as $index=>$item): ?>
            <div class="card p-3 mb-3 accomplishment-row">
                <div class="d-flex justify-content-between align-items-center mb-3"><strong class="entry-number">Activity <?= $index+1 ?></strong><button type="button" class="btn btn-sm btn-outline-danger remove-entry">Remove</button></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Extension Project</label><select name="project_id[]" class="form-select project-select" required><option value="">Select project</option><?php foreach($projects as $project): ?><option value="<?= intval($project['id']) ?>" <?= intval($item['project_id'] ?? 0)===intval($project['id'])?'selected':'' ?>><?= htmlspecialchars($project['program_title'].' — '.$project['project_title']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-3"><label class="form-label">Inclusive Date From</label><input type="date" name="inclusive_date_start[]" class="form-control" value="<?= htmlspecialchars($item['inclusive_date_start'] ?? '') ?>"></div>
                    <div class="col-md-3"><label class="form-label">Inclusive Date To</label><input type="date" name="inclusive_date_end[]" class="form-control" value="<?= htmlspecialchars($item['inclusive_date_end'] ?? '') ?>"></div>
                    <div class="col-12"><label class="form-label">Title of Activity</label><textarea name="activity_title[]" class="form-control" rows="2" required><?= htmlspecialchars($item['activity_title'] ?? '') ?></textarea></div>
                    <div class="col-md-4"><label class="form-label">Type of Beneficiaries</label><textarea name="beneficiary_type[]" class="form-control" rows="2"><?= htmlspecialchars($item['beneficiary_type'] ?? '') ?></textarea></div>
                    <div class="col-md-2"><label class="form-label">Male</label><input type="number" min="0" name="male_count[]" class="form-control beneficiary-count" value="<?= intval($item['male_count'] ?? 0) ?>"></div>
                    <div class="col-md-2"><label class="form-label">Female</label><input type="number" min="0" name="female_count[]" class="form-control beneficiary-count" value="<?= intval($item['female_count'] ?? 0) ?>"></div>
                    <div class="col-md-2"><label class="form-label">Total</label><input type="number" class="form-control beneficiary-total" readonly value="<?= intval($item['male_count'] ?? 0)+intval($item['female_count'] ?? 0) ?>"></div>
                    <div class="col-md-2"><label class="form-label">Rating (1–5)</label><input type="number" min="1" max="5" step="0.01" name="quality_rating[]" class="form-control" value="<?= htmlspecialchars($item['quality_rating'] ?? '') ?>"></div>
                    <div class="col-md-2"><label class="form-label">Duration (hours)</label><input type="number" min="0" step="0.25" name="duration_hours[]" class="form-control" value="<?= htmlspecialchars($item['duration_hours'] ?? 0) ?>"></div>
                    <div class="col-md-4"><label class="form-label">Type of Extension Service</label><input name="service_type[]" class="form-control" placeholder="Training, outreach, consultancy…" value="<?= htmlspecialchars($item['service_type'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Partner Agency / Community</label><input name="partner_agency[]" class="form-control" value="<?= htmlspecialchars($item['partner_agency'] ?? '') ?>"></div>
                    <div class="col-md-5"><label class="form-label">Faculty / Staff Involved</label><textarea name="faculty_staff_involved[]" class="form-control" rows="3" placeholder="One name per line"><?= htmlspecialchars($item['faculty_staff_involved'] ?? '') ?></textarea></div>
                    <div class="col-md-2"><label class="form-label">Students Involved</label><input type="number" min="0" name="students_involved[]" class="form-control" value="<?= intval($item['students_involved'] ?? 0) ?>"></div>
                    <div class="col-md-5"><label class="form-label">Nature of Participation</label><textarea name="nature_of_participation[]" class="form-control" rows="3"><?= htmlspecialchars($item['nature_of_participation'] ?? '') ?></textarea></div>
                    <div class="col-md-3"><label class="form-label">Project Cost</label><input type="number" min="0" step="0.01" name="project_cost[]" class="form-control" value="<?= htmlspecialchars($item['project_cost'] ?? 0) ?>"></div>
                    <div class="col-md-5"><label class="form-label">Funding Source</label><input name="funding_source[]" class="form-control" value="<?= htmlspecialchars($item['funding_source'] ?? '') ?>"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card p-4 mb-3">
        <h5 class="fw-bold">Signatories</h5>
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">Prepared by</label><input name="prepared_by" class="form-control" value="<?= htmlspecialchars($value('prepared_by',$profile['fullname'] ?? '')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Prepared Title</label><input name="prepared_title" class="form-control" value="<?= htmlspecialchars($value('prepared_title',$profile['signatory_title'] ?? 'Proponent/s')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Checked by</label><input name="checked_by" class="form-control" value="<?= htmlspecialchars($value('checked_by','')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Checked Title</label><input name="checked_title" class="form-control" value="<?= htmlspecialchars($value('checked_title','College Extension Coordinator')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Noted by</label><input name="noted_by" class="form-control" value="<?= htmlspecialchars($value('noted_by','')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Noted Title</label><input name="noted_title" class="form-control" value="<?= htmlspecialchars($value('noted_title','College Dean')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Approved by</label><input name="approved_by" class="form-control" value="<?= htmlspecialchars($value('approved_by','')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Approved Title</label><input name="approved_title" class="form-control" value="<?= htmlspecialchars($value('approved_title','Director, OES')) ?>"></div>
        </div>
    </div>
    <div class="d-flex gap-2 mb-4"><button class="btn btn-secondary" name="save_action" value="draft">Save as Draft</button><button class="btn btn-primary" name="save_action" value="submit">Save and Submit</button></div>
</form>

<template id="accomplishmentRowTemplate">
    <div class="card p-3 mb-3 accomplishment-row">
        <div class="d-flex justify-content-between align-items-center mb-3"><strong class="entry-number">Activity</strong><button type="button" class="btn btn-sm btn-outline-danger remove-entry">Remove</button></div>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Extension Project</label><select name="project_id[]" class="form-select project-select" required><option value="">Select project</option><?php foreach($projects as $project): ?><option value="<?= intval($project['id']) ?>"><?= htmlspecialchars($project['program_title'].' — '.$project['project_title']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label">Inclusive Date From</label><input type="date" name="inclusive_date_start[]" class="form-control"></div><div class="col-md-3"><label class="form-label">Inclusive Date To</label><input type="date" name="inclusive_date_end[]" class="form-control"></div>
            <div class="col-12"><label class="form-label">Title of Activity</label><textarea name="activity_title[]" class="form-control" rows="2" required></textarea></div>
            <div class="col-md-4"><label class="form-label">Type of Beneficiaries</label><textarea name="beneficiary_type[]" class="form-control" rows="2"></textarea></div>
            <div class="col-md-2"><label class="form-label">Male</label><input type="number" min="0" name="male_count[]" class="form-control beneficiary-count" value="0"></div><div class="col-md-2"><label class="form-label">Female</label><input type="number" min="0" name="female_count[]" class="form-control beneficiary-count" value="0"></div><div class="col-md-2"><label class="form-label">Total</label><input type="number" class="form-control beneficiary-total" readonly value="0"></div><div class="col-md-2"><label class="form-label">Rating (1–5)</label><input type="number" min="1" max="5" step="0.01" name="quality_rating[]" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">Duration (hours)</label><input type="number" min="0" step="0.25" name="duration_hours[]" class="form-control" value="0"></div><div class="col-md-4"><label class="form-label">Type of Extension Service</label><input name="service_type[]" class="form-control"></div><div class="col-md-6"><label class="form-label">Partner Agency / Community</label><input name="partner_agency[]" class="form-control"></div>
            <div class="col-md-5"><label class="form-label">Faculty / Staff Involved</label><textarea name="faculty_staff_involved[]" class="form-control" rows="3"></textarea></div><div class="col-md-2"><label class="form-label">Students Involved</label><input type="number" min="0" name="students_involved[]" class="form-control" value="0"></div><div class="col-md-5"><label class="form-label">Nature of Participation</label><textarea name="nature_of_participation[]" class="form-control" rows="3"></textarea></div>
            <div class="col-md-3"><label class="form-label">Project Cost</label><input type="number" min="0" step="0.01" name="project_cost[]" class="form-control" value="0"></div><div class="col-md-5"><label class="form-label">Funding Source</label><input name="funding_source[]" class="form-control"></div>
        </div>
    </div>
</template>

<script>
(() => {
    const rows=document.getElementById('accomplishmentRows');
    const renumber=()=>rows.querySelectorAll('.accomplishment-row').forEach((row,index)=>row.querySelector('.entry-number').textContent=`Activity ${index+1}`);
    document.getElementById('addAccomplishmentRow').addEventListener('click',()=>{ rows.append(document.getElementById('accomplishmentRowTemplate').content.cloneNode(true)); renumber(); });
    rows.addEventListener('click',event=>{ const button=event.target.closest('.remove-entry'); if(!button)return; if(rows.querySelectorAll('.accomplishment-row').length===1)return; button.closest('.accomplishment-row').remove(); renumber(); });
    rows.addEventListener('input',event=>{ if(!event.target.classList.contains('beneficiary-count'))return; const row=event.target.closest('.accomplishment-row'); const counts=[...row.querySelectorAll('.beneficiary-count')]; row.querySelector('.beneficiary-total').value=counts.reduce((sum,input)=>sum+(Number(input.value)||0),0); });
})();
</script>
