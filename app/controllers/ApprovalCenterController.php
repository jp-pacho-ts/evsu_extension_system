<?php
require_once "app/models/QuarterlyReport.php";

class ApprovalCenterController {
    private $reportModel;

    public function __construct($db) {
        $this->reportModel = new QuarterlyReport($db);
    }

    public function index() {
        requireAccess(canAccessApprovalCenter());

        $currentUser = [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role']
        ];

        $reports = [];
        $reports = $this->reportModel->approvalQueueForUser($currentUser);

        include "app/views/approval_center/index.php";
    }
}
?>
