<?php include "app/views/layouts/header.php"; ?>

<p class="text-muted">
    Heatmap, markers, and coverage highlights can now be used together. Hover over markers to view project details.
</p>

<div class="row g-4">
    <div class="col-lg-9">
        <div class="card p-3">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-2">
                <h5 class="fw-bold mb-0">Combined GIS Visualization</h5>
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleHeatmap()">Toggle Heatmap</button>
                    <button class="btn btn-sm btn-outline-dark" onclick="toggleMarkers()">Toggle Markers</button>
                    <button class="btn btn-sm btn-outline-success" onclick="toggleCoverage()">Toggle Coverage</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="resetMap()">Reset</button>
                </div>
            </div>

            <div class="alert alert-info py-2">
                Default view shows heatmap, markers, and coverage highlights together. Hover on a marker to view program/project details.
            </div>

            <div id="map"></div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card p-3 mb-3">
            <h5 class="fw-bold">Map Legend</h5>
            <p class="small mb-2"><b>ESFI Heatmap</b></p>
            <div class="heat-gradient"></div>
            <div class="d-flex justify-content-between small mt-1 mb-3">
                <span>Low</span><span>Moderate</span><span>High</span>
            </div>

            <p class="small mb-2"><b>Coverage / Marker Color</b></p>
            <p><span class="legend-box cov-none"></span> No / Very Low Coverage</p>
            <p><span class="legend-box cov-low"></span> Low Service Coverage</p>
            <p><span class="legend-box cov-mid"></span> Moderate Service Distribution</p>
            <p><span class="legend-box cov-high"></span> High Service Concentration</p>
        </div>

        <div class="card p-3 mb-3">
            <h5 class="fw-bold">ESFI Formula</h5>
            <p class="small">(Monitoring Activities × 0.70) + (Participants ÷ 100 × 0.30)</p>
        </div>

        <div class="card p-3" id="hoverPanel">
            <h5 class="fw-bold">Hovered Area</h5>
            <p class="text-muted small mb-0">Hover over a marker to view details.</p>
        </div>
    </div>
</div>

<div class="card p-3 mt-4">
    <h5 class="fw-bold">ESFI Ranking</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Project</th>
                    <th>Barangay</th><th>Municipality</th>
                    <th>Monitoring Count</th>
                    <th>Participants</th>
                    <th>ESFI</th>
                    <th>Interpretation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($projects as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['project_title'] ?? $p['title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['barangay'] ?? '') ?></td><td><?= htmlspecialchars($p['municipality'] ?? '') ?></td>
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
const projects = <?= json_encode($projects) ?>;
</script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script src="public/assets/js/map.js"></script>

<?php include "app/views/layouts/footer.php"; ?>
