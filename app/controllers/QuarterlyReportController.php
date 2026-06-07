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
        'Admin',
        'Faculty'
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
        'Faculty',
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

    private function canManageReports($report = null) {
        if(hasRole($this->manageRoles)) return true;
        if(!hasRole(['Faculty']) || !userHasExtensionServices($this->db)) return false;
        if($report === null) return true;

        $uid = intval($_SESSION['user_id'] ?? 0);
        if($uid <= 0) return false;

        return intval($report['created_by'] ?? 0) === $uid || intval($report['submitted_by'] ?? 0) === $uid;
    }

    private function denyAccess() {
        include "app/views/access_denied.php";
        exit();
    }

    private function currentUserProfile() {
        $uid = intval($_SESSION['user_id'] ?? 0);
        if($uid <= 0) return [];

        $result = @$this->db->query("SELECT * FROM users WHERE id=$uid LIMIT 1");
        if(!$result) return [];
        return $result->fetch_assoc() ?: [];
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

        $currentUserProfile = $this->currentUserProfile();
        $reports = $this->model->all();
        $reportPermissions = [];
        foreach($reports as $report) {
            $reportPermissions[$report['id']] = $this->canManageReports($report);
        }
        include "app/views/quarterly_reports/index.php";
    }

    public function edit() {
        requireLogin();

        $id = intval($_GET['id'] ?? 0);
        $report = $this->model->find($id);
        $items = $this->model->items($id);

        if(!$report || !$this->canManageReports($report) || !$this->model->canEdit($report)) $this->denyAccess();

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
        $canManageQuarterlyReports = $this->canManageReports($report);

        include "app/views/quarterly_reports/show.php";
    }

    public function submit() {
        requireLogin();

        $id = intval($_GET['id'] ?? 0);
        $report = $this->model->find($id);
        if(!$report || !$this->canManageReports($report)) $this->denyAccess();

        if($this->model->submit($id, $_SESSION['user_id'])) {
            logActivity($this->db, 'Submit Quarterly Report', 'Quarterly Report', 'Submitted report ID: '.$id);
        }
        header('Location: index.php?page=view_quarterly_report&id='.$id);
        exit();
    }

    public function recall() {
        requireLogin();

        $id = intval($_GET['id'] ?? 0);
        $report = $this->model->find($id);
        if(!$report || !$this->canManageReports($report)) $this->denyAccess();

        if($this->model->recall($id, $_SESSION['user_id'])) {
            logActivity($this->db, 'Recall Submission', 'Quarterly Report', 'Recalled report ID: '.$id);
        }
        header('Location: index.php?page=edit_quarterly_report&id='.$id);
        exit();
    }

    public function delete() {
        requireLogin();

        $id = intval($_GET['id'] ?? 0);
        $report = $this->model->find($id);
        if(!$report || !$this->canManageReports($report)) $this->denyAccess();

        if($this->model->delete($id)) {
            logActivity($this->db, 'Delete Quarterly Report', 'Quarterly Report', 'Deleted report ID: '.$id);
        }
        header('Location: index.php?page=quarterly_reports');
        exit();
    }
}
?>
