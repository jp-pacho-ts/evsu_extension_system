<?php
require_once "app/models/QuarterlyReport.php";

class ApprovalCenterController {
    private $reportModel;

    public function __construct($db) {
        $this->reportModel = new QuarterlyReport($db);
    }

    public function index() {
        requireLogin();

        $currentUser = [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role']
        ];

        $reports = [];
        if(hasRole(['School Coordinator','Campus Director','VP ORIES','Super Admin','Admin'])) {
            $reports = $this->reportModel->approvalQueueForUser($currentUser);
        }

        include "app/views/approval_center/index.php";
    }
}
?>