<?php
$campusOptions = ['External','EVSU Main Campus','EVSU Burauen Campus','EVSU Carigara Campus','EVSU Dulag Campus','EVSU Ormoc Campus','EVSU Tanauan Campus'];
$schoolOptions = ['SAS','SED','SAME','SOT','SAAD','SOE'];
$mapToday = date('Y-m-d');
include "app/views/layouts/header.php";
?>

<p class="text-muted">
    Heatmap, markers, coverage highlights, filters, and suggested programs for municipalities without projects.
</p>

<div class="row g-4 map-page-layout align-items-stretch">
    <div class="col-lg-6 map-main-column">
        <div class="card p-3 h-100 map-visual-card">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-2">
                <h5 class="fw-bold mb-0">Combined GIS Visualization</h5>
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleHeatmap()">Toggle Heatmap</button>
                    <button class="btn btn-sm btn-outline-dark" onclick="toggleMarkers()">Toggle Markers</button>
                    <button class="btn btn-sm btn-outline-success" onclick="toggleCoverage()">Toggle Coverage</button>
                    <button id="mapRecommenderToggle" type="button" class="btn btn-sm btn-outline-warning active" aria-pressed="true" onclick="toggleMunicipalityRecommender()">Hide Recommender Markers</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="resetMap()">Reset</button>
                </div>
            </div>

            <div class="map-filter-bar no-print mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="mapSearch" class="form-label small fw-bold">Search</label>
                        <input id="mapSearch" class="form-control form-control-sm" type="search" placeholder="Project, barangay, municipality">
                    </div>
                    <div class="col-md-2">
                        <label for="mapStatusFilter" class="form-label small fw-bold">Status</label>
                        <select id="mapStatusFilter" class="form-select form-select-sm">
                            <option value="">All Status</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="mapCoverageFilter" class="form-label small fw-bold">Coverage</label>
                        <select id="mapCoverageFilter" class="form-select form-select-sm">
                            <option value="">All Coverage</option>
                            <option value="none">No / Very Low</option>
                            <option value="low">Low</option>
                            <option value="moderate">Moderate</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="mapProvinceFilter" class="form-label small fw-bold">Province</label>
                        <select id="mapProvinceFilter" class="form-select form-select-sm">
                            <option value="">All Provinces</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="mapMunicipalityFilter" class="form-label small fw-bold">Municipality</label>
                        <select id="mapMunicipalityFilter" class="form-select form-select-sm">
                            <option value="">All Municipalities</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="mapCampusFilter" class="form-label small fw-bold">EVSU Campus / External</label>
                        <select id="mapCampusFilter" class="form-select form-select-sm">
                            <option value="">All Campus / External</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="mapSchoolFilter" class="form-label small fw-bold">School</label>
                        <select id="mapSchoolFilter" class="form-select form-select-sm">
                            <option value="">All Schools</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="mapNeedFilter" class="form-label small fw-bold">Community Need</label>
                        <select id="mapNeedFilter" class="form-select form-select-sm">
                            <option value="">All Needs</option>
                            <option value="livelihood">Livelihood</option>
                            <option value="literacy">Literacy</option>
                            <option value="health">Health</option>
                            <option value="agriculture">Agriculture</option>
                            <option value="disaster">Disaster Preparedness</option>
                            <option value="digital">Digital Skills</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="mapRecommendationFilter" class="form-label small fw-bold">Municipality Availability</label>
                        <select id="mapRecommendationFilter" class="form-select form-select-sm">
                            <option value="">All Not-Taken Municipalities</option>
                            <option value="needs_project">No Project / Not Taken</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="mapClearFilters" type="button" class="btn btn-sm btn-outline-secondary w-100">Clear Filters</button>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-2">
                <p id="mapResultCount" class="text-muted small mb-0"></p>
                <p id="mapRecommendationSummary" class="text-muted small mb-0"></p>
            </div>

            <div id="map"></div>
        </div>

        <div class="row g-3 map-support-row align-items-start">
            <div class="col-12">
                <div class="card p-3 map-legend-card">
                    <h5 class="fw-bold">Map Legend</h5>
                    <p class="small mb-2"><b>ESFI Heatmap</b></p>
                    <div class="heat-gradient"></div>
                    <div class="d-flex justify-content-between small mt-1 map-legend-scale">
                        <span>Low</span><span>Moderate</span><span>High</span>
                    </div>

                    <p class="small mb-2"><b>Coverage / Marker Color</b></p>
                    <div class="map-legend-items">
                        <p class="map-legend-item"><span class="legend-box cov-none"></span> No / Very Low Coverage</p>
                        <p class="map-legend-item"><span class="legend-box cov-low"></span> Low Service Coverage</p>
                        <p class="map-legend-item"><span class="legend-box cov-mid"></span> Moderate Service Distribution</p>
                        <p class="map-legend-item"><span class="legend-box cov-high"></span> High Service Concentration</p>
                        <p class="map-legend-item"><span class="legend-pin priority-pin-sample"></span> Recommended Municipality Area</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 map-recommender-column">
        <div class="card p-3 map-recommendation-card priority-recommender-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h5 class="fw-bold mb-0">Not-Taken Municipality Recommender</h5>
                <span id="recommendationCount" class="badge bg-success">0</span>
            </div>
            <div id="recommendationList" class="map-recommendation-list priority-recommendation-list"></div>
        </div>

        <div class="card p-3 map-formula-card" id="esfiFormulaCard">
            <h5 class="fw-bold">ESFI Formula</h5>
            <p class="small mb-2">(Monitoring Count x 0.70) + (Participants / 100 x 0.30). Monitoring Count includes saved monitoring entries, created quarterly monitoring reports, accomplishment reports, and field visit logs.</p>
            <div id="esfiFormulaResult" class="map-formula-result" aria-live="polite">
                <p class="small text-muted mb-0">Hover a project in the ESFI Ranking to view its computed ESFI result.</p>
            </div>
        </div>
    </div>
</div>

<div class="card p-3 mt-4" id="esfiRanking">
    <h5 class="fw-bold mb-3">ESFI Ranking</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Project</th>
                    <th>Barangay</th>
                    <th>Municipality</th>
                    <th>Status</th>
                    <th>Latest Monitoring</th>
                    <th>Monitoring Count</th>
                    <th>Participants</th>
                    <th>ESFI</th>
                    <th>Interpretation</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(($rankingProjects ?? []) as $p): ?>
                <tr class="js-map-row" tabindex="0" data-project-id="<?= intval($p['id'] ?? 0) ?>">
                    <td><?= htmlspecialchars($p['project_title'] ?? $p['title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['barangay'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['municipality'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['latest_monitoring_status'] ?? $p['status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['latest_monitoring_date'] ?? '') ?></td>
                    <td title="<?= intval($p['saved_monitoring_count'] ?? 0) ?> monitoring entries + <?= intval($p['quarterly_monitoring_count'] ?? 0) ?> quarterly monitoring reports + <?= intval($p['quarterly_accomplishment_count'] ?? 0) ?> accomplishment reports + <?= intval($p['field_visit_count'] ?? 0) ?> field visit logs"><?= intval($p['monitoring_count'] ?? 0) ?></td>
                    <td><?= intval($p['participants'] ?? 0) ?></td>
                    <td><strong><?= htmlspecialchars($p['esfi'] ?? computeESFI($p['monitoring_count'] ?? 0, $p['participants'] ?? 0)) ?></strong></td>
                    <td><?= htmlspecialchars($p['esfi_label'] ?? esfiInterpretation(computeESFI($p['monitoring_count'] ?? 0, $p['participants'] ?? 0))) ?></td>
                    <td class="js-map-priority"></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($rankingProjects ?? [])): ?>
                    <tr><td colspan="10" class="text-center text-muted">No ESFI ranking records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= renderPagination($rankingPagination ?? [], 'ESFI ranking records') ?>
</div>

<script>
const projects = <?= scriptJson($projects ?? [], '[]') ?>;
const gisMunicipalities = <?= scriptJson($locations ?? [], '[]') ?>;
const gisCampusOptions = <?= scriptJson($campusOptions ?? [], '[]') ?>;
const gisSchoolOptions = <?= scriptJson($schoolOptions ?? [], '[]') ?>;
const gisMapToday = <?= scriptJson($mapToday ?? '', '""') ?>;
</script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script src="public/assets/js/map.js?v=<?= filemtime('public/assets/js/map.js') ?>"></script>

<?php include "app/views/layouts/footer.php"; ?>
