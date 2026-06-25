<?php
require_once "app/models/QuarterlyAccomplishment.php";

class QuarterlyAccomplishmentController {
    private $db;
    private $model;
    private $manageRoles = ['Department Coordinator','Extension Staff','School Coordinator','Super Admin','Admin'];

    public function __construct($db) {
        $this->db=$db;
        $this->model=new QuarterlyAccomplishment($db);
    }

    private function canManage($report=null) {
        if(hasRole($this->manageRoles)) return true;
        if(!hasRole(['Faculty']) || !userHasExtensionServices($this->db)) return false;
        if($report===null) return true;
        return intval($report['created_by'] ?? 0)===intval($_SESSION['user_id'] ?? 0);
    }

    private function deny() { include "app/views/access_denied.php"; exit(); }

    private function profile() {
        $uid=intval($_SESSION['user_id'] ?? 0);
        $result=$uid>0?@$this->db->query("SELECT * FROM users WHERE id=$uid LIMIT 1"):false;
        return $result?($result->fetch_assoc() ?: []):[];
    }

    private function collectItems() {
        $keys=['project_id','inclusive_date_start','inclusive_date_end','activity_title','beneficiary_type','male_count','female_count','quality_rating','duration_hours','service_type','partner_agency','faculty_staff_involved','students_involved','nature_of_participation','project_cost','funding_source'];
        $count=0; foreach($keys as $key) $count=max($count,count($_POST[$key] ?? []));
        $items=[];
        for($i=0;$i<$count;$i++) {
            $row=[]; foreach($keys as $key) $row[$key]=$_POST[$key][$i] ?? '';
            $items[]=$row;
        }
        return $items;
    }

    private function hasValidItems($items) {
        foreach($items as $item) if(intval($item['project_id'] ?? 0)>0 && trim((string)($item['activity_title'] ?? ''))!=='') return true;
        return false;
    }

    public function index() {
        requireAccess(canAccessExtensionForms());
        $canCreate=$this->canManage();
        $pagination=paginationParams($this->model->countAll(),10);
        $reports=$this->model->paginated($pagination['per_page'],$pagination['offset']);
        $permissions=[]; foreach($reports as $report) $permissions[$report['id']]=$this->canManage($report);
        include "app/views/quarterly_accomplishments/index.php";
    }

    public function create() {
        requireAccess(canAccessExtensionForms());
        if(!$this->canManage()) $this->deny();
        $message=''; $profile=$this->profile(); $projects=$this->model->projectOptions(); $report=[]; $items=[];
        if($_SERVER['REQUEST_METHOD']==='POST') {
            $submittedItems=$this->collectItems();
            if(!$this->hasValidItems($submittedItems)) { $items=$submittedItems; $message='Add at least one activity with a selected project.'; include "app/views/quarterly_accomplishments/create.php"; return; }
            $id=$this->model->create($_POST,$submittedItems,$_SESSION['user_id']);
            if($id) {
                logActivity($this->db,'Create Quarterly Accomplishment','Quarterly Accomplishment','Created report ID: '.$id);
                header('Location: index.php?page=view_quarterly_accomplishment&id='.$id); exit();
            }
            $message='Unable to save the quarterly accomplishment report.';
        }
        include "app/views/quarterly_accomplishments/create.php";
    }

    public function edit() {
        requireAccess(canAccessExtensionForms());
        $id=intval($_GET['id'] ?? 0); $report=$this->model->find($id);
        if(!$report || !$this->canManage($report) || !$this->model->canEdit($report)) $this->deny();
        $items=$this->model->items($id); $projects=$this->model->projectOptions(); $profile=$this->profile(); $message='';
        if($_SERVER['REQUEST_METHOD']==='POST') {
            $submittedItems=$this->collectItems();
            if(!$this->hasValidItems($submittedItems)) { $items=$submittedItems; $message='Add at least one activity with a selected project.'; include "app/views/quarterly_accomplishments/edit.php"; return; }
            if($this->model->update($id,$_POST,$submittedItems,$_SESSION['user_id'])) {
                logActivity($this->db,'Update Quarterly Accomplishment','Quarterly Accomplishment','Updated report ID: '.$id);
                header('Location: index.php?page=view_quarterly_accomplishment&id='.$id); exit();
            }
            $message='Unable to update the quarterly accomplishment report.';
        }
        include "app/views/quarterly_accomplishments/edit.php";
    }

    public function show() {
        requireAccess(canAccessExtensionForms());
        $id=intval($_GET['id'] ?? 0); $report=$this->model->find($id);
        if(!$report) $this->deny();
        $items=$this->model->items($id); $approvals=$this->model->approvals($id); $canManage=$this->canManage($report);
        include "app/views/quarterly_accomplishments/show.php";
    }

    public function submit() { $this->workflowAction('submit'); }
    public function recall() { $this->workflowAction('recall'); }
    public function delete() { $this->workflowAction('delete'); }

    private function workflowAction($action) {
        requireAccess(canAccessExtensionForms());
        if($_SERVER['REQUEST_METHOD']!=='POST') { header('Location: index.php?page=quarterly_accomplishments'); exit(); }
        $id=intval($_POST['id'] ?? 0); $report=$this->model->find($id);
        if(!$report || !$this->canManage($report)) $this->deny();
        $ok=false;
        if($action==='submit') $ok=$this->model->submit($id,$_SESSION['user_id']);
        elseif($action==='recall') $ok=$this->model->recall($id,$_SESSION['user_id']);
        elseif($action==='delete') $ok=$this->model->delete($id);
        if($ok) logActivity($this->db,ucfirst($action).' Quarterly Accomplishment','Quarterly Accomplishment',ucfirst($action).' report ID: '.$id);
        $target=$action==='delete'?'index.php?page=quarterly_accomplishments':'index.php?page=view_quarterly_accomplishment&id='.$id;
        header('Location: '.$target.($ok?'&success='.$action:'&error=1')); exit();
    }
}
?>
