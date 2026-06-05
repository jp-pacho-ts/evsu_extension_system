<?php
require_once 'app/models/Project.php';
require_once 'app/models/QuarterlyReport.php';

class DashboardController {
    private $projectModel;
    private $quarterlyReportModel;

    function __construct($db) {
        $this->projectModel = new Project($db);
        $this->quarterlyReportModel = new QuarterlyReport($db);
    }

    function index() {
        requireRole(['Super Admin','Admin','School Coordinator','Campus Director','Extension Director','VP ORIES','Reviewer']);

        $projects = $this->projectModel->all();
        $statusCounts = [];
        $programCounts = [];
        $municipalityCounts = [];

        foreach($projects as $p) {
            $statusCounts[$p['status']] = ($statusCounts[$p['status']] ?? 0) + 1;
            $programCounts[$p['program_title']] = ($programCounts[$p['program_title']] ?? 0) + 1;
            $municipalityCounts[$p['municipality']] = ($municipalityCounts[$p['municipality']] ?? 0) + 1;
        }

        $total = count($projects);
        $quarterlyPhaseRows = $this->quarterlyReportModel->phaseSummary();
        $quarterlyPhaseCounts = [];

        foreach(range(1, 7) as $phase) {
            $quarterlyPhaseCounts[(string)$phase] = 0;
        }

        foreach($quarterlyPhaseRows as $row) {
            $phase = trim((string)($row['project_phase'] ?? ''));
            if(preg_match('/\d+/', $phase, $matches)) $phase = $matches[0];
            if($phase !== '') $quarterlyPhaseCounts[$phase] = intval($row['total'] ?? 0);
        }

        $quarterlyPhaseItems = $this->quarterlyReportModel->latestPhaseItems(8);

        include 'app/views/dashboard/index.php';
    }
}
?>
