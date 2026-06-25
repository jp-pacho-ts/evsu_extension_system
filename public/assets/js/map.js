const defaultCenter = [11.15, 124.98];
const defaultZoom = 8;
const allProjects = typeof projects !== 'undefined' && Array.isArray(projects) ? projects : [];
const allMunicipalityLocations = typeof gisMunicipalities !== 'undefined' && Array.isArray(gisMunicipalities) ? gisMunicipalities : [];
let osmMunicipalityLocations = [];
let osmMunicipalitiesLoaded = false;
let osmMunicipalitiesLoading = false;
let osmMunicipalitiesFailed = false;
const fixedCampusOptions = typeof gisCampusOptions !== 'undefined' ? gisCampusOptions : [];
const fixedSchoolOptions = typeof gisSchoolOptions !== 'undefined' ? gisSchoolOptions : [];
const mapToday = new Date(`${typeof gisMapToday !== 'undefined' ? gisMapToday : new Date().toISOString().slice(0, 10)}T00:00:00`);
const mapDataBounds = {
    south: 9.50,
    west: 123.70,
    north: 13.20,
    east: 126.40
};
const mapBounds = [[mapDataBounds.south, mapDataBounds.west], [mapDataBounds.north, mapDataBounds.east]];
const region8Provinces = ['Biliran', 'Eastern Samar', 'Leyte', 'Northern Samar', 'Samar', 'Southern Leyte'];
const needCategories = {
    livelihood: {
        label: 'Livelihood',
        program: 'Entrepreneurship and Skills Training',
        keywords: ['livelihood', 'entrepreneur', 'enterprise', 'business', 'income', 'skills', 'employment', 'training', 'cooperative', 'financial', 'food processing']
    },
    literacy: {
        label: 'Literacy',
        program: 'Literacy and Learning Support',
        keywords: ['literacy', 'reading', 'writing', 'education', 'learning', 'tutorial', 'school', 'student', 'teacher', 'youth', 'als']
    },
    health: {
        label: 'Health',
        program: 'Community Health Awareness',
        keywords: ['health', 'medical', 'nutrition', 'wellness', 'sanitation', 'hygiene', 'mental', 'dental', 'maternal', 'disease']
    },
    agriculture: {
        label: 'Agriculture',
        program: 'Sustainable Agriculture Support',
        keywords: ['agriculture', 'farming', 'farmers', 'fishery', 'fisherfolk', 'crop', 'livestock', 'organic', 'aquaculture', 'harvest']
    },
    disaster: {
        label: 'Disaster Preparedness',
        program: 'DRRM and Community Resilience Training',
        keywords: ['disaster', 'drrm', 'risk', 'resilience', 'preparedness', 'emergency', 'climate', 'flood', 'typhoon', 'evacuation', 'environment']
    },
    digital: {
        label: 'Digital Skills',
        program: 'Digital Literacy and ICT Training',
        keywords: ['digital', 'computer', 'ict', 'technology', 'online', 'internet', 'coding', 'software', 'data', 'multimedia']
    }
};
const defaultNeedKey = 'livelihood';
const baselineNeedProfile = {
    key: 'baseline',
    label: 'Needs Assessment',
    program: 'Community Needs Assessment and Project Profiling',
    source: 'baseline'
};
const notTakenMunicipalityColor = '#16a34a';

const map = L.map('map', {
    maxBounds: mapBounds,
    maxBoundsViscosity: 0.75
}).setView(defaultCenter, defaultZoom);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'OpenStreetMap contributors'
}).addTo(map);

let heatLayer = null;
let heatmapVisible = true;
let markersVisible = true;
let coverageVisible = true;
let municipalityRecommenderMarkersVisible = true;

const markerLayer = L.layerGroup();
const coverageLayer = L.layerGroup();
const markerByProjectId = new Map();
const markerByMunicipalityId = new Map();
const projectById = new Map(allProjects.map(p => [String(p.id || ''), p]));
let activeFormulaProjectId = null;

const filterControls = {
    search: document.getElementById('mapSearch'),
    status: document.getElementById('mapStatusFilter'),
    coverage: document.getElementById('mapCoverageFilter'),
    province: document.getElementById('mapProvinceFilter'),
    municipality: document.getElementById('mapMunicipalityFilter'),
    campus: document.getElementById('mapCampusFilter'),
    school: document.getElementById('mapSchoolFilter'),
    need: document.getElementById('mapNeedFilter'),
    recommendation: document.getElementById('mapRecommendationFilter')
};

function syncRecommenderHeight() {
    const mapCard = document.querySelector('.map-visual-card');
    const recommenderCard = document.querySelector('.priority-recommender-card');
    if (!mapCard || !recommenderCard) return;

    if (!window.matchMedia('(min-width: 992px)').matches) {
        recommenderCard.style.removeProperty('--priority-recommender-height');
        return;
    }

    const mapHeight = Math.ceil(mapCard.getBoundingClientRect().height);
    recommenderCard.style.setProperty('--priority-recommender-height', `${mapHeight}px`);
}

function queueRecommenderHeightSync() {
    window.requestAnimationFrame(syncRecommenderHeight);
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function safeNumber(value) {
    const number = parseFloat(value);
    return Number.isFinite(number) ? number : 0;
}

function formatCount(value) {
    return Math.round(safeNumber(value)).toLocaleString();
}

function formatScore(value) {
    return safeNumber(value).toFixed(2);
}

function getProjectTitle(p) {
    return p.project_title || p.title || 'Extension Project';
}

function getProjectStatus(p) {
    return p.latest_monitoring_status || p.status || 'N/A';
}

function textForProject(p) {
    return [
        getProjectTitle(p),
        p.program_title,
        p.sdg,
        p.type_of_clientele,
        p.partners,
        p.latest_monitoring_title,
        p.latest_update,
        p.campus_school
    ].join(' ').toLowerCase();
}

function selectedNeedKey() {
    const value = filterControls.need?.value || '';
    return needCategories[value] ? value : '';
}

function detectNeedKey(text) {
    const value = String(text || '').toLowerCase();
    let bestKey = '';
    let bestScore = 0;

    Object.entries(needCategories).forEach(([key, category]) => {
        const score = category.keywords.reduce((sum, keyword) => {
            return sum + (value.includes(keyword) ? 1 : 0);
        }, 0);

        if (score > bestScore) {
            bestKey = key;
            bestScore = score;
        }
    });

    return bestKey;
}

function needProfileFromKey(key, source = 'inferred') {
    const safeKey = needCategories[key] ? key : defaultNeedKey;
    return {
        key: safeKey,
        label: needCategories[safeKey].label,
        program: needCategories[safeKey].program,
        source
    };
}

function projectNeedProfile(p) {
    const selected = selectedNeedKey();
    if (selected) return needProfileFromKey(selected, 'selected');
    return needProfileFromKey(detectNeedKey(textForProject(p)) || defaultNeedKey);
}

function municipalityNeedProfile(area) {
    const selected = selectedNeedKey();
    if (selected) return needProfileFromKey(selected, 'selected');
    if (municipalityIsNotTaken(area)) return baselineNeedProfile;
    const areaText = area.projects.map(project => textForProject(project)).join(' ');
    return needProfileFromKey(detectNeedKey(areaText) || defaultNeedKey);
}

function projectMatchesNeed(p, needKey) {
    if (!needKey) return true;
    return detectNeedKey(textForProject(p)) === needKey;
}

function municipalityMatchesNeed(area, needKey) {
    if (!needKey) return true;
    if (area.project_count === 0) return true;
    return area.projects.some(project => projectMatchesNeed(project, needKey));
}

function getESFI(p) {
    if (p.esfi !== undefined && p.esfi !== null) return parseFloat(p.esfi);
    const monitoring = parseFloat(p.monitoring_count || 0);
    const participants = parseFloat(p.participants || 0);
    return parseFloat(((monitoring * 0.70) + ((participants / 100) * 0.30)).toFixed(2));
}

function esfiFormulaParts(p) {
    const monitoring = safeNumber(p.monitoring_count);
    const participants = safeNumber(p.participants);
    const monitoringScore = monitoring * 0.70;
    const participantScore = (participants / 100) * 0.30;
    const result = monitoringScore + participantScore;

    return {
        monitoring,
        participants,
        monitoringScore,
        participantScore,
        result
    };
}

function getCoordinates(p) {
    const lat = parseFloat(p.barangay_latitude || p.latitude);
    const lng = parseFloat(p.barangay_longitude || p.longitude);
    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
    return [lat, lng];
}

function coverageBucket(esfi) {
    esfi = parseFloat(esfi || 0);
    if (esfi < 1.00) return 'none';
    if (esfi < 2.50) return 'low';
    if (esfi < 3.51) return 'moderate';
    return 'high';
}

function esfiLabel(esfi) {
    return {
        none: 'No / Very Low Coverage',
        low: 'Low Service Coverage',
        moderate: 'Moderate Service Distribution',
        high: 'High Service Concentration'
    }[coverageBucket(esfi)];
}

function esfiColor(esfi) {
    return {
        none: '#2563eb',
        low: '#22c55e',
        moderate: '#f59e0b',
        high: '#dc2626'
    }[coverageBucket(esfi)];
}

function highlightFillColor(esfi) {
    return {
        none: '#93c5fd',
        low: '#86efac',
        moderate: '#fde68a',
        high: '#fca5a5'
    }[coverageBucket(esfi)];
}

function daysSinceLatestMonitoring(p) {
    if (!p.latest_monitoring_date) return null;
    const latest = new Date(`${p.latest_monitoring_date}T00:00:00`);
    if (Number.isNaN(latest.getTime())) return null;
    const days = Math.floor((mapToday - latest) / 86400000);
    return Math.max(0, days);
}

function normalizeLocationName(value) {
    return String(value || '')
        .toLowerCase()
        .replace(/\bcity of\b/g, '')
        .replace(/\bmunicipality of\b/g, '')
        .replace(/\bprovince of\b/g, '')
        .replace(/\bcity\b/g, '')
        .replace(/\bmunicipality\b/g, '')
        .replace(/[^a-z0-9]+/g, ' ')
        .trim();
}

function municipalityKey(province, municipality) {
    return `${normalizeLocationName(province)}|${normalizeLocationName(municipality)}`;
}

function findExistingMunicipalityArea(areaMap, province, municipality) {
    const exactKey = municipalityKey(province, municipality);
    if (areaMap.has(exactKey)) return areaMap.get(exactKey);

    const municipalityPart = normalizeLocationName(municipality);
    if (!municipalityPart) return null;

    const matches = Array.from(areaMap.values()).filter(area => {
        const sameMunicipality = normalizeLocationName(area.municipality) === municipalityPart;
        const sameProvince = !province || !area.province || normalizeLocationName(area.province) === normalizeLocationName(province);
        return sameMunicipality && sameProvince;
    });

    return matches.length === 1 ? matches[0] : null;
}

function addMunicipalityArea(areaMap, province, municipality, coordinates = null) {
    if (!municipality) return null;
    province = province || '';

    const key = municipalityKey(province, municipality);
    let area = findExistingMunicipalityArea(areaMap, province, municipality);
    if (!area) {
        areaMap.set(key, {
            id: key,
            province,
            municipality,
            latitude: null,
            longitude: null,
            projects: []
        });
        area = areaMap.get(key);
    }

    if (!area.province && province) area.province = province;
    if (coordinates && area.latitude === null && area.longitude === null) {
        area.latitude = coordinates[0];
        area.longitude = coordinates[1];
    }
    return area;
}

function sourceMunicipalityLocations() {
    return [...allMunicipalityLocations, ...osmMunicipalityLocations];
}

function buildMunicipalityAreas() {
    const areaMap = new Map();

    sourceMunicipalityLocations().forEach(location => {
        const lat = parseFloat(location.latitude);
        const lng = parseFloat(location.longitude);
        const coordinates = Number.isFinite(lat) && Number.isFinite(lng) ? [lat, lng] : null;
        addMunicipalityArea(areaMap, location.province, location.municipality, coordinates);
    });

    allProjects.forEach(project => {
        const area = addMunicipalityArea(areaMap, project.province, project.municipality, getCoordinates(project));
        if (area) area.projects.push(project);
    });

    return Array.from(areaMap.values()).map(area => {
        const latestDates = area.projects
            .map(project => project.latest_monitoring_date || '')
            .filter(Boolean)
            .sort();
        const projectCount = area.projects.length;
        const totalMonitoring = area.projects.reduce((sum, project) => sum + parseInt(project.monitoring_count || 0, 10), 0);
        const participants = area.projects.reduce((sum, project) => sum + parseInt(project.participants || 0, 10), 0);
        const averageEsfi = projectCount
            ? area.projects.reduce((sum, project) => sum + getESFI(project), 0) / projectCount
            : 0;
        const statuses = area.projects.map(project => getProjectStatus(project));
        const partnerCount = area.projects.filter(project => String(project.partners || '').trim() !== '').length;

        return {
            ...area,
            average_esfi: parseFloat(averageEsfi.toFixed(2)),
            latest_monitoring_date: latestDates.length ? latestDates[latestDates.length - 1] : '',
            monitoring_count: totalMonitoring,
            participants,
            partner_count: partnerCount,
            problem_status_count: statuses.filter(status => ['Inactive', 'Expired', 'Terminated'].includes(status)).length,
            project_count: projectCount,
            status_summary: projectCount ? Array.from(new Set(statuses)).join(', ') : 'No Project'
        };
    });
}

let municipalityAreas = buildMunicipalityAreas();

function provinceFromOsmTags(tags = {}) {
    const fields = [
        tags['is_in:province'],
        tags['addr:province'],
        tags.province,
        tags['is_in']
    ].filter(Boolean);

    const fieldText = fields.join(' ').toLowerCase();
    return [...region8Provinces]
        .sort((a, b) => b.length - a.length)
        .find(province => fieldText.includes(province.toLowerCase())) || '';
}

function osmMunicipalityFromElement(element, fallbackProvince = '') {
    const tags = element.tags || {};
    const municipality = tags.name || tags['name:en'] || '';
    const province = provinceFromOsmTags(tags) || fallbackProvince;
    const latitude = parseFloat(element.center?.lat ?? element.lat);
    const longitude = parseFloat(element.center?.lon ?? element.lon);

    if (!municipality || !Number.isFinite(latitude) || !Number.isFinite(longitude)) return null;

    return {
        province,
        municipality,
        latitude,
        longitude,
        source: 'openstreetmap'
    };
}

function easternVisayasMunicipalityQuery() {
    return `
        [out:json][timeout:25];
        (
            area["ISO3166-2"="PH-08"]["boundary"="administrative"];
            area["name"="Eastern Visayas"]["boundary"="administrative"];
        )->.region;
        (
            relation["boundary"="administrative"]["admin_level"~"^(7|8)$"]["name"](area.region);
            node["place"~"^(city|town)$"]["name"](area.region);
        );
        out center tags;
    `;
}

function provinceMunicipalityQuery(province) {
    return `
        [out:json][timeout:25];
        area["name"="${province}"]["boundary"="administrative"]["admin_level"="6"]->.province;
        (
            relation["boundary"="administrative"]["admin_level"~"^(7|8)$"]["name"](area.province);
            node["place"~"^(city|town)$"]["name"](area.province);
        );
        out center tags;
    `;
}

async function fetchOsmMunicipalities(query, fallbackProvince = '') {
    const response = await fetch('https://overpass-api.de/api/interpreter', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
        body: new URLSearchParams({ data: query })
    });

    if (!response.ok) throw new Error(`OpenStreetMap lookup failed with ${response.status}`);

    const payload = await response.json();
    return (payload.elements || [])
        .map(element => osmMunicipalityFromElement(element, fallbackProvince))
        .filter(Boolean);
}

function uniqueMunicipalityLocations(locations) {
    const locationsByKey = new Map();

    locations.forEach(location => {
        const key = municipalityKey(location.province, location.municipality);
        const existing = locationsByKey.get(key);

        if (!existing) {
            locationsByKey.set(key, location);
            return;
        }

        if (!existing.province && location.province) existing.province = location.province;
        if ((existing.latitude === null || existing.longitude === null) && location.latitude !== null && location.longitude !== null) {
            existing.latitude = location.latitude;
            existing.longitude = location.longitude;
        }
    });

    return Array.from(locationsByKey.values());
}

async function loadMapMunicipalitiesFromOsm() {
    if (osmMunicipalitiesLoaded || osmMunicipalitiesLoading) return;

    osmMunicipalitiesLoading = true;
    osmMunicipalitiesFailed = false;
    renderRecommendations(getFilteredProjects(), getFilteredMunicipalityAreas());

    try {
        let locations = [];

        try {
            locations = await fetchOsmMunicipalities(easternVisayasMunicipalityQuery());
        } catch (error) {
            console.warn('Unable to load Eastern Visayas municipality data from OpenStreetMap.', error);
        }

        if (!locations.length || locations.some(location => !location.province)) {
            const provinceResults = await Promise.allSettled(
                region8Provinces.map(province => fetchOsmMunicipalities(provinceMunicipalityQuery(province), province))
            );
            const provinceLocations = provinceResults
                .filter(result => result.status === 'fulfilled')
                .flatMap(result => result.value);

            locations = [...locations, ...provinceLocations];
        }

        if (!locations.length) throw new Error('OpenStreetMap lookup did not return municipality data.');

        osmMunicipalityLocations = uniqueMunicipalityLocations(locations);
        osmMunicipalitiesLoaded = true;
    } catch (error) {
        osmMunicipalitiesFailed = true;
        console.warn('Unable to load OpenStreetMap municipality data.', error);
    } finally {
        osmMunicipalitiesLoading = false;
        municipalityAreas = buildMunicipalityAreas();
        populateFilters();
        applyFilters();
    }
}

function recommendationFor(p) {
    const esfi = getESFI(p);
    const bucket = coverageBucket(esfi);
    const status = getProjectStatus(p);
    const monitoringCount = parseInt(p.monitoring_count || 0, 10);
    const participants = parseInt(p.participants || 0, 10);
    const days = daysSinceLatestMonitoring(p);
    const need = projectNeedProfile(p);
    const hasPartner = String(p.partners || '').trim() !== '';
    const reasons = [];
    let score = 0;

    if (bucket === 'none') {
        score += 35;
        reasons.push('very low coverage');
    } else if (bucket === 'low') {
        score += 25;
        reasons.push('low service coverage');
    } else if (bucket === 'moderate') {
        score += 10;
        reasons.push('moderate coverage to maintain');
    }

    if (['Inactive', 'Expired', 'Terminated'].includes(status)) {
        score += 25;
        reasons.push(`${status.toLowerCase()} monitoring status`);
    } else if (status === 'On-going') {
        score += 8;
        reasons.push('ongoing implementation');
    }

    if (monitoringCount <= 0) {
        score += 25;
        reasons.push('no monitoring entry');
    } else if (days === null) {
        score += 18;
        reasons.push('no monitoring date');
    } else if (days > 180) {
        score += 20;
        reasons.push(`no recent monitoring for ${days} days`);
    } else if (days > 90) {
        score += 12;
        reasons.push(`monitoring older than ${days} days`);
    }

    if (participants >= 150) {
        score += 15;
        reasons.push('high beneficiary reach');
    } else if (participants >= 75) {
        score += 10;
        reasons.push('good beneficiary reach');
    } else if (participants > 0) {
        score += 5;
        reasons.push('active beneficiary group');
    }

    if (hasPartner) {
        score += 5;
        reasons.push('available partner for coordination');
    }

    score = Math.min(100, score);

    let action = 'Maintain monitoring and balance service distribution.';
    if (status === 'Inactive') action = 'Schedule a reactivation assessment.';
    else if (status === 'Expired') action = 'Review timeline for renewal or closeout.';
    else if (status === 'Terminated') action = 'Validate termination records and archive lessons.';
    else if (status === 'Completed') action = 'Validate outcomes and document impact.';
    else if (bucket === 'none' || bucket === 'low') action = 'Plan additional extension intervention.';
    else if (days !== null && days > 90) action = 'Schedule a follow-up monitoring visit.';

    const level = score >= 60 ? 'High' : (score >= 35 ? 'Medium' : 'Low');
    return {
        action,
        bucket,
        days,
        level,
        need,
        reasons: reasons.length ? reasons : ['balanced monitoring profile'],
        score,
        status
    };
}

function municipalityRecommendationFor(area) {
    const projectCount = parseInt(area.project_count || 0, 10);
    const monitoringCount = parseInt(area.monitoring_count || 0, 10);
    const participants = parseInt(area.participants || 0, 10);
    const partnerCount = parseInt(area.partner_count || 0, 10);
    const days = daysSinceLatestMonitoring(area);
    const bucket = coverageBucket(area.average_esfi || 0);
    const need = municipalityNeedProfile(area);
    const reasons = [];
    let score = 0;
    let action = 'Maintain municipal coverage and monitor project outcomes.';

    if (projectCount === 0) {
        score += 70;
        reasons.push('no recorded extension project');
        reasons.push('no project-based monitoring yet');
        action = 'Create a new extension project for this not-taken municipality.';
    } else if (projectCount === 1) {
        score += 38;
        reasons.push('only one recorded project');
        action = 'Review whether municipal coverage should be expanded.';
    } else if (projectCount <= 3) {
        score += 32;
        reasons.push('limited municipal project coverage');
        action = 'Review whether municipal coverage should be expanded.';
    } else if (projectCount <= 5) {
        score += 16;
        reasons.push('moderate municipal project coverage');
    }

    if (bucket === 'none') {
        score += 20;
        reasons.push('very low municipal ESFI');
    } else if (bucket === 'low') {
        score += 15;
        reasons.push('low municipal ESFI');
    }

    if (area.problem_status_count > 0) {
        score += Math.min(25, area.problem_status_count * 10);
        reasons.push('has inactive, expired, or terminated monitoring status');
        if (projectCount > 0) action = 'Assess municipality for new or replacement project intervention.';
    }

    if (projectCount > 0 && monitoringCount <= projectCount) {
        score += 10;
        reasons.push('low monitoring activity per project');
    }

    if (projectCount > 0) {
        if (days === null) {
            score += 15;
            reasons.push('no latest monitoring date');
        } else if (days > 180) {
            score += 15;
            reasons.push(`no recent monitoring for ${days} days`);
        } else if (days > 90) {
            score += 8;
            reasons.push(`monitoring older than ${days} days`);
        }
    }

    if (participants >= 150 && projectCount <= 2) {
        score += 8;
        reasons.push('large beneficiary base with limited projects');
    }

    if (partnerCount > 0) {
        score += 5;
        reasons.push('has partner organization for coordination');
    }

    score = Math.min(100, score);
    const level = score >= 60 ? 'High' : (score >= 35 ? 'Medium' : 'Low');

    return {
        action,
        bucket,
        days,
        level,
        need,
        reasons: reasons.length ? reasons : ['balanced municipality coverage'],
        score,
        status: area.status_summary || 'No Project'
    };
}

function priorityColor(recommendation) {
    if (recommendation.score >= 60) return '#dc2626';
    if (recommendation.score >= 35) return '#f59e0b';
    return '#2563eb';
}

function priorityPinIcon(recommendation) {
    return L.divIcon({
        className: 'priority-pin-icon',
        html: `<span class="priority-pin-shape" style="--pin-color:${notTakenMunicipalityColor}"></span>`,
        iconSize: [30, 38],
        iconAnchor: [15, 34],
        popupAnchor: [0, -32],
        tooltipAnchor: [0, -32]
    });
}

function projectMatchesRecommendation(p, filter) {
    if (!filter) return true;
    const recommendation = recommendationFor(p);
    const bucket = coverageBucket(getESFI(p));
    const status = recommendation.status;
    const days = recommendation.days;

    if (filter === 'needs_project') return false;
    if (filter === 'high_priority') return recommendation.score >= 60;
    if (filter === 'needs_follow_up') {
        return ['Inactive', 'Expired', 'Terminated', 'On-going'].includes(status) || days === null || days > 90;
    }
    if (filter === 'low_coverage') return bucket === 'none' || bucket === 'low';
    if (filter === 'impact_validation') return status === 'Completed';
    return true;
}

function municipalityIsNotTaken(area) {
    return parseInt(area.project_count || 0, 10) === 0;
}

function municipalityMatchesRecommendation(area, filter) {
    if (!filter) return true;
    const recommendation = municipalityRecommendationFor(area);
    const bucket = coverageBucket(area.average_esfi || 0);
    const days = recommendation.days;

    if (filter === 'needs_project') {
        return municipalityIsNotTaken(area);
    }
    if (filter === 'high_priority') return recommendation.score >= 60;
    if (filter === 'needs_follow_up') return area.project_count > 0 && (area.problem_status_count > 0 || days === null || days > 90);
    if (filter === 'low_coverage') return bucket === 'none' || bucket === 'low';
    if (filter === 'impact_validation') return false;
    return true;
}

function matchesSearch(p, value) {
    if (!value) return true;
    const need = projectNeedProfile(p);
    const haystack = [
        getProjectTitle(p),
        p.program_title,
        p.barangay,
        p.municipality,
        p.province,
        p.sdg,
        p.partners,
        p.type_of_clientele,
        p.evsu_campus,
        p.campus_school,
        need.label,
        need.program
    ].join(' ').toLowerCase();
    return haystack.includes(value.toLowerCase());
}

function matchesMunicipalitySearch(area, value) {
    if (!value) return true;
    const need = municipalityNeedProfile(area);
    const projectText = area.projects.map(project => getProjectTitle(project)).join(' ');
    const availabilityText = municipalityIsNotTaken(area) ? 'not taken no project unserved available' : 'has project';
    const haystack = [
        area.municipality,
        area.province,
        area.status_summary,
        projectText,
        availabilityText,
        need.label,
        need.program
    ].join(' ').toLowerCase();
    return haystack.includes(value.toLowerCase());
}

function getFilteredProjects() {
    const search = filterControls.search?.value.trim() || '';
    const status = filterControls.status?.value || '';
    const coverage = filterControls.coverage?.value || '';
    const province = filterControls.province?.value || '';
    const municipality = filterControls.municipality?.value || '';
    const campus = filterControls.campus?.value || '';
    const school = filterControls.school?.value || '';
    const need = selectedNeedKey();
    const recommendation = filterControls.recommendation?.value || '';

    return allProjects.filter(p => {
        const esfi = getESFI(p);
        return matchesSearch(p, search)
            && (!status || getProjectStatus(p) === status)
            && (!coverage || coverageBucket(esfi) === coverage)
            && (!province || (p.province || '') === province)
            && (!municipality || (p.municipality || '') === municipality)
            && (!campus || (p.evsu_campus || '') === campus)
            && (!school || (p.campus_school || '') === school)
            && projectMatchesNeed(p, need)
            && projectMatchesRecommendation(p, recommendation);
    });
}

function getFilteredMunicipalityAreas() {
    const search = filterControls.search?.value.trim() || '';
    const status = filterControls.status?.value || '';
    const coverage = filterControls.coverage?.value || '';
    const province = filterControls.province?.value || '';
    const municipality = filterControls.municipality?.value || '';
    const campus = filterControls.campus?.value || '';
    const school = filterControls.school?.value || '';
    const need = selectedNeedKey();
    const recommendation = filterControls.recommendation?.value || '';

    return municipalityAreas.filter(area => {
        const areaRecommendation = municipalityRecommendationFor(area);
        const areaStatuses = area.status_summary.split(', ').filter(Boolean);
        const hasCampus = !campus || area.projects.some(project => (project.evsu_campus || '') === campus);
        const hasSchool = !school || area.projects.some(project => (project.campus_school || '') === school);

        return municipalityIsNotTaken(area)
            && matchesMunicipalitySearch(area, search)
            && (!status || (status === 'No Project' ? area.project_count === 0 : areaStatuses.includes(status)))
            && (!coverage || coverageBucket(area.average_esfi || 0) === coverage)
            && (!province || area.province === province)
            && (!municipality || area.municipality === municipality)
            && hasCampus
            && hasSchool
            && municipalityMatchesNeed(area, need)
            && municipalityMatchesRecommendation(area, recommendation)
            && areaRecommendation.score > 0;
    });
}

function optionValues(values) {
    return Array.from(new Set(values.filter(value => String(value || '').trim() !== '')))
        .sort((a, b) => String(a).localeCompare(String(b)));
}

function populateSelect(select, values, allLabel) {
    if (!select) return;
    const current = select.value;
    select.innerHTML = `<option value="">${escapeHtml(allLabel)}</option>`;
    optionValues(values).forEach(value => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        select.appendChild(option);
    });
    if (optionValues(values).includes(current)) select.value = current;
}

function populateMunicipalityOptions() {
    const province = filterControls.province?.value || '';
    const municipalities = municipalityAreas
        .filter(area => !province || area.province === province)
        .map(area => area.municipality || '');
    populateSelect(filterControls.municipality, municipalities, 'All Municipalities');
}

function populateFilters() {
    populateSelect(filterControls.status, ['No Project', ...allProjects.map(p => getProjectStatus(p))], 'All Status');
    populateSelect(filterControls.province, municipalityAreas.map(area => area.province || ''), 'All Provinces');
    populateMunicipalityOptions();
    populateSelect(filterControls.campus, [...fixedCampusOptions, ...allProjects.map(p => p.evsu_campus || '')], 'All Campus / External');
    populateSelect(filterControls.school, [...fixedSchoolOptions, ...allProjects.map(p => p.campus_school || '')], 'All Schools');
}

function createHeatLayer(items) {
    const maxESFI = Math.max(1, ...items.map(p => getESFI(p)));
    const heatPoints = items
        .map(p => {
            const coordinates = getCoordinates(p);
            if (!coordinates) return null;
            const intensity = Math.max(0.15, Math.min(1, getESFI(p) / maxESFI));
            return [coordinates[0], coordinates[1], intensity];
        })
        .filter(Boolean);

    return L.heatLayer(heatPoints, {
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
}

function createCoverageBox(p) {
    const coordinates = getCoordinates(p);
    if (!coordinates) return null;
    const [lat, lng] = coordinates;
    const esfi = getESFI(p);
    const recommendation = recommendationFor(p);
    const size = 0.085;

    const box = L.rectangle([
        [lat - size, lng - size],
        [lat + size, lng + size]
    ], {
        color: priorityColor(recommendation),
        weight: recommendation.score >= 60 ? 2.5 : 1.5,
        fillColor: highlightFillColor(esfi),
        fillOpacity: 0.35,
        dashArray: '6, 4'
    });

    box.bindTooltip(`${escapeHtml(p.barangay || p.municipality || getProjectTitle(p))} | ${escapeHtml(esfiLabel(esfi))} | Priority ${recommendation.score}`, {
        sticky: true
    });

    return box;
}

function markerTooltip(p, recommendation) {
    const esfi = getESFI(p);
    return `
        <b>${escapeHtml(getProjectTitle(p))}</b><br>
        Program: ${escapeHtml(p.program_title || 'N/A')}<br>
        Barangay: ${escapeHtml(p.barangay || 'N/A')}<br>
        Municipality: ${escapeHtml(p.municipality || 'N/A')}, ${escapeHtml(p.province || '')}<br>
        Need: ${escapeHtml(recommendation.need.label)}<br>
        Status: ${escapeHtml(recommendation.status)}<br>
        ESFI: <b>${escapeHtml(esfi)}</b><br>
        Priority: <b>${escapeHtml(recommendation.score)}</b> (${escapeHtml(recommendation.level)})
    `;
}

function createMarker(p) {
    const coordinates = getCoordinates(p);
    if (!coordinates) return null;

    const recommendation = recommendationFor(p);
    const esfi = getESFI(p);
    const marker = L.circleMarker(coordinates, {
        color: recommendation.score >= 60 ? priorityColor(recommendation) : '#ffffff',
        fillColor: esfiColor(esfi),
        fillOpacity: 0.95,
        weight: recommendation.score >= 60 ? 3 : 2
    });
    marker.setRadius(recommendation.score >= 60 ? 10 : 8);

    marker.bindTooltip(markerTooltip(p, recommendation), {
        direction: 'top',
        sticky: true,
        opacity: 0.95
    });

    marker.bindPopup(`
        <b>${escapeHtml(getProjectTitle(p))}</b><br>
        Program: ${escapeHtml(p.program_title || 'N/A')}<br>
        Barangay: ${escapeHtml(p.barangay || 'N/A')}<br>
        Municipality: ${escapeHtml(p.municipality || 'N/A')}, ${escapeHtml(p.province || '')}<br>
        Campus: ${escapeHtml(p.evsu_campus || 'N/A')}<br>
        School: ${escapeHtml(p.campus_school || 'N/A')}<br>
        Community Need: ${escapeHtml(recommendation.need.label)}<br>
        Suggested Program: ${escapeHtml(recommendation.need.program)}<br>
        Status: ${escapeHtml(recommendation.status)}<br>
        Latest Monitoring: ${escapeHtml(p.latest_monitoring_date || 'N/A')}<br>
        Monitoring Count: ${escapeHtml(p.monitoring_count || 0)}<br>
        Participants: ${escapeHtml(p.participants || 0)}<br>
        <b>ESFI: ${escapeHtml(esfi)}</b><br>
        <b>Priority Score: ${escapeHtml(recommendation.score)}</b><br>
        ${escapeHtml(recommendation.action)}
    `);

    return marker;
}

function createMunicipalityMarker(area) {
    if (area.latitude === null || area.longitude === null) return null;
    if (!municipalityIsNotTaken(area)) return null;
    const recommendation = municipalityRecommendationFor(area);

    const marker = L.marker([parseFloat(area.latitude), parseFloat(area.longitude)], {
        icon: priorityPinIcon(recommendation),
        zIndexOffset: 1000
    });

    marker.bindTooltip(`
        <b>${escapeHtml(area.municipality)}</b><br>
        Province: ${escapeHtml(area.province || 'Eastern Visayas')}<br>
        Availability: ${municipalityIsNotTaken(area) ? 'Not taken / no project' : 'Has recorded project'}<br>
        Projects: ${escapeHtml(area.project_count)}<br>
        Municipal ESFI: ${escapeHtml(area.average_esfi)}<br>
        Need: ${escapeHtml(recommendation.need.label)}<br>
        Recommendation Score: <b>${escapeHtml(recommendation.score)}</b><br>
        ${escapeHtml(recommendation.action)}
    `, {
        direction: 'top',
        sticky: true,
        opacity: 0.95
    });

    marker.bindPopup(`
        <b>${escapeHtml(area.municipality)}</b><br>
        Province: ${escapeHtml(area.province || 'Eastern Visayas')}<br>
        Availability: ${municipalityIsNotTaken(area) ? 'Not taken / no project' : 'Has recorded project'}<br>
        Recorded Projects: ${escapeHtml(area.project_count)}<br>
        Monitoring Count: ${escapeHtml(area.monitoring_count)}<br>
        Participants: ${escapeHtml(area.participants)}<br>
        Latest Monitoring: ${escapeHtml(area.latest_monitoring_date || 'N/A')}<br>
        Community Need: ${escapeHtml(recommendation.need.label)}<br>
        Suggested Program: ${escapeHtml(recommendation.need.program)}<br>
        <b>Need Score: ${escapeHtml(recommendation.score)}</b><br>
        ${escapeHtml(recommendation.action)}
    `);

    return marker;
}

function updateMapLayers(items, municipalityItems = []) {
    markerLayer.clearLayers();
    coverageLayer.clearLayers();
    markerByProjectId.clear();
    markerByMunicipalityId.clear();

    if (heatLayer && map.hasLayer(heatLayer)) map.removeLayer(heatLayer);
    heatLayer = createHeatLayer(items);
    if (heatmapVisible) heatLayer.addTo(map);

    items.forEach(p => {
        const coverageBox = createCoverageBox(p);
        if (coverageBox) {
            coverageLayer.addLayer(coverageBox);
        }

        const marker = createMarker(p);
        if (marker) {
            markerLayer.addLayer(marker);
            markerByProjectId.set(String(p.id || ''), marker);
        }
    });

    if (municipalityRecommenderMarkersVisible) {
        municipalityItems.forEach(area => {
            const marker = createMunicipalityMarker(area);
            if (marker) {
                markerLayer.addLayer(marker);
                markerByMunicipalityId.set(area.id, marker);
            }
        });
    }

    if (coverageVisible && !map.hasLayer(coverageLayer)) coverageLayer.addTo(map);
    if (!coverageVisible && map.hasLayer(coverageLayer)) map.removeLayer(coverageLayer);
    if (markersVisible && !map.hasLayer(markerLayer)) markerLayer.addTo(map);
    if (!markersVisible && map.hasLayer(markerLayer)) map.removeLayer(markerLayer);
}

function rankedRecommendations(projectItems, municipalityItems = []) {
    const municipalityRecommendations = municipalityItems
        .filter(municipalityIsNotTaken)
        .map(area => ({ kind: 'municipality', area, recommendation: municipalityRecommendationFor(area) }));

    return municipalityRecommendations
        .sort((a, b) => {
            if (b.recommendation.score !== a.recommendation.score) {
                return b.recommendation.score - a.recommendation.score;
            }
            return String(a.area.municipality || '').localeCompare(String(b.area.municipality || ''));
        });
}

function renderRecommendations(items, municipalityItems = []) {
    const list = document.getElementById('recommendationList');
    const count = document.getElementById('recommendationCount');
    const summary = document.getElementById('mapRecommendationSummary');
    if (!list) return;

    const ranked = rankedRecommendations(items, municipalityItems);
    if (count) count.textContent = ranked.length;
    if (summary) {
        const notTaken = municipalityItems.filter(municipalityIsNotTaken).length;
        const selectedNeed = selectedNeedKey();
        const focus = selectedNeed ? `; ${needCategories[selectedNeed].label} focus` : '';
        const source = osmMunicipalitiesLoading
            ? '; loading map data'
            : (osmMunicipalitiesFailed ? '; map data unavailable' : '');
        summary.textContent = `${notTaken} not-taken municipalit${notTaken === 1 ? 'y' : 'ies'} ready for project matching${focus}${source}`;
    }

    if (!ranked.length) {
        list.innerHTML = '<p class="text-muted small mb-0">No not-taken municipalities match the filters.</p>';
        queueRecommenderHeightSync();
        return;
    }

    list.innerHTML = ranked.map(item => {
        const { recommendation } = item;
        const title = item.area.municipality;
        const subtitle = `${item.area.province || 'Eastern Visayas'} municipality`;
        const attrs = `data-municipality-id="${escapeHtml(item.area.id)}"`;

        return `
        <button type="button" class="map-recommendation-item" ${attrs} style="--priority-color:${notTakenMunicipalityColor}">
            <span class="map-recommendation-score">${escapeHtml(recommendation.score)}%</span>
            <span class="map-recommendation-body">
                <small>Not Taken Municipality</small>
                <strong>Not Taken: ${escapeHtml(title)}</strong>
                <span><b>Need:</b> ${escapeHtml(recommendation.need.label)}</span>
                <span><b>Suggested Program:</b> ${escapeHtml(recommendation.need.program)}</span>
                <span><b>Reason:</b> ${escapeHtml(recommendation.reasons.slice(0, 3).join(', '))}</span>
                <em>${escapeHtml(subtitle)}</em>
            </span>
        </button>
    `;
    }).join('');
    queueRecommenderHeightSync();
}

function updateRankingPriorityBadges() {
    document.querySelectorAll('.js-map-row').forEach(row => {
        const id = row.dataset.projectId || '';
        const project = projectById.get(id);
        const priorityCell = row.querySelector('.js-map-priority');

        if (project && priorityCell) {
            const recommendation = recommendationFor(project);
            priorityCell.innerHTML = `<span class="badge" style="background:${priorityColor(recommendation)}">${escapeHtml(recommendation.score)} ${escapeHtml(recommendation.level)}</span>`;
        }
    });
}

function resetFormulaResult() {
    const formulaCard = document.getElementById('esfiFormulaCard');
    const formulaResult = document.getElementById('esfiFormulaResult');

    if (formulaCard) formulaCard.classList.remove('is-formula-active');
    if (!formulaResult) return;

    formulaResult.innerHTML = '<p class="small text-muted mb-0">Hover a project in the ESFI Ranking to view its computed ESFI result.</p>';
}

function showFormulaResult(projectId) {
    const key = String(projectId || '');
    const project = projectById.get(key);
    const formulaCard = document.getElementById('esfiFormulaCard');
    const formulaResult = document.getElementById('esfiFormulaResult');
    if (!project || !formulaResult) return;

    activeFormulaProjectId = key;
    if (formulaCard) formulaCard.classList.add('is-formula-active');

    const parts = esfiFormulaParts(project);
    const computedEsfi = getESFI(project);
    const interpretation = project.esfi_label || esfiLabel(computedEsfi);
    const recommendation = recommendationFor(project);

    formulaResult.innerHTML = `
        <div class="map-formula-project">${escapeHtml(getProjectTitle(project))}</div>
        <div class="map-formula-equation">
            <span>(${formatCount(parts.monitoring)} x 0.70) + (${formatCount(parts.participants)} / 100 x 0.30)</span>
            <strong>= ${escapeHtml(formatScore(computedEsfi))}</strong>
        </div>
        <div class="map-formula-metrics">
            <div class="map-formula-metric">
                <small>Monitoring part</small>
                <strong>${escapeHtml(formatScore(parts.monitoringScore))}</strong>
            </div>
            <div class="map-formula-metric">
                <small>Participant part</small>
                <strong>${escapeHtml(formatScore(parts.participantScore))}</strong>
            </div>
            <div class="map-formula-metric">
                <small>Priority</small>
                <strong>${escapeHtml(recommendation.score)} ${escapeHtml(recommendation.level)}</strong>
            </div>
        </div>
        <p class="small mb-0 map-formula-interpretation"><b>Interpretation:</b> ${escapeHtml(interpretation)}</p>
    `;
}

function clearFormulaResult(projectId) {
    const key = String(projectId || '');
    if (activeFormulaProjectId && activeFormulaProjectId !== key) return;

    activeFormulaProjectId = null;
    resetFormulaResult();
}

function updateCounts(items, municipalityItems = []) {
    const resultCount = document.getElementById('mapResultCount');
    if (!resultCount) return;
    const mapped = items.filter(p => getCoordinates(p)).length;
    resultCount.textContent = `${items.length} project record${items.length === 1 ? '' : 's'} shown, ${mapped} mapped; ${municipalityItems.length} municipal area${municipalityItems.length === 1 ? '' : 's'} analyzed`;
}

function updateMunicipalityRecommenderToggle() {
    const toggle = document.getElementById('mapRecommenderToggle');

    if (toggle) {
        toggle.textContent = municipalityRecommenderMarkersVisible ? 'Hide Recommender Markers' : 'Show Recommender Markers';
        toggle.setAttribute('aria-pressed', municipalityRecommenderMarkersVisible ? 'true' : 'false');
        toggle.classList.toggle('active', municipalityRecommenderMarkersVisible);
    }
}

function applyFilters() {
    populateMunicipalityOptions();
    const filteredProjects = getFilteredProjects();
    const filteredMunicipalities = getFilteredMunicipalityAreas();
    updateMapLayers(filteredProjects, filteredMunicipalities);
    renderRecommendations(filteredProjects, filteredMunicipalities);
    updateMunicipalityRecommenderToggle();
    updateRankingPriorityBadges();
    updateCounts(filteredProjects, filteredMunicipalities);
}

function clearFilters() {
    Object.values(filterControls).forEach(control => {
        if (control) control.value = '';
    });
    populateMunicipalityOptions();
    applyFilters();
}

function focusProject(projectId) {
    const key = String(projectId || '');
    const project = projectById.get(key);
    if (!project) return;

    const coordinates = getCoordinates(project);
    if (coordinates) map.setView(coordinates, 11);

    const marker = markerByProjectId.get(key);
    if (marker) marker.openPopup();
}

function focusMunicipality(municipalityId) {
    const area = municipalityAreas.find(item => item.id === municipalityId);
    if (!area) return;

    if (area.latitude !== null && area.longitude !== null) {
        map.setView([parseFloat(area.latitude), parseFloat(area.longitude)], 10);
    }

    const marker = markerByMunicipalityId.get(municipalityId);
    if (marker) marker.openPopup();
}

function toggleHeatmap() {
    heatmapVisible = !heatmapVisible;
    if (heatLayer) {
        if (heatmapVisible) heatLayer.addTo(map);
        else map.removeLayer(heatLayer);
    }
}

function toggleMarkers() {
    markersVisible = !markersVisible;
    if (markersVisible) markerLayer.addTo(map);
    else map.removeLayer(markerLayer);
}

function toggleCoverage() {
    coverageVisible = !coverageVisible;
    if (coverageVisible) coverageLayer.addTo(map);
    else map.removeLayer(coverageLayer);
}

function toggleMunicipalityRecommender() {
    municipalityRecommenderMarkersVisible = !municipalityRecommenderMarkersVisible;
    applyFilters();
}

function resetMap() {
    heatmapVisible = true;
    markersVisible = true;
    coverageVisible = true;
    municipalityRecommenderMarkersVisible = true;
    clearFilters();
    map.setView(defaultCenter, defaultZoom);
}

function bindFilterEvents() {
    Object.entries(filterControls).forEach(([key, control]) => {
        if (!control) return;
        const eventName = key === 'search' ? 'input' : 'change';
        control.addEventListener(eventName, applyFilters);
    });

    window.addEventListener('resize', queueRecommenderHeightSync);

    document.getElementById('mapClearFilters')?.addEventListener('click', clearFilters);
    document.getElementById('recommendationList')?.addEventListener('click', event => {
        const item = event.target.closest('[data-project-id], [data-municipality-id]');
        if (!item) return;
        if (item.dataset.municipalityId) focusMunicipality(item.dataset.municipalityId);
        else focusProject(item.dataset.projectId);
    });

    const ranking = document.getElementById('esfiRanking');
    const rankingRowFromEvent = event => {
        return event.target && typeof event.target.closest === 'function'
            ? event.target.closest('.js-map-row')
            : null;
    };

    const showFormulaFromRow = event => {
        const row = rankingRowFromEvent(event);
        if (!row || !ranking?.contains(row)) return;
        showFormulaResult(row.dataset.projectId);
    };

    const clearFormulaFromRow = event => {
        const row = rankingRowFromEvent(event);
        if (!row || !ranking?.contains(row) || (event.relatedTarget && row.contains(event.relatedTarget))) return;
        clearFormulaResult(row.dataset.projectId);
    };

    ranking?.addEventListener('pointerover', showFormulaFromRow);
    ranking?.addEventListener('mouseover', showFormulaFromRow);
    ranking?.addEventListener('pointerout', clearFormulaFromRow);
    ranking?.addEventListener('mouseout', clearFormulaFromRow);
    ranking?.addEventListener('focusin', showFormulaFromRow);
    ranking?.addEventListener('focusout', clearFormulaFromRow);
    ranking?.addEventListener('click', showFormulaFromRow);
}

populateFilters();
bindFilterEvents();
applyFilters();
resetFormulaResult();
if (allMunicipalityLocations.length < 40) {
    loadMapMunicipalitiesFromOsm();
}
