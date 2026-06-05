const defaultCenter = [11.15, 124.98];
const defaultZoom = 8;

const map = L.map('map', {
    maxBounds: [[9.50, 123.70], [13.20, 126.40]],
    maxBoundsViscosity: 0.75
}).setView(defaultCenter, defaultZoom);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

function getProjectTitle(p) {
    return p.project_title || p.title || 'Extension Project';
}

function getESFI(p) {
    if (p.esfi !== undefined && p.esfi !== null) return parseFloat(p.esfi);
    const monitoring = parseFloat(p.monitoring_count || 0);
    const participants = parseFloat(p.participants || 0);
    return parseFloat(((monitoring * 0.70) + ((participants / 100) * 0.30)).toFixed(2));
}

function esfiLabel(esfi) {
    esfi = parseFloat(esfi || 0);
    if (esfi < 1.00) return 'No / Very Low Coverage';
    if (esfi < 2.50) return 'Low Service Coverage';
    if (esfi < 3.51) return 'Moderate Service Distribution';
    return 'High Service Concentration';
}

function esfiColor(esfi) {
    esfi = parseFloat(esfi || 0);
    if (esfi < 1.00) return '#2563eb';
    if (esfi < 2.50) return '#22c55e';
    if (esfi < 3.51) return '#f59e0b';
    return '#dc2626';
}

function highlightFillColor(esfi) {
    esfi = parseFloat(esfi || 0);
    if (esfi < 1.00) return '#93c5fd';
    if (esfi < 2.50) return '#86efac';
    if (esfi < 3.51) return '#fde68a';
    return '#fca5a5';
}

function coverageRecommendation(esfi) {
    esfi = parseFloat(esfi || 0);
    if (esfi < 1.00) return 'Consider new extension intervention or validation.';
    if (esfi < 2.50) return 'Recommend additional extension activities.';
    if (esfi < 3.51) return 'Maintain and balance service distribution.';
    return 'Monitor high concentration and consider nearby underserved areas.';
}

function updateHoverPanel(p) {
    const esfi = getESFI(p);
    const panel = document.getElementById('hoverPanel');
    if (!panel) return;

    panel.innerHTML = `
        <h5 class="fw-bold">${(p.barangay || p.municipality || 'Selected Area')}</h5>
        <span class="badge" style="background:${esfiColor(esfi)}">${esfiLabel(esfi)}</span>
        <hr>
        <p class="small mb-1"><b>Program:</b> ${p.program_title || 'N/A'}</p>
        <p class="small mb-1"><b>Project:</b> ${getProjectTitle(p)}</p>
        <p class="small mb-1"><b>Barangay:</b> ${p.barangay || 'N/A'}<br><b>Municipality:</b> ${p.municipality || 'N/A'}<br><b>Province:</b> ${p.province || 'N/A'}</p>
        <p class="small mb-1"><b>Status:</b> ${p.status || 'N/A'}</p>
        <p class="small mb-1"><b>Monitoring Activities:</b> ${p.monitoring_count || 0}</p>
        <p class="small mb-1"><b>Participants:</b> ${p.participants || 0}</p>
        <p class="small mb-1"><b>ESFI:</b> ${esfi}</p>
        <p class="small mb-0"><b>Recommendation:</b> ${coverageRecommendation(esfi)}</p>
    `;
}

function resetHoverPanel() {
    const panel = document.getElementById('hoverPanel');
    if (!panel) return;
    panel.innerHTML = `
        <h5 class="fw-bold">Hovered Area</h5>
        <p class="text-muted small mb-0">Hover over a marker to view details.</p>
    `;
}

// Heatmap layer
const maxESFI = Math.max(...projects.map(p => getESFI(p)), 1);

const heatPoints = projects
    .filter(p => p.latitude && p.longitude)
    .map(p => {
        const esfi = getESFI(p);
        const intensity = Math.max(0.15, Math.min(1, esfi / maxESFI));
        return [parseFloat(p.barangay_latitude || p.latitude), parseFloat(p.barangay_longitude || p.longitude), intensity];
    });

const heatLayer = L.heatLayer(heatPoints, {
    radius: 85,
    blur: 55,
    maxZoom: 12,
    minOpacity: 0.45,
    gradient: {
        0.10: '#2563eb',
        0.35: '#22c55e',
        0.55: '#facc15',
        0.75: '#f97316',
        1.00: '#dc2626'
    }
});

// Marker and Coverage layers
const markerLayer = L.layerGroup();
const coverageLayer = L.layerGroup();

function createCoverageBox(p) {
    const lat = parseFloat(p.barangay_latitude || p.latitude);
    const lng = parseFloat(p.barangay_longitude || p.longitude);
    const esfi = getESFI(p);

    const size = 0.085;
    const bounds = [
        [lat - size, lng - size],
        [lat + size, lng + size]
    ];

    const box = L.rectangle(bounds, {
        color: esfiColor(esfi),
        weight: 1.5,
        fillColor: highlightFillColor(esfi),
        fillOpacity: 0.35,
        dashArray: '6, 4'
    });

    box.bindTooltip(`${p.barangay || p.barangay || p.municipality || getProjectTitle(p)} | ${esfiLabel(esfi)} | ESFI ${esfi}`, {
        sticky: true
    });

    return box;
}

projects.forEach(p => {
    if (!p.latitude || !p.longitude) return;
    const esfi = getESFI(p);

    // Coverage square is created for every marker but controlled by Toggle Coverage.
    coverageLayer.addLayer(createCoverageBox(p));

    const marker = L.circleMarker([parseFloat(p.barangay_latitude || p.latitude), parseFloat(p.barangay_longitude || p.longitude)], {
        radius: 8,
        color: '#ffffff',
        fillColor: esfiColor(esfi),
        fillOpacity: 0.95,
        weight: 2
    });

    marker.bindTooltip(`
        <b>${getProjectTitle(p)}</b><br>
        Program: ${p.program_title || 'N/A'}<br>
        Barangay: ${p.barangay || 'N/A'}<br>Barangay: ${p.barangay || 'N/A'}<br>Municipality: ${p.municipality || 'N/A'}, ${p.province || ''}<br>
        ESFI: <b>${esfi}</b><br>
        ${esfiLabel(esfi)}
    `, {
        direction: 'top',
        sticky: true,
        opacity: 0.95
    });

    marker.bindPopup(`
        <b>${getProjectTitle(p)}</b><br>
        Program: ${p.program_title || 'N/A'}<br>
        Barangay: ${p.barangay || 'N/A'}<br>Barangay: ${p.barangay || 'N/A'}<br>Municipality: ${p.municipality || 'N/A'}, ${p.province || ''}<br>
        Status: ${p.status || 'N/A'}<br>
        Monitoring Activities: ${p.monitoring_count || 0}<br>
        Participants: ${p.participants || 0}<br>
        <b>ESFI: ${esfi}</b><br>
        ${esfiLabel(esfi)}
    `);

    marker.on('mouseover', () => updateHoverPanel(p));
    marker.on('mouseout', () => resetHoverPanel());

    markerLayer.addLayer(marker);
});

let heatmapVisible = true;
let markersVisible = true;
let coverageVisible = true;

function toggleHeatmap() {
    if (heatmapVisible) {
        map.removeLayer(heatLayer);
        heatmapVisible = false;
    } else {
        heatLayer.addTo(map);
        heatmapVisible = true;
    }
}

function toggleMarkers() {
    if (markersVisible) {
        map.removeLayer(markerLayer);
        markersVisible = false;
        resetHoverPanel();
    } else {
        markerLayer.addTo(map);
        markersVisible = true;
    }
}

function toggleCoverage() {
    if (coverageVisible) {
        map.removeLayer(coverageLayer);
        coverageVisible = false;
    } else {
        coverageLayer.addTo(map);
        coverageVisible = true;
    }
}

function resetMap() {
    map.setView(defaultCenter, defaultZoom);
    resetHoverPanel();

    if (!heatmapVisible) {
        heatLayer.addTo(map);
        heatmapVisible = true;
    }

    if (!markersVisible) {
        markerLayer.addTo(map);
        markersVisible = true;
    }

    if (!coverageVisible) {
        coverageLayer.addTo(map);
        coverageVisible = true;
    }
}

// Default: all layers visible together.
heatLayer.addTo(map);
coverageLayer.addTo(map);
markerLayer.addTo(map);
