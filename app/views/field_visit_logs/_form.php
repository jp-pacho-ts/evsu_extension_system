<?php
$isEdit=!empty($report['id']);
$value=function($key,$default='') use($report,$profile) {
    if(isset($_POST[$key]) && !is_array($_POST[$key])) return $_POST[$key];
    if(array_key_exists($key,$report)) return $report[$key];
    return $profile[$key] ?? $default;
};
$rows=!empty($items)?$items:[[]]; $currentQuarter=intval(ceil(date('n')/3));
?>

<div class="d-flex justify-content-between align-items-start gap-3 mb-3"><p class="text-muted mb-0">EVSU-ORDE&amp;S-F-076 · Work Plan and Monitoring Log for Field Visits</p><a class="btn btn-outline-secondary" href="index.php?page=field_visit_logs">Back to Logs</a></div>
<?php if($message): ?><div class="alert alert-danger"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<form method="post" id="fieldVisitForm">
    <div class="card p-4 mb-3">
        <h5 class="fw-bold">Project and Report Header</h5>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Program / Project</label><select name="project_id" id="fieldVisitProject" class="form-select" required><option value="">Select project</option><?php foreach($projects as $project): ?><option value="<?= intval($project['id']) ?>" data-start="<?= htmlspecialchars($project['start_date'] ?? '') ?>" data-end="<?= htmlspecialchars($project['end_date'] ?? '') ?>" <?= intval($value('project_id',0))===intval($project['id'])?'selected':'' ?>><?= htmlspecialchars($project['program_title'].' — '.$project['project_title']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label">Implementing College / Unit</label><input name="implementing_unit" class="form-control" required value="<?= htmlspecialchars($value('implementing_unit',$profile['department'] ?? '')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Campus</label><input name="campus" class="form-control" required value="<?= htmlspecialchars($value('campus','EVSU Main Campus')) ?>"></div>
            <div class="col-md-2"><label class="form-label">Quarter</label><select name="quarter" class="form-select"><?php for($q=1;$q<=4;$q++): ?><option value="<?= $q ?>" <?= intval($value('quarter',$currentQuarter))===$q?'selected':'' ?>>Quarter <?= $q ?></option><?php endfor; ?></select></div>
            <div class="col-md-2"><label class="form-label">Year</label><input type="number" min="2000" max="2100" name="report_year" class="form-control" value="<?= intval($value('report_year',date('Y'))) ?>"></div>
            <div class="col-md-2"><label class="form-label">Project Duration From</label><input type="date" id="durationStart" name="duration_start" class="form-control" value="<?= htmlspecialchars($value('duration_start','')) ?>"></div>
            <div class="col-md-2"><label class="form-label">Project Duration To</label><input type="date" id="durationEnd" name="duration_end" class="form-control" value="<?= htmlspecialchars($value('duration_end','')) ?>"></div>
            <div class="col-md-2"><label class="form-label">Control No.</label><input name="control_no" class="form-control" value="<?= htmlspecialchars($value('control_no','EVSU-ORDE&S-F-076')) ?>"></div>
            <div class="col-md-1"><label class="form-label">Revision</label><input name="revision_no" class="form-control" value="<?= htmlspecialchars($value('revision_no','00')) ?>"></div>
            <div class="col-md-1"><label class="form-label">Form Date</label><input type="date" name="form_date" class="form-control" value="<?= htmlspecialchars($value('form_date','2022-03-14')) ?>"></div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2"><div><h5 class="fw-bold mb-0">Field Visit Entries</h5><p class="text-muted small mb-0">Use a separate entry for each visit or monitoring activity.</p></div><button class="btn btn-outline-primary" type="button" id="addFieldVisitRow"><i class="bi bi-plus-lg"></i> Add Visit</button></div>
    <div id="fieldVisitRows">
        <?php foreach($rows as $index=>$item): ?>
        <div class="card p-3 mb-3 field-visit-row">
            <div class="d-flex justify-content-between align-items-center mb-3"><strong class="entry-number">Visit <?= $index+1 ?></strong><button type="button" class="btn btn-sm btn-outline-danger remove-entry">Remove</button></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Objectives</label><textarea name="objectives[]" rows="4" class="form-control" required><?= htmlspecialchars($item['objectives'] ?? '') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Activities</label><textarea name="activities[]" rows="4" class="form-control" required><?= htmlspecialchars($item['activities'] ?? '') ?></textarea></div>
                <div class="col-md-3"><label class="form-label">Visit Date</label><input type="date" name="visit_date[]" class="form-control" required value="<?= htmlspecialchars($item['visit_date'] ?? '') ?>"></div>
                <div class="col-md-5"><label class="form-label">Place</label><input name="place[]" class="form-control" value="<?= htmlspecialchars($item['place'] ?? '') ?>"></div>
                <div class="col-md-2"><label class="form-label">Start Time</label><input type="time" name="time_start[]" class="form-control" value="<?= htmlspecialchars(substr((string)($item['time_start'] ?? ''),0,5)) ?>"></div>
                <div class="col-md-2"><label class="form-label">End Time</label><input type="time" name="time_end[]" class="form-control" value="<?= htmlspecialchars(substr((string)($item['time_end'] ?? ''),0,5)) ?>"></div>
                <div class="col-md-6"><label class="form-label">Expected Output — Parameter</label><textarea name="expected_parameter[]" rows="3" class="form-control"><?= htmlspecialchars($item['expected_parameter'] ?? '') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Expected Output — Target</label><textarea name="expected_target[]" rows="3" class="form-control"><?= htmlspecialchars($item['expected_target'] ?? '') ?></textarea></div>
                <div class="col-md-4"><label class="form-label">Person Contacted</label><input name="person_contacted[]" class="form-control" value="<?= htmlspecialchars($item['person_contacted'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Position</label><input name="contact_position[]" class="form-control" value="<?= htmlspecialchars($item['contact_position'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Results</label><textarea name="results[]" rows="3" class="form-control"><?= htmlspecialchars($item['results'] ?? '') ?></textarea></div>
                <div class="col-md-3"><label class="form-label">Observations</label><textarea name="observations[]" rows="3" class="form-control"><?= htmlspecialchars($item['observations'] ?? '') ?></textarea></div>
                <div class="col-md-3"><label class="form-label">Issues / Problems</label><textarea name="issues[]" rows="3" class="form-control"><?= htmlspecialchars($item['issues'] ?? '') ?></textarea></div>
                <div class="col-md-3"><label class="form-label">Action Points</label><textarea name="action_points[]" rows="3" class="form-control"><?= htmlspecialchars($item['action_points'] ?? '') ?></textarea></div>
                <div class="col-md-3"><label class="form-label">Comments</label><textarea name="comments[]" rows="3" class="form-control"><?= htmlspecialchars($item['comments'] ?? '') ?></textarea></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card p-4 mb-3">
        <h5 class="fw-bold">Signatories</h5>
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">Prepared by</label><input name="prepared_by" class="form-control" value="<?= htmlspecialchars($value('prepared_by',$profile['fullname'] ?? '')) ?>"></div><div class="col-md-3"><label class="form-label">Prepared Title</label><input name="prepared_title" class="form-control" value="<?= htmlspecialchars($value('prepared_title','Program/Project Leader')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Noted by</label><input name="noted_by" class="form-control" value="<?= htmlspecialchars($value('noted_by','')) ?>"></div><div class="col-md-3"><label class="form-label">Noted Title</label><input name="noted_title" class="form-control" value="<?= htmlspecialchars($value('noted_title','College Extension Coordinator')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Attested by</label><input name="attested_by" class="form-control" value="<?= htmlspecialchars($value('attested_by','')) ?>"></div><div class="col-md-3"><label class="form-label">Attested Title</label><input name="attested_title" class="form-control" value="<?= htmlspecialchars($value('attested_title','Head/Authorized Representative of Partner Agency')) ?>"></div>
            <div class="col-md-3"><label class="form-label">Approved by</label><input name="approved_by" class="form-control" value="<?= htmlspecialchars($value('approved_by','')) ?>"></div><div class="col-md-3"><label class="form-label">Approved Title</label><input name="approved_title" class="form-control" value="<?= htmlspecialchars($value('approved_title','Director, OES')) ?>"></div>
        </div>
    </div>
    <div class="d-flex gap-2 mb-4"><button class="btn btn-secondary" name="save_action" value="draft">Save as Draft</button><button class="btn btn-primary" name="save_action" value="submit">Save and Submit</button></div>
</form>

<template id="fieldVisitRowTemplate"><div class="card p-3 mb-3 field-visit-row"><div class="d-flex justify-content-between align-items-center mb-3"><strong class="entry-number">Visit</strong><button type="button" class="btn btn-sm btn-outline-danger remove-entry">Remove</button></div><div class="row g-3">
    <div class="col-md-6"><label class="form-label">Objectives</label><textarea name="objectives[]" rows="4" class="form-control" required></textarea></div><div class="col-md-6"><label class="form-label">Activities</label><textarea name="activities[]" rows="4" class="form-control" required></textarea></div>
    <div class="col-md-3"><label class="form-label">Visit Date</label><input type="date" name="visit_date[]" class="form-control" required></div><div class="col-md-5"><label class="form-label">Place</label><input name="place[]" class="form-control"></div><div class="col-md-2"><label class="form-label">Start Time</label><input type="time" name="time_start[]" class="form-control"></div><div class="col-md-2"><label class="form-label">End Time</label><input type="time" name="time_end[]" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Expected Output — Parameter</label><textarea name="expected_parameter[]" rows="3" class="form-control"></textarea></div><div class="col-md-6"><label class="form-label">Expected Output — Target</label><textarea name="expected_target[]" rows="3" class="form-control"></textarea></div>
    <div class="col-md-4"><label class="form-label">Person Contacted</label><input name="person_contacted[]" class="form-control"></div><div class="col-md-4"><label class="form-label">Position</label><input name="contact_position[]" class="form-control"></div><div class="col-md-4"><label class="form-label">Results</label><textarea name="results[]" rows="3" class="form-control"></textarea></div>
    <div class="col-md-3"><label class="form-label">Observations</label><textarea name="observations[]" rows="3" class="form-control"></textarea></div><div class="col-md-3"><label class="form-label">Issues / Problems</label><textarea name="issues[]" rows="3" class="form-control"></textarea></div><div class="col-md-3"><label class="form-label">Action Points</label><textarea name="action_points[]" rows="3" class="form-control"></textarea></div><div class="col-md-3"><label class="form-label">Comments</label><textarea name="comments[]" rows="3" class="form-control"></textarea></div>
</div></div></template>

<script>
(() => {
    const rows=document.getElementById('fieldVisitRows');
    const renumber=()=>rows.querySelectorAll('.field-visit-row').forEach((row,index)=>row.querySelector('.entry-number').textContent=`Visit ${index+1}`);
    document.getElementById('addFieldVisitRow').addEventListener('click',()=>{ rows.append(document.getElementById('fieldVisitRowTemplate').content.cloneNode(true)); renumber(); });
    rows.addEventListener('click',event=>{ const button=event.target.closest('.remove-entry'); if(!button)return; if(rows.querySelectorAll('.field-visit-row').length===1)return; button.closest('.field-visit-row').remove(); renumber(); });
    document.getElementById('fieldVisitProject').addEventListener('change',event=>{ const option=event.target.selectedOptions[0]; if(!document.getElementById('durationStart').value) document.getElementById('durationStart').value=option.dataset.start||''; if(!document.getElementById('durationEnd').value) document.getElementById('durationEnd').value=option.dataset.end||''; });
})();
</script>
