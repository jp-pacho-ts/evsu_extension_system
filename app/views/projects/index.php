<?php include "app/views/layouts/header.php"; ?>

<h2 class="fw-bold">📁 Project Data Entry</h2>
<p class="text-muted">Encode project details under an umbrella program. The saved projects are listed below.</p>

<?php if(isset($message) && $message): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<div class="card p-4 mb-4">
    <h5 class="fw-bold">Add Project</h5>
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-4">
                <label>Program</label>
                <select name="program_id" class="form-select" required>
                    <?php foreach($programs as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['program_title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-8">
                <label>Project Title</label>
                <input name="project_title" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label>SDG</label>
                <input name="sdg" class="form-control" placeholder="e.g., SDG 4: Quality Education">
            </div>

            <div class="col-md-4">
                <label>Partners</label>
                <input name="partners" class="form-control" placeholder="e.g., LGU, DepEd, NGO">
            </div>

            <div class="col-md-4">
                <label>Type of Clientele</label>
                <input name="type_of_clientele" class="form-control" placeholder="e.g., Students, Farmers, Residents">
            </div>

            <div class="col-md-4">
                <label>Project Leader</label>
                <input name="leader" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Assistant Leader</label>
                <input name="assistant_leader" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Members</label>
                <textarea name="members" class="form-control" rows="1"></textarea>
            </div>

            <div class="col-md-3">
                <label>Participants</label>
                <input type="number" name="participants" class="form-control" value="0">
            </div>

            <div class="col-md-3">
                <label>Project Cost</label>
                <input type="number" step="0.01" name="project_cost" class="form-control" value="0">
            </div>

            <div class="col-md-3">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control">
            </div>

            <div class="col-md-3">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Special Order No.</label>
                <input name="special_order_no" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Province</label>
                <select id="provinceSelect" name="province" class="form-select" required>
                    <option value="">Select Province</option>
                    <?php
                        $provinceList = [];
                        if(isset($locations)) {
                            foreach($locations as $loc) $provinceList[$loc['province']] = true;
                        }
                        foreach(array_keys($provinceList) as $province):
                    ?>
                        <option value="<?= htmlspecialchars($province) ?>"><?= htmlspecialchars($province) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label>Municipality / City</label>
                <select id="municipalitySelect" name="municipality" class="form-select" required>
                    <option value="">Select Municipality / City</option>
                </select>
            </div>

            <div class="col-md-4">
                <label>Target Barangay</label>
                <select id="barangaySelect" name="barangay" class="form-select">
                    <option value="">Select Barangay</option>
                </select>
                <input type="hidden" id="barangayLatitudeInput" name="barangay_latitude">
                <input type="hidden" id="barangayLongitudeInput" name="barangay_longitude">
            </div>

            <div class="col-md-4">
                <label>Latitude</label>
                <input id="latitudeInput" type="number" step="any" name="latitude" class="form-control" readonly>
            </div>

            <div class="col-md-4">
                <label>Longitude</label>
                <input id="longitudeInput" type="number" step="any" name="longitude" class="form-control" readonly>
            </div>

            <div class="col-md-4">
                <label>Status</label>
                <select name="status" class="form-select">
                    <?php foreach(['On-going','Completed','Terminated','Inactive','Expired'] as $s): ?>
                        <option><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <button class="btn btn-primary">Save Project</button>
            </div>
        </div>
    </form>
</div>

<div class="card p-3">
    <h5 class="fw-bold">Project List</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Program</th>
                    <th>Project</th><th>Barangay</th>
                    <th>SDG</th>
                    <th>Clientele</th>
                    <th>Status</th>
                    <th>Monitoring Count</th>
                    <th>Participants</th>
                    <th>ESFI</th>
                    <th>ESFI Interpretation</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($projects as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['program_title']) ?></td>
                    <td><?= htmlspecialchars($p['project_title']) ?></td><td><?= htmlspecialchars($p['barangay'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['sdg']) ?></td>
                    <td><?= htmlspecialchars($p['type_of_clientele']) ?></td>
                    <td><span class="badge bg-<?= statusBadge($p['status']) ?>"><?= $p['status'] ?></span></td>
                    <td><?= $p['monitoring_count'] ?? 0 ?></td>
                    <td><?= $p['participants'] ?? 0 ?></td>
                    <td><strong><?= $p['esfi'] ?? computeESFI($p['monitoring_count'] ?? 0, $p['participants'] ?? 0) ?></strong></td>
                    <td><?= $p['esfi_label'] ?? esfiInterpretation(computeESFI($p['monitoring_count'] ?? 0, $p['participants'] ?? 0)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<script>
const region8Locations = <?= json_encode($locations ?? []) ?>;
const region8Barangays = <?= json_encode($barangayLocations ?? []) ?>;
const provinceSelect = document.getElementById('provinceSelect');
const municipalitySelect = document.getElementById('municipalitySelect');
const latitudeInput = document.getElementById('latitudeInput');
const longitudeInput = document.getElementById('longitudeInput');
const barangaySelect = document.getElementById('barangaySelect');
const barangayLatitudeInput = document.getElementById('barangayLatitudeInput');
const barangayLongitudeInput = document.getElementById('barangayLongitudeInput');

function clearCoordinates() {
    if(latitudeInput) latitudeInput.value = '';
    if(longitudeInput) longitudeInput.value = '';
    if(barangaySelect) barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    if(barangayLatitudeInput) barangayLatitudeInput.value = '';
    if(barangayLongitudeInput) barangayLongitudeInput.value = '';
}

function loadBarangays() {
    if(!barangaySelect) return;
    const province = provinceSelect.value;
    const municipality = municipalitySelect.value;
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    if(barangayLatitudeInput) barangayLatitudeInput.value = '';
    if(barangayLongitudeInput) barangayLongitudeInput.value = '';

    region8Barangays
        .filter(b => b.province === province && b.municipality === municipality)
        .forEach(b => {
            const option = document.createElement('option');
            option.value = b.barangay;
            option.textContent = b.barangay;
            option.dataset.latitude = b.latitude;
            option.dataset.longitude = b.longitude;
            barangaySelect.appendChild(option);
        });
}

if (provinceSelect && municipalitySelect) {
    provinceSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        municipalitySelect.innerHTML = '<option value="">Select Municipality / City</option>';
        clearCoordinates();

        region8Locations
            .filter(loc => loc.province === selectedProvince)
            .forEach(loc => {
                const option = document.createElement('option');
                option.value = loc.municipality;
                option.textContent = loc.municipality;
                option.dataset.latitude = loc.latitude;
                option.dataset.longitude = loc.longitude;
                municipalitySelect.appendChild(option);
            });
    });

    municipalitySelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        latitudeInput.value = selected.dataset.latitude || '';
        longitudeInput.value = selected.dataset.longitude || '';
        loadBarangays();
    });

    barangaySelect?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        barangayLatitudeInput.value = selected.dataset.latitude || '';
        barangayLongitudeInput.value = selected.dataset.longitude || '';

        // Use barangay coordinates for exact GIS plotting when selected.
        if(selected.dataset.latitude && selected.dataset.longitude) {
            latitudeInput.value = selected.dataset.latitude;
            longitudeInput.value = selected.dataset.longitude;
        }
    });
}
</script>


<script>
function refreshBarangayDropdown() {
    const province = document.getElementById('provinceSelect')?.value || '';
    const municipality = document.getElementById('municipalitySelect')?.value || '';
    const barangaySelect = document.getElementById('barangaySelect');
    const barangayLatitudeInput = document.getElementById('barangayLatitudeInput');
    const barangayLongitudeInput = document.getElementById('barangayLongitudeInput');

    if(!barangaySelect) return;

    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    if(barangayLatitudeInput) barangayLatitudeInput.value = '';
    if(barangayLongitudeInput) barangayLongitudeInput.value = '';

    (region8Barangays || [])
        .filter(b => b.province === province && b.municipality === municipality)
        .forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.barangay;
            opt.textContent = b.barangay;
            opt.dataset.latitude = b.latitude;
            opt.dataset.longitude = b.longitude;
            barangaySelect.appendChild(opt);
        });
}

document.getElementById('provinceSelect')?.addEventListener('change', refreshBarangayDropdown);
document.getElementById('municipalitySelect')?.addEventListener('change', refreshBarangayDropdown);

document.getElementById('barangaySelect')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const lat = selected.dataset.latitude || '';
    const lng = selected.dataset.longitude || '';

    document.getElementById('barangayLatitudeInput').value = lat;
    document.getElementById('barangayLongitudeInput').value = lng;

    // Exact barangay coordinate auto-selection:
    // Once barangay is selected, main GIS latitude/longitude use the barangay point.
    if(lat && lng) {
        document.getElementById('latitudeInput').value = lat;
        document.getElementById('longitudeInput').value = lng;
    }
});
</script>

<?php include "app/views/layouts/footer.php"; ?>
