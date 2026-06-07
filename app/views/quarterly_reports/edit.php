<?php include "app/views/layouts/header.php"; ?>

<p class="text-muted">Updates the same report. No duplicate submission is created.</p>
<?php if(isset($message)&&$message): ?><div class="alert alert-danger"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<div class="alert alert-info">Current Status: <b><?= htmlspecialchars($report['submission_status']??'Draft') ?></b></div>
<div class="card p-4 mb-4"><form method="POST">
<h5 class="fw-bold">Report Header</h5><div class="row g-3">
<div class="col-md-4"><label>College</label><input name="college" class="form-control" value="<?= htmlspecialchars($report['college']) ?>"></div>
<div class="col-md-4"><label>Campus</label><input name="campus" class="form-control" value="<?= htmlspecialchars($report['campus']) ?>"></div>
<div class="col-md-4"><label>Department</label><input name="department" class="form-control" value="<?= htmlspecialchars($report['department']) ?>"></div>
<div class="col-md-3"><label>Period Covered</label><input name="period_covered" class="form-control" value="<?= htmlspecialchars($report['period_covered']) ?>"></div>
<div class="col-md-3"><label>Control No.</label><input name="control_no" class="form-control" value="<?= htmlspecialchars($report['control_no']) ?>"></div>
<div class="col-md-3"><label>Revision No.</label><input name="revision_no" class="form-control" value="<?= htmlspecialchars($report['revision_no']) ?>"></div>
<div class="col-md-3"><label>Date</label><input type="date" name="report_date" class="form-control" value="<?= htmlspecialchars($report['report_date']) ?>"></div></div><hr>
<h5 class="fw-bold">Project Entries</h5><div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Title</th><th>Proponents</th><th>Date</th><th>Location</th><th>Fund</th><th>Cost</th><th>Phase</th></tr></thead><tbody>
<?php for($i=0;$i<4;$i++): $item=$items[$i]??null; ?><tr>
<td><textarea name="title_of_extension_project[]" class="form-control" rows="3"><?= htmlspecialchars($item['title_of_extension_project']??'') ?></textarea></td>
<td><textarea name="proponents[]" class="form-control" rows="3"><?= htmlspecialchars($item['proponents']??'') ?></textarea></td>
<td><input name="date_conducted[]" class="form-control" value="<?= htmlspecialchars($item['date_conducted']??'') ?>"></td>
<td><textarea name="location[]" class="form-control"><?= htmlspecialchars($item['location']??'') ?></textarea></td>
<td><textarea name="source_of_fund[]" class="form-control"><?= htmlspecialchars($item['source_of_fund']??'') ?></textarea></td>
<td><input type="number" step="0.01" name="total_project_cost[]" class="form-control" value="<?= htmlspecialchars($item['total_project_cost']??'0') ?>"></td>
<td><select name="project_phase[]" class="form-select"><?php for($p=1;$p<=7;$p++): ?><option value="<?= $p ?>" <?= ($item && intval($item['project_phase'])==$p)?'selected':'' ?>>Phase <?= $p ?></option><?php endfor; ?></select></td>
</tr><?php endfor; ?></tbody></table></div><hr>
<h5 class="fw-bold">Signatories</h5><div class="row g-3">
<div class="col-md-4"><label>Prepared by</label><input name="prepared_by" class="form-control" value="<?= htmlspecialchars($report['prepared_by']) ?>"></div>
<div class="col-md-4"><label>Prepared Title</label><input name="prepared_title" class="form-control" value="<?= htmlspecialchars($report['prepared_title']) ?>"></div>
<div class="col-md-4"><label>Noted by: Campus Director/Dean</label><input name="noted_by_dean" class="form-control" value="<?= htmlspecialchars($report['noted_by_dean']) ?>"></div>
<div class="col-md-4"><label>Dean Title</label><input name="noted_by_dean_title" class="form-control" value="<?= htmlspecialchars($report['noted_by_dean_title']) ?>"></div>
<div class="col-md-4"><label>Noted by: Extension Director</label><input name="noted_by_extension_director" class="form-control" value="<?= htmlspecialchars($report['noted_by_extension_director']) ?>"></div>
<div class="col-md-4"><label>Extension Director Title</label><input name="noted_by_extension_director_title" class="form-control" value="<?= htmlspecialchars($report['noted_by_extension_director_title']) ?>"></div>
<div class="col-md-4"><label>Approved by</label><input name="approved_by" class="form-control" value="<?= htmlspecialchars($report['approved_by']) ?>"></div>
<div class="col-md-4"><label>Approved Title</label><input name="approved_title" class="form-control" value="<?= htmlspecialchars($report['approved_title']) ?>"></div></div>
<div class="mt-3"><label>Revision / Update Notes</label><textarea name="revision_notes" class="form-control"><?= htmlspecialchars($report['revision_notes']??'') ?></textarea></div>
<div class="mt-4 d-flex gap-2"><button class="btn btn-secondary" name="save_action" value="draft">Update Only</button><button class="btn btn-primary" name="save_action" value="submit">Update and Resubmit</button><a href="index.php?page=view_quarterly_report&id=<?= $report['id'] ?>" class="btn btn-outline-dark">Cancel</a></div>
</form></div><?php include "app/views/layouts/footer.php"; ?>
