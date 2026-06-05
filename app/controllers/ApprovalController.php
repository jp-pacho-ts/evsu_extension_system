<?php
require_once "app/models/QuarterlyReport.php";
class ApprovalController {
    private $reportModel;
    public function __construct($db){ $this->reportModel=new QuarterlyReport($db); }
    public function index(){ requireRole(['School Coordinator','Campus Director','VP ORIES','Super Admin','Admin']); $currentUser=['id'=>$_SESSION['user_id'],'role'=>$_SESSION['role']]; $reports=$this->reportModel->approvalQueueForUser($currentUser); include "app/views/approvals/index.php"; }
    public function action(){ requireRole(['School Coordinator','Campus Director','VP ORIES','Super Admin','Admin']); $id=intval($_POST['report_id']); $action=$_POST['approval_action']; $remarks=$_POST['approval_remarks']??''; $currentUser=['id'=>$_SESSION['user_id'],'role'=>$_SESSION['role']]; if($action=='approve')$this->reportModel->approveAsCurrentUser($id,$currentUser,$remarks); elseif($action=='revise')$this->reportModel->returnForRevision($id,$currentUser,$remarks); elseif($action=='archive')$this->reportModel->archive($id,$currentUser,$remarks); header("Location: index.php?page=approvals"); exit(); }
}
?>
