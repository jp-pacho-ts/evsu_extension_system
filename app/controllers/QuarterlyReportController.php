<?php
require_once "app/models/QuarterlyReport.php";

class QuarterlyReportController {
    private $model;
    private $db;

    private $listRoles = [
        'Department Coordinator',
        'Extension Staff',
        'School Coordinator',
        'Campus Director',
        'Extension Director',
        'VP ORIES',
        'Super Admin',
        'Admin'
    ];

    private $showRoles = [
        'Department Coordinator',
        'Extension Staff',
        'School Coordinator',
        'Campus Director',
        'Extension Director',
        'VP ORIES',
        'Super Admin',
        'Admin',
        'Reviewer'
    ];

    private $manageRoles = [
        'Department Coordinator',
        'Extension Staff',
        'School Coordinator',
        'Super Admin'
    ];

    public function __construct($db) {
        $this->db = $db;
        $this->model = new QuarterlyReport($db);
    }

    private function canManageReports() {
        return hasRole($this->manageRoles);
    }

    private function collectItems() {
        $items = [];
        $count = count($_POST['title_of_extension_project'] ?? []);

        for($i = 0; $i < $count; $i++) {
            $items[] = [
                'title_of_extension_project' => $_POST['title_of_extension_project'][$i],
                'proponents' => $_POST['proponents'][$i],
                'date_conducted' => $_POST['date_conducted'][$i],
                'location' => $_POST['location'][$i],
                'source_of_fund' => $_POST['source_of_fund'][$i],
                'total_project_cost' => $_POST['total_project_cost'][$i],
                'project_phase' => $_POST['project_phase'][$i]
            ];
        }

        return $items;
    }

    public function index() {
        requireRole($this->listRoles);

        $message = '';
        $canManageQuarterlyReports = $this->canManageReports();

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(!$canManageQuarterlyReports) {
                include "app/views/access_denied.php";
                exit();
            }

            $id = $this->model->create($_POST, $this->collectItems());
            if($id) {
                logActivity($this->db, 'Create Quarterly Report', 'Quarterly Report', 'Created report ID: '.$id);
                header('Location: index.php?page=view_quarterly_report&id='.$id);
                exit();
            }

            $message = 'Unable to save quarterly report.';
        }

        $reports = $this->model->all();
        include "app/views/quarterly_reports/index.php";
    }

    public function edit() {
        requireRole($this->manageRoles);

        $id = intval($_GET['id']);
        $report = $this->model->find($id);
        $items = $this->model->items($id);

        if(!$report || !$this->model->canEdit($report)) {
            include "app/views/access_denied.php";
            exit();
        }

        $message = '';
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($this->model->update($id, $_POST, $this->collectItems())) {
                logActivity($this->db, 'Update Quarterly Report', 'Quarterly Report', 'Updated report ID: '.$id);
                header('Location: index.php?page=view_quarterly_report&id='.$id);
                exit();
            }

            $message = 'Unable to update report.';
        }

        include "app/views/quarterly_reports/edit.php";
    }

    public function show() {
        requireRole($this->showRoles);

        $id = intval($_GET['id']);
        $report = $this->model->find($id);
        if(!$report) {
            include "app/views/access_denied.php";
            exit();
        }

        $items = $this->model->items($id);
        $approvals = $this->model->approvals($id);
        $canManageQuarterlyReports = $this->canManageReports();

        include "app/views/quarterly_reports/show.php";
    }

    public function submit() {
        requireRole($this->manageRoles);

        $id = intval($_GET['id']);
        $this->model->submit($id, $_SESSION['user_id']);
        logActivity($this->db, 'Submit Quarterly Report', 'Quarterly Report', 'Submitted report ID: '.$id);
        header('Location: index.php?page=view_quarterly_report&id='.$id);
        exit();
    }

    public function recall() {
        requireRole($this->manageRoles);

        $id = intval($_GET['id']);
        $this->model->recall($id, $_SESSION['user_id']);
        logActivity($this->db, 'Recall Submission', 'Quarterly Report', 'Recalled report ID: '.$id);
        header('Location: index.php?page=edit_quarterly_report&id='.$id);
        exit();
    }

    public function delete() {
        requireRole($this->manageRoles);

        $id = intval($_GET['id']);
        $this->model->delete($id);
        logActivity($this->db, 'Delete Quarterly Report', 'Quarterly Report', 'Deleted report ID: '.$id);
        header('Location: index.php?page=quarterly_reports');
        exit();
    }
}
?>
