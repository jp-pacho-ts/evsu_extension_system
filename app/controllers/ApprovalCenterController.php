<?php
require_once "app/models/QuarterlyReport.php";
require_once "app/models/QuarterlyAccomplishment.php";
require_once "app/models/FieldVisitLog.php";

class ApprovalCenterController {
    private $reportModel;
    private $accomplishmentModel;
    private $fieldVisitModel;

    public function __construct($db) {
        $this->reportModel = new QuarterlyReport($db);
        $this->accomplishmentModel = new QuarterlyAccomplishment($db);
        $this->fieldVisitModel = new FieldVisitLog($db);
    }

    public function index() {
        requireAccess(canAccessApprovalCenter());

        $currentUser = [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role']
        ];

        $reports = $this->reportModel->approvalQueueForUser($currentUser);
        foreach($reports as &$report) {
            $report['document_type'] = 'quarterly_report';
            $report['document_label'] = 'Quarterly Monitoring';
        }
        unset($report);
        $reports = array_merge(
            $reports,
            $this->accomplishmentModel->approvalQueueForUser($currentUser),
            $this->fieldVisitModel->approvalQueueForUser($currentUser)
        );

        include "app/views/approval_center/index.php";
    }
}
?>
