<?php $programForm = $programForm ?? []; ?>
<div class="row g-3">
    <div class="col-md-6">
        <label>Program Title</label>
        <input name="program_title" class="form-control" value="<?= htmlspecialchars($programForm['program_title'] ?? '') ?>" required>
    </div>
    <div class="col-md-3">
        <label>Program Cost</label>
        <input type="number" step="0.01" name="project_cost" class="form-control" value="<?= htmlspecialchars($programForm['project_cost'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label>Special Order No.</label>
        <input name="special_order_no" class="form-control" value="<?= htmlspecialchars($programForm['special_order_no'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label>Leader</label>
        <input name="leader" class="form-control" value="<?= htmlspecialchars($programForm['leader'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label>Assistant Leader</label>
        <input name="assistant_leader" class="form-control" value="<?= htmlspecialchars($programForm['assistant_leader'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label>Members</label>
        <textarea name="members" class="form-control"><?= htmlspecialchars($programForm['members'] ?? '') ?></textarea>
    </div>
    <div class="col-md-3">
        <label>Start Date</label>
        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($programForm['start_date'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <label>End Date</label>
        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($programForm['end_date'] ?? '') ?>">
    </div>
</div>
