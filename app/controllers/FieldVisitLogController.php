<?php
require_once "app/models/FieldVisitLog.php";

class FieldVisitLogController {
    private $db;
    private $model;
    private $manageRoles=['Department Coordinator','Extension Staff','School Coordinator','Super Admin','Admin'];

    public function __construct($db) { $this->db=$db; $this->model=new FieldVisitLog($db); }

    private function canManage($report=null) {
        if(hasRole($this->manageRoles)) return true;
        if(!hasRole(['Faculty']) || !userHasExtensionServices($this->db)) return false;
        if($report===null) return true;
        return intval($report['created_by'] ?? 0)===intval($_SESSION['user_id'] ?? 0);
    }

    private function deny() { include "app/views/access_denied.php"; exit(); }
    private function profile() {
        $uid=intval($_SESSION['user_id'] ?? 0); $result=$uid>0?@$this->db->query("SELECT * FROM users WHERE id=$uid LIMIT 1"):false;
        return $result?($result->fetch_assoc() ?: []):[];
    }

    private function collectItems() {
        $keys=['objectives','activities','visit_date','place','time_start','time_end','expected_parameter','expected_target','person_contacted','contact_position','results','observations','issues','action_points','comments'];
        $count=0; foreach($keys as $key) $count=max($count,count($_POST[$key] ?? []));
        $items=[];
        for($i=0;$i<$count;$i++) { $row=[]; foreach($keys as $key) $row[$key]=$_POST[$key][$i] ?? ''; $items[]=$row; }
        return $items;
    }

    private function hasValidItems($items) {
        foreach($items as $item) if(trim((string)($item['activities'] ?? ''))!=='' && trim((string)($item['visit_date'] ?? ''))!=='') return true;
        return false;
    }

    public function index() {
        requireAccess(canAccessExtensionForms()); $canCreate=$this->canManage();
        $pagination=paginationParams($this->model->countAll(),10); $logs=$this->model->paginated($pagination['per_page'],$pagination['offset']);
        $permissions=[]; foreach($logs as $log) $permissions[$log['id']]=$this->canManage($log);
        include "app/views/field_visit_logs/index.php";
    }

    public function create() {
        requireAccess(canAccessExtensionForms()); if(!$this->canManage()) $this->deny();
        $message=''; $profile=$this->profile(); $projects=$this->model->projectOptions(); $report=[]; $items=[];
        if($_SERVER['REQUEST_METHOD']==='POST') {
            $submittedItems=$this->collectItems();
            if(intval($_POST['project_id'] ?? 0)<=0 || !$this->hasValidItems($submittedItems)) { $items=$submittedItems; $message='Select a project and add at least one dated field visit activity.'; include "app/views/field_visit_logs/create.php"; return; }
            $id=$this->model->create($_POST,$submittedItems,$_SESSION['user_id']);
            if($id) { logActivity($this->db,'Create Field Visit Log','Field Visit Log','Created log ID: '.$id); header('Location: index.php?page=view_field_visit_log&id='.$id); exit(); }
            $message='Unable to save the field visit log.';
        }
        include "app/views/field_visit_logs/create.php";
    }

    public function edit() {
        requireAccess(canAccessExtensionForms()); $id=intval($_GET['id'] ?? 0); $report=$this->model->find($id);
        if(!$report || !$this->canManage($report) || !$this->model->canEdit($report)) $this->deny();
        $items=$this->model->items($id); $projects=$this->model->projectOptions(); $profile=$this->profile(); $message='';
        if($_SERVER['REQUEST_METHOD']==='POST') {
            $submittedItems=$this->collectItems();
            if(intval($_POST['project_id'] ?? 0)<=0 || !$this->hasValidItems($submittedItems)) { $items=$submittedItems; $message='Select a project and add at least one dated field visit activity.'; include "app/views/field_visit_logs/edit.php"; return; }
            if($this->model->update($id,$_POST,$submittedItems,$_SESSION['user_id'])) { logActivity($this->db,'Update Field Visit Log','Field Visit Log','Updated log ID: '.$id); header('Location: index.php?page=view_field_visit_log&id='.$id); exit(); }
            $message='Unable to update the field visit log.';
        }
        include "app/views/field_visit_logs/edit.php";
    }

    public function show() {
        requireAccess(canAccessExtensionForms()); $id=intval($_GET['id'] ?? 0); $report=$this->model->find($id); if(!$report) $this->deny();
        $items=$this->model->items($id); $approvals=$this->model->approvals($id); $canManage=$this->canManage($report);
        include "app/views/field_visit_logs/show.php";
    }

    public function submit() { $this->workflowAction('submit'); }
    public function recall() { $this->workflowAction('recall'); }
    public function delete() { $this->workflowAction('delete'); }

    private function workflowAction($action) {
        requireAccess(canAccessExtensionForms());
        if($_SERVER['REQUEST_METHOD']!=='POST') { header('Location: index.php?page=field_visit_logs'); exit(); }
        $id=intval($_POST['id'] ?? 0); $report=$this->model->find($id); if(!$report || !$this->canManage($report)) $this->deny();
        $ok=false;
        if($action==='submit') $ok=$this->model->submit($id,$_SESSION['user_id']);
        elseif($action==='recall') $ok=$this->model->recall($id,$_SESSION['user_id']);
        elseif($action==='delete') $ok=$this->model->delete($id);
        if($ok) logActivity($this->db,ucfirst($action).' Field Visit Log','Field Visit Log',ucfirst($action).' log ID: '.$id);
        $target=$action==='delete'?'index.php?page=field_visit_logs':'index.php?page=view_field_visit_log&id='.$id;
        header('Location: '.$target.($ok?'&success='.$action:'&error=1')); exit();
    }
}
?>
