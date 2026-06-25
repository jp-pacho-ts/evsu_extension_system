<?php
require_once "app/models/QuarterlyReport.php";
require_once "app/models/QuarterlyAccomplishment.php";
require_once "app/models/FieldVisitLog.php";
class ApprovalController {
    private $reportModel;
    private $accomplishmentModel;
    private $fieldVisitModel;
    public function __construct($db){ $this->reportModel=new QuarterlyReport($db); $this->accomplishmentModel=new QuarterlyAccomplishment($db); $this->fieldVisitModel=new FieldVisitLog($db); }
    public function index(){ requireAccess(canAccessApprovalCenter()); $currentUser=['id'=>$_SESSION['user_id'],'role'=>$_SESSION['role']]; $reports=$this->reportModel->approvalQueueForUser($currentUser); include "app/views/approvals/index.php"; }
    public function action(){ requireAccess(canAccessApprovalCenter()); $id=intval($_POST['report_id']); $action=$_POST['approval_action']; $remarks=$_POST['approval_remarks']??''; $currentUser=['id'=>$_SESSION['user_id'],'role'=>$_SESSION['role']]; $type=$_POST['document_type']??'quarterly_report'; $model=$type==='quarterly_accomplishment'?$this->accomplishmentModel:($type==='field_visit_log'?$this->fieldVisitModel:$this->reportModel); if($action=='approve')$model->approveAsCurrentUser($id,$currentUser,$remarks); elseif($action=='not_approve')$model->notApproveAsCurrentUser($id,$currentUser,$remarks); header("Location: index.php?page=approval_center"); exit(); }
}
?>
