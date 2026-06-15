<?php
$projectForm = $projectForm ?? [];
$statusOptions = ['On-going','Completed','Terminated','Inactive','Expired'];
$sdgOptions = [
    'SDG 1: No Poverty',
    'SDG 2: Zero Hunger',
    'SDG 3: Good Health and Well-being',
    'SDG 4: Quality Education',
    'SDG 5: Gender Equality',
    'SDG 6: Clean Water and Sanitation',
    'SDG 7: Affordable and Clean Energy',
    'SDG 8: Decent Work and Economic Growth',
    'SDG 9: Industry, Innovation and Infrastructure',
    'SDG 10: Reduced Inequalities',
    'SDG 11: Sustainable Cities and Communities',
    'SDG 12: Responsible Consumption and Production',
    'SDG 13: Climate Action',
    'SDG 14: Life Below Water',
    'SDG 15: Life on Land',
    'SDG 16: Peace, Justice and Strong Institutions',
    'SDG 17: Partnerships for the Goals',
];
$provinceList = [];
foreach(($locations ?? []) as $loc) {
    if(!empty($loc['province'])) $provinceList[$loc['province']] = true;
}
$selectedProvince = $projectForm['province'] ?? '';
$selectedMunicipality = $projectForm['municipality'] ?? '';
$selectedBarangay = $projectForm['barangay'] ?? '';
$selectedSdg = $projectForm['sdg'] ?? '';
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
        <label>SDG</label>
        <select name="sdg" class="form-select">
            <option value="">Select SDG</option>
            <?php if($selectedSdg && !in_array($selectedSdg, $sdgOptions, true)): ?>
                <option value="<?= htmlspecialchars($selectedSdg) ?>" selected><?= htmlspecialchars($selectedSdg) ?></option>
            <?php endif; ?>
            <?php foreach($sdgOptions as $sdg): ?>
                <option value="<?= htmlspecialchars($sdg) ?>" <?= $selectedSdg === $sdg ? 'selected' : '' ?>><?= htmlspecialchars($sdg) ?></option>
            <?php endforeach; ?>
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
