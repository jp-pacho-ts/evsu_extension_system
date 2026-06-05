<?php
require_once "app/models/QuarterlyReport.php";
class ApprovalController {
    private $reportModel;
    public function __construct($db){ $this->reportModel=new QuarterlyReport($db); }
    public function index(){ requireRole(['Department Coordinator','School Coordinator','Campus Director','VP ORIES']); $currentUser=['id'=>$_SESSION['user_id'],'role'=>$_SESSION['role']]; $reports=$this->reportModel->approvalQueueForUser($currentUser); include "app/views/approvals/index.php"; }
    public function action(){ requireRole(['Department Coordinator','School Coordinator','Campus Director','VP ORIES']); $id=intval($_POST['report_id']); $action=$_POST['approval_action']; $remarks=$_POST['approval_remarks']??''; $currentUser=['id'=>$_SESSION['user_id'],'role'=>$_SESSION['role']]; if($action=='approve')$this->reportModel->approveAsCurrentUser($id,$currentUser,$remarks); elseif($action=='not_approve')$this->reportModel->notApproveAsCurrentUser($id,$currentUser,$remarks); header("Location: index.php?page=approvals"); exit(); }
}
?>
