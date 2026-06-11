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
        requireRole(['Super Admin','Admin','Extension Director','Reviewer']);

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
        $quarterlyReportItems = $this->quarterlyReportModel->latestPhaseItems(8);

        include 'app/views/dashboard/index.php';
    }
}
?>
