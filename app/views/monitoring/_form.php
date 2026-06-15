<?php
$monitoringForm = $monitoringForm ?? [];
$statusOptions = ['On-going','Completed','Inactive','Expired','Terminated'];
$campusOptions = ['External','EVSU Main Campus','EVSU Burauen Campus','EVSU Carigara Campus','EVSU Dulag Campus','EVSU Ormoc Campus','EVSU Tanauan Campus'];
$schoolOptions = ['SAS','SED','SAME','SOT','SAAD','SOE'];
$selectedProjectId = intval($monitoringForm['project_id'] ?? 0);
$selectedStatus = $monitoringForm['status'] ?? 'On-going';
$selectedCampus = $monitoringForm['evsu_campus'] ?? '';
$selectedSchool = $monitoringForm['campus_school'] ?? '';
?>

<div class="row g-3">
    <div class="col-md-6">
        <label>Project</label>
        <select name="project_id" class="form-select" required>
            <option value="">Select Project</option>
            <?php foreach(($projects ?? []) as $p): ?>
                <?php $projectId = intval($p['id'] ?? 0); ?>
                <option value="<?= $projectId ?>" <?= $selectedProjectId === $projectId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['project_title'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label>Additional Monitoring Title</label>
        <input name="activity_title" class="form-control" value="<?= htmlspecialchars($monitoringForm['activity_title'] ?? '') ?>" required>
    </div>

    <div class="col-md-4">
        <label>Monitoring Date</label>
        <input type="date" name="monitoring_date" class="form-control" value="<?= htmlspecialchars($monitoringForm['monitoring_date'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label>Source of Fund</label>
        <input name="source_of_fund" class="form-control" value="<?= htmlspecialchars($monitoringForm['source_of_fund'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label>EVSU Campus / External</label>
        <select name="evsu_campus" class="form-select">
            <option value="">Select Campus / External</option>
            <?php foreach($campusOptions as $campus): ?>
                <option value="<?= htmlspecialchars($campus) ?>" <?= $selectedCampus === $campus ? 'selected' : '' ?>>
                    <?= htmlspecialchars($campus) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>School in Campus</label>
        <select name="campus_school" class="form-select">
            <option value="">Select School</option>
            <?php foreach($schoolOptions as $school): ?>
                <option value="<?= htmlspecialchars($school) ?>" <?= $selectedSchool === $school ? 'selected' : '' ?>>
                    <?= htmlspecialchars($school) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Project Status</label>
        <select name="status" class="form-select">
            <?php foreach($statusOptions as $opt): ?>
                <option value="<?= $opt ?>" <?= $selectedStatus === $opt ? 'selected' : '' ?>><?= $opt ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Terminal Report Date</label>
        <input type="date" name="terminal_report_date" class="form-control" value="<?= htmlspecialchars($monitoringForm['terminal_report_date'] ?? '') ?>">
    </div>

    <div class="col-md-8">
        <label>Recent Update</label>
        <textarea name="activity_description" class="form-control" rows="3"><?= htmlspecialchars($monitoringForm['activity_description'] ?? '') ?></textarea>
    </div>

    <div class="col-12">
        <label>Remarks</label>
        <textarea name="remarks" class="form-control" rows="3"><?= htmlspecialchars($monitoringForm['remarks'] ?? '') ?></textarea>
    </div>
</div>
