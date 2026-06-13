<?php include "app/views/layouts/header.php"; ?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <p class="text-muted mb-0">Encode project details under an umbrella program. The saved projects are listed below.</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">Add Project</button>
</div>

<?php if(isset($message) && $message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Project List</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Program</th>
                    <th>Project</th>
                    <th>Barangay</th>
                    <th>SDG</th>
                    <th>Clientele</th>
                    <th>Status</th>
                    <th>Monitoring Count</th>
                    <th>Participants</th>
                    <th>ESFI</th>
                    <th>ESFI Interpretation</th>
                    <th class="text-end actions-column">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($projects as $p): ?>
                <?php $projectId = intval($p['id']); ?>
                <tr>
                    <td><?= htmlspecialchars($p['program_title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['project_title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['barangay'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['sdg'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['type_of_clientele'] ?? '') ?></td>
                    <td><span class="badge bg-<?= statusBadge($p['status'] ?? '') ?>"><?= htmlspecialchars($p['status'] ?? '') ?></span></td>
                    <td><?= intval($p['monitoring_count'] ?? 0) ?></td>
                    <td><?= intval($p['participants'] ?? 0) ?></td>
                    <td><strong><?= htmlspecialchars($p['esfi'] ?? computeESFI($p['monitoring_count'] ?? 0, $p['participants'] ?? 0)) ?></strong></td>
                    <td><?= htmlspecialchars($p['esfi_label'] ?? esfiInterpretation(computeESFI($p['monitoring_count'] ?? 0, $p['participants'] ?? 0))) ?></td>
                    <td class="text-end table-actions actions-column">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProjectModal<?= $projectId ?>">Edit</button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProjectModal<?= $projectId ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($projects)): ?>
                <tr><td colspan="11" class="text-center text-muted">No projects recorded yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= renderPagination($pagination ?? [], 'projects') ?>
</div>

<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" class="project-location-form">
                <input type="hidden" name="form_action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Add Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php $projectForm = []; include "app/views/projects/_form.php"; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($projects as $p): ?>
    <?php $projectId = intval($p['id']); ?>
    <div class="modal fade" id="editProjectModal<?= $projectId ?>" tabindex="-1" aria-labelledby="editProjectModalLabel<?= $projectId ?>" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" class="project-location-form">
                    <input type="hidden" name="form_action" value="update">
                    <input type="hidden" name="project_id" value="<?= $projectId ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProjectModalLabel<?= $projectId ?>">Edit Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php $projectForm = $p; include "app/views/projects/_form.php"; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Update Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteProjectModal<?= $projectId ?>" tabindex="-1" aria-labelledby="deleteProjectModalLabel<?= $projectId ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="form_action" value="delete">
                    <input type="hidden" name="project_id" value="<?= $projectId ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteProjectModalLabel<?= $projectId ?>">Delete Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">Delete this project?</p>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($p['project_title'] ?? '') ?></p>
                        <p class="text-muted small mt-2 mb-0">Projects with related monitoring records cannot be deleted.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger">Delete Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
const region8Locations = <?= json_encode($locations ?? []) ?>;
const region8Barangays = <?= json_encode($barangayLocations ?? []) ?>;

function setOptions(select, items, placeholder, selectedValue, getValue, getLabel, applyData) {
    select.innerHTML = '';
    const placeholderOption = document.createElement('option');
    placeholderOption.value = '';
    placeholderOption.textContent = placeholder;
    select.appendChild(placeholderOption);

    items.forEach(item => {
        const option = document.createElement('option');
        option.value = getValue(item);
        option.textContent = getLabel(item);
        if(applyData) applyData(option, item);
        if(option.value === selectedValue) option.selected = true;
        select.appendChild(option);
    });
}

function updateProjectMunicipalities(form) {
    const provinceSelect = form.querySelector('.js-province');
    const municipalitySelect = form.querySelector('.js-municipality');
    const selectedMunicipality = municipalitySelect.dataset.selected || municipalitySelect.value;
    const municipalities = region8Locations.filter(loc => loc.province === provinceSelect.value);

    setOptions(
        municipalitySelect,
        municipalities,
        'Select Municipality / City',
        selectedMunicipality,
        loc => loc.municipality,
        loc => loc.municipality,
        (option, loc) => {
            option.dataset.latitude = loc.latitude;
            option.dataset.longitude = loc.longitude;
        }
    );

    municipalitySelect.dataset.selected = '';
    updateProjectCoordinates(form);
    updateProjectBarangays(form);
}

function updateProjectBarangays(form) {
    const provinceSelect = form.querySelector('.js-province');
    const municipalitySelect = form.querySelector('.js-municipality');
    const barangaySelect = form.querySelector('.js-barangay');
    const selectedBarangay = barangaySelect.dataset.selected || barangaySelect.value;
    const barangays = region8Barangays.filter(item => item.province === provinceSelect.value && item.municipality === municipalitySelect.value);

    setOptions(
        barangaySelect,
        barangays,
        'Select Barangay',
        selectedBarangay,
        item => item.barangay,
        item => item.barangay,
        (option, item) => {
            option.dataset.latitude = item.latitude;
            option.dataset.longitude = item.longitude;
        }
    );

    barangaySelect.dataset.selected = '';
    updateProjectBarangayCoordinates(form);
}

function updateProjectCoordinates(form) {
    const municipalitySelect = form.querySelector('.js-municipality');
    const latitudeInput = form.querySelector('.js-latitude');
    const longitudeInput = form.querySelector('.js-longitude');
    const selected = municipalitySelect.options[municipalitySelect.selectedIndex];

    if(latitudeInput) latitudeInput.value = selected?.dataset.latitude || '';
    if(longitudeInput) longitudeInput.value = selected?.dataset.longitude || '';
}

function updateProjectBarangayCoordinates(form) {
    const barangaySelect = form.querySelector('.js-barangay');
    const barangayLatitudeInput = form.querySelector('.js-barangay-latitude');
    const barangayLongitudeInput = form.querySelector('.js-barangay-longitude');
    const latitudeInput = form.querySelector('.js-latitude');
    const longitudeInput = form.querySelector('.js-longitude');
    const selected = barangaySelect.options[barangaySelect.selectedIndex];

    if(barangayLatitudeInput) barangayLatitudeInput.value = selected?.dataset.latitude || '';
    if(barangayLongitudeInput) barangayLongitudeInput.value = selected?.dataset.longitude || '';

    if(selected?.dataset.latitude && selected?.dataset.longitude) {
        if(latitudeInput) latitudeInput.value = selected.dataset.latitude;
        if(longitudeInput) longitudeInput.value = selected.dataset.longitude;
    }
}

document.querySelectorAll('.project-location-form').forEach(form => {
    const provinceSelect = form.querySelector('.js-province');
    const municipalitySelect = form.querySelector('.js-municipality');
    const barangaySelect = form.querySelector('.js-barangay');

    updateProjectMunicipalities(form);

    provinceSelect?.addEventListener('change', () => {
        if(municipalitySelect) municipalitySelect.dataset.selected = '';
        if(barangaySelect) barangaySelect.dataset.selected = '';
        updateProjectMunicipalities(form);
    });

    municipalitySelect?.addEventListener('change', () => {
        if(barangaySelect) barangaySelect.dataset.selected = '';
        updateProjectCoordinates(form);
        updateProjectBarangays(form);
    });

    barangaySelect?.addEventListener('change', () => updateProjectBarangayCoordinates(form));
});
</script>

<?php include "app/views/layouts/footer.php"; ?>
