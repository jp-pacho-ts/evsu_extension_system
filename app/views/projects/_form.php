<?php
$projectForm = $projectForm ?? [];
$statusOptions = ['On-going','Completed','Terminated','Inactive','Expired'];
$sdgOptions = $sdgOptions ?? [];
$provinceList = [];
foreach(($locations ?? []) as $loc) {
    if(!empty($loc['province'])) $provinceList[$loc['province']] = true;
}
$selectedProvince = $projectForm['province'] ?? '';
$selectedMunicipality = $projectForm['municipality'] ?? '';
$selectedBarangay = $projectForm['barangay'] ?? '';
$selectedSdg = $projectForm['sdg'] ?? '';
$selectedSdgIds = [];
foreach(explode(',', (string)($projectForm['sdg_ids'] ?? '')) as $sdgId) {
    $sdgId = intval($sdgId);
    if($sdgId > 0) $selectedSdgIds[$sdgId] = true;
}
?>
<div class="row g-3">
    <div class="col-md-4">
        <label>Program</label>
        <select name="program_id" class="form-select" required>
            <?php foreach($programs as $program): ?>
                <option value="<?= intval($program['id']) ?>" <?= intval($projectForm['program_id'] ?? 0) === intval($program['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($program['program_title'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-8">
        <label>Project Title</label>
        <input name="project_title" class="form-control" value="<?= htmlspecialchars($projectForm['project_title'] ?? '') ?>" required>
    </div>

    <div class="col-md-4">
        <label>SDGs</label>
        <input type="hidden" name="sdg_ids_submitted" value="1">
        <input type="hidden" name="sdg" value="<?= htmlspecialchars($selectedSdg) ?>">
        <select name="sdg_ids[]" class="form-select" multiple size="6">
            <?php $matchedSelectedSdg = false; ?>
            <?php foreach($sdgOptions as $sdg): ?>
                <?php
                    $sdgId = intval($sdg['id'] ?? 0);
                    $sdgLabel = $sdg['label'] ?? '';
                    $isSelected = isset($selectedSdgIds[$sdgId]) || ($selectedSdg !== '' && $sdgLabel !== '' && strpos($selectedSdg, $sdgLabel) !== false);
                    if($isSelected) $matchedSelectedSdg = true;
                    if(($sdg['status'] ?? 'Active') !== 'Active' && !$isSelected) continue;
                ?>
                <option value="<?= $sdgId ?>" <?= $isSelected ? 'selected' : '' ?>><?= htmlspecialchars($sdgLabel) ?></option>
            <?php endforeach; ?>
            <?php if($selectedSdg !== '' && !$matchedSelectedSdg): ?>
                <option value="" selected><?= htmlspecialchars($selectedSdg) ?></option>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Partners</label>
        <input name="partners" class="form-control" value="<?= htmlspecialchars($projectForm['partners'] ?? '') ?>" placeholder="e.g., LGU, DepEd, NGO">
    </div>

    <div class="col-md-4">
        <label>Type of Clientele</label>
        <input name="type_of_clientele" class="form-control" value="<?= htmlspecialchars($projectForm['type_of_clientele'] ?? '') ?>" placeholder="e.g., Students, Farmers, Residents">
    </div>

    <div class="col-md-3">
        <label>Participants</label>
        <input type="number" name="participants" class="form-control" value="<?= htmlspecialchars($projectForm['participants'] ?? '0') ?>">
    </div>

    <div class="col-md-4">
        <label>Province</label>
        <select name="province" class="form-select js-province" required>
            <option value="">Select Province</option>
            <?php foreach(array_keys($provinceList) as $province): ?>
                <option value="<?= htmlspecialchars($province) ?>" <?= $selectedProvince === $province ? 'selected' : '' ?>><?= htmlspecialchars($province) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Municipality / City</label>
        <select name="municipality" class="form-select js-municipality" data-selected="<?= htmlspecialchars($selectedMunicipality) ?>" required>
            <option value="">Select Municipality / City</option>
            <?php if($selectedMunicipality): ?>
                <option value="<?= htmlspecialchars($selectedMunicipality) ?>" selected><?= htmlspecialchars($selectedMunicipality) ?></option>
            <?php endif; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Target Barangay</label>
        <select name="barangay" class="form-select js-barangay" data-selected="<?= htmlspecialchars($selectedBarangay) ?>">
            <option value="">Select Barangay</option>
            <?php if($selectedBarangay): ?>
                <option value="<?= htmlspecialchars($selectedBarangay) ?>" selected><?= htmlspecialchars($selectedBarangay) ?></option>
            <?php endif; ?>
        </select>
        <input type="hidden" class="js-barangay-latitude" name="barangay_latitude" value="<?= htmlspecialchars($projectForm['barangay_latitude'] ?? '') ?>">
        <input type="hidden" class="js-barangay-longitude" name="barangay_longitude" value="<?= htmlspecialchars($projectForm['barangay_longitude'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label>Latitude</label>
        <input type="number" step="any" name="latitude" class="form-control js-latitude" value="<?= htmlspecialchars($projectForm['latitude'] ?? '') ?>" readonly>
    </div>

    <div class="col-md-4">
        <label>Longitude</label>
        <input type="number" step="any" name="longitude" class="form-control js-longitude" value="<?= htmlspecialchars($projectForm['longitude'] ?? '') ?>" readonly>
    </div>

    <div class="col-md-4">
        <label>Status</label>
        <select name="status" class="form-select">
            <?php foreach($statusOptions as $status): ?>
                <option <?= ($projectForm['status'] ?? 'On-going') === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
