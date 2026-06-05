<?php
class QuarterlyReport {
    private $conn;
    public function __construct($db){ $this->conn=$db; }

    public function all(){
        $r=$this->conn->query("SELECT qr.*,u.fullname submitted_by_name FROM quarterly_reports qr LEFT JOIN users u ON u.id=qr.submitted_by ORDER BY qr.created_at DESC");
        $d=[]; if($r) while($x=$r->fetch_assoc()) $d[]=$x; return $d;
    }

    public function find($id){
        $id=intval($id);
        return $this->conn->query("SELECT qr.*,u.fullname submitted_by_name FROM quarterly_reports qr LEFT JOIN users u ON u.id=qr.submitted_by WHERE qr.id=$id")->fetch_assoc();
    }

    public function items($id){
        $id=intval($id); $r=$this->conn->query("SELECT * FROM quarterly_report_items WHERE report_id=$id ORDER BY id ASC");
        $d=[]; if($r) while($x=$r->fetch_assoc()) $d[]=$x; return $d;
    }

    public function approvals($id){
        $id=intval($id);
        $r=$this->conn->query("SELECT da.*,u.fullname,u.signatory_title FROM document_approvals da LEFT JOIN users u ON u.id=da.approver_user_id WHERE da.document_type='quarterly_report' AND da.document_id=$id ORDER BY da.approval_level ASC");
        $d=[]; if($r) while($x=$r->fetch_assoc()) $d[]=$x; return $d;
    }

    public function canEdit($report){ return in_array($report['submission_status']??'Draft',['Draft','Recalled','For Revision']); }
    public function canRecall($report){ return in_array($report['submission_status']??'Draft',['Submitted','Under Review']); }
    public function canDelete($report){ return in_array($report['submission_status']??'Draft',['Draft','Recalled','For Revision']); }

    private function esc($v){ return $this->conn->real_escape_string($v ?? ''); }

    private function saveItems($id,$items){
        $id=intval($id);
        foreach($items as $item){
            if(trim($item['title_of_extension_project']??'')==='') continue;
            foreach($item as $k=>$v) $item[$k]=$this->esc($v);
            $this->conn->query("INSERT INTO quarterly_report_items(report_id,title_of_extension_project,proponents,date_conducted,location,source_of_fund,total_project_cost,project_phase) VALUES($id,'{$item['title_of_extension_project']}','{$item['proponents']}','{$item['date_conducted']}','{$item['location']}','{$item['source_of_fund']}','{$item['total_project_cost']}','{$item['project_phase']}')");
        }
    }

    public function create($data,$items){
        foreach($data as $k=>$v) if(!is_array($v)) $data[$k]=$this->esc($v);
        $status=($data['save_action']??'draft')=='submit'?'Submitted':'Draft';
        $uid=isset($_SESSION['user_id'])?intval($_SESSION['user_id']):0;
        $submittedBy=$status=='Submitted'?$uid:"NULL";
        $submittedAt=$status=='Submitted'?"NOW()":"NULL";
        $ok=$this->conn->query("INSERT INTO quarterly_reports(college,campus,department,period_covered,control_no,revision_no,report_date,prepared_by,prepared_title,noted_by_dean,noted_by_dean_title,noted_by_extension_director,noted_by_extension_director_title,approved_by,approved_title,submission_status,submitted_by,submitted_at) VALUES('{$data['college']}','{$data['campus']}','{$data['department']}','{$data['period_covered']}','{$data['control_no']}','{$data['revision_no']}','{$data['report_date']}','{$data['prepared_by']}','{$data['prepared_title']}','{$data['noted_by_dean']}','{$data['noted_by_dean_title']}','{$data['noted_by_extension_director']}','{$data['noted_by_extension_director_title']}','{$data['approved_by']}','{$data['approved_title']}','$status',$submittedBy,$submittedAt)");
        if(!$ok) return false;
        $id=$this->conn->insert_id;
        $this->saveItems($id,$items);
        if($status=='Submitted') $this->resetApprovalRoute($id);
        $this->addApprovalHistory($id, 'Submitted', 'Draft/Recalled/For Revision', 'Submitted', 'Report submitted for approval.');
        notifyUsersByRole($this->conn,['School Coordinator'],'New Quarterly Report Submitted','A quarterly report is waiting for your approval.','index.php?page=approval_center');
        return $id;
    }

    public function update($id,$data,$items){
        $id=intval($id); $report=$this->find($id); if(!$report || !$this->canEdit($report)) return false;
        foreach($data as $k=>$v) if(!is_array($v)) $data[$k]=$this->esc($v);
        $uid=isset($_SESSION['user_id'])?intval($_SESSION['user_id']):0;
        $status=($data['save_action']??'draft')=='submit'?'Submitted':($report['submission_status']??'Draft');
        $submitSql=$status=='Submitted'?", submitted_by=$uid, submitted_at=NOW(), recalled_by=NULL, recalled_at=NULL":"";
        $ok=$this->conn->query("UPDATE quarterly_reports SET college='{$data['college']}',campus='{$data['campus']}',department='{$data['department']}',period_covered='{$data['period_covered']}',control_no='{$data['control_no']}',revision_no='{$data['revision_no']}',report_date='{$data['report_date']}',prepared_by='{$data['prepared_by']}',prepared_title='{$data['prepared_title']}',noted_by_dean='{$data['noted_by_dean']}',noted_by_dean_title='{$data['noted_by_dean_title']}',noted_by_extension_director='{$data['noted_by_extension_director']}',noted_by_extension_director_title='{$data['noted_by_extension_director_title']}',approved_by='{$data['approved_by']}',approved_title='{$data['approved_title']}',submission_status='$status',updated_by=$uid,updated_at=NOW(),revision_notes='{$data['revision_notes']}' $submitSql WHERE id=$id");
        if(!$ok) return false;
        $this->conn->query("DELETE FROM quarterly_report_items WHERE report_id=$id");
        $this->saveItems($id,$items);
        if($status=='Submitted'){
            $this->conn->query("DELETE FROM document_approvals WHERE document_type='quarterly_report' AND document_id=$id");
            notifyUsersByRole($this->conn,['School Coordinator'],'Quarterly Report Resubmitted','A corrected quarterly report is waiting for your approval.','index.php?page=approvals');
        }
        return true;
    }

    public function submit($id,$uid){
        $id=intval($id); $uid=intval($uid); $report=$this->find($id);
        if(!$report || !in_array($report['submission_status'],['Draft','Recalled','For Revision'])) return false;
        $this->conn->query("UPDATE quarterly_reports SET submission_status='Submitted',submitted_by=$uid,submitted_at=NOW(),recalled_by=NULL,recalled_at=NULL WHERE id=$id");
        $this->resetApprovalRoute($id);
        $this->addApprovalHistory($id, 'Submitted', 'Draft/Recalled/For Revision', 'Submitted', 'Report submitted for approval.');
        notifyUsersByRole($this->conn,['School Coordinator'],'New Quarterly Report Submitted','A quarterly report is waiting for your approval.','index.php?page=approval_center');
        return true;
    }

    public function recall($id,$uid){
        $id=intval($id); $uid=intval($uid); $report=$this->find($id);
        if(!$report || !$this->canRecall($report)) return false;
        $this->conn->query("UPDATE quarterly_reports SET submission_status='Recalled',recalled_by=$uid,recalled_at=NOW(),approval_remarks='Submission recalled by coordinator for correction.' WHERE id=$id");
        $this->conn->query("DELETE FROM document_approvals WHERE document_type='quarterly_report' AND document_id=$id");
        notifyUsersByRole($this->conn,['School Coordinator'],'Quarterly Report Recalled','A submitted quarterly report was recalled by the coordinator.','index.php?page=approvals');
        return true;
    }

    public function delete($id){
        $id=intval($id); $report=$this->find($id); if(!$report) return false;
        if(!$this->canDelete($report) && !hasRole(['Super Admin'])) return false;
        $this->conn->query("DELETE FROM quarterly_report_items WHERE report_id=$id");
        $this->conn->query("DELETE FROM document_approvals WHERE document_type='quarterly_report' AND document_id=$id");
        return $this->conn->query("DELETE FROM quarterly_reports WHERE id=$id");
    }

    public function approvalQueueForUser($user){
        $role = $this->conn->real_escape_string($user['role'] ?? '');
        $expected = $this->requiredStatusForRole($role);
        if($expected == '') return [];

        $expected = $this->conn->real_escape_string($expected);
        $sql = "SELECT qr.*, u.fullname AS submitted_by_name
                FROM quarterly_reports qr
                LEFT JOIN users u ON u.id = qr.submitted_by
                WHERE qr.submission_status='$expected'
                ORDER BY qr.submitted_at DESC, qr.created_at DESC";
        $result = $this->conn->query($sql);
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function approveAsCurrentUser($id, $user, $remarks=''){
        $id = intval($id);
        $remarks = $this->conn->real_escape_string($remarks);
        $report = $this->find($id);
        if(!$report) return false;

        $role = $user['role'] ?? '';
        $required = $this->requiredStatusForRole($role);
        $next = $this->nextStatusForRole($role);
        $level = $this->approvalLevelForRole($role);

        // Sequential locking enforcement
        if($required == '' || $next == '' || $level == 0) return false;
        if(($report['submission_status'] ?? '') != $required) return false;

        $uid = intval($user['id'] ?? $_SESSION['user_id'] ?? 0);
        $approver = $this->conn->query("SELECT fullname, signature_image FROM users WHERE id=$uid")->fetch_assoc();
        $approverName = $this->conn->real_escape_string($approver['fullname'] ?? '');
        $approverSignature = $this->conn->real_escape_string($approver['signature_image'] ?? '');

        $this->ensureApprovalRoute($id);
        $this->conn->query("UPDATE document_approvals
            SET status='Approved', approver_user_id=$uid, approver_name='$approverName', approver_signature_image='$approverSignature', remarks='$remarks', signed_at=NOW()
            WHERE document_type='quarterly_report' AND document_id=$id AND approval_level=$level");

        $this->conn->query("UPDATE quarterly_reports SET submission_status='$next', approval_remarks='$remarks' WHERE id=$id");
        $this->addApprovalHistory($id, 'Approved', $required, $next, $remarks);

        if($next == 'School Coordinator Approved') notifyUsersByRole($this->conn, ['Campus Director'], 'Report Ready for Approval', 'A report is ready for Campus Director/Dean approval.', 'index.php?page=approval_center');
        if($next == 'Campus Director Approved') notifyUsersByRole($this->conn, ['Super Admin','Admin'], 'Report Ready for Extension Office Approval', 'A report is ready for Extension Office approval.', 'index.php?page=approval_center');
        if($next == 'Extension Office Approved') notifyUsersByRole($this->conn, ['VP ORIES'], 'Report Ready for Final Approval', 'A report is ready for VP ORIES final approval.', 'index.php?page=approval_center');

        return true;
    }
    public function returnForRevision($id, $user, $remarks=''){
        $id = intval($id);
        $remarks = $this->conn->real_escape_string($remarks);
        $report = $this->find($id);
        if(!$report) return false;
        $old = $report['submission_status'] ?? '';

        $this->conn->query("UPDATE quarterly_reports SET submission_status='For Revision', approval_remarks='$remarks' WHERE id=$id");
        $this->addApprovalHistory($id, 'Returned for Revision', $old, 'For Revision', $remarks);
        return true;
    }
    private function statusAfterApprovalRole($role){ 
        if($role=='School Coordinator') return 'School Coordinator Approved'; 
        if($role=='Campus Director') return 'Campus Director Approved'; 
        if($role=='Super Admin' || $role=='Admin') return 'Extension Office Approved'; 
        if($role=='VP ORIES') return 'VP ORIES Approved'; 
        return 'Under Review'; 
    }

    public function addApprovalHistory($id, $action, $previous, $new, $remarks='') {
        $id = intval($id);
        $uid = intval($_SESSION['user_id'] ?? 0);
        $role = $this->conn->real_escape_string($_SESSION['role'] ?? '');
        $action = $this->conn->real_escape_string($action);
        $previous = $this->conn->real_escape_string($previous);
        $new = $this->conn->real_escape_string($new);
        $remarks = $this->conn->real_escape_string($remarks);
        return $this->conn->query("INSERT INTO approval_history(document_type, document_id, action_by, action_role, action_taken, previous_status, new_status, remarks)
            VALUES('quarterly_report',$id,$uid,'$role','$action','$previous','$new','$remarks')");
    }

    public function history($id) {
        $id = intval($id);
        $result = $this->conn->query("SELECT ah.*, u.fullname, u.username
            FROM approval_history ah
            LEFT JOIN users u ON u.id = ah.action_by
            WHERE ah.document_type='quarterly_report' AND ah.document_id=$id
            ORDER BY ah.created_at ASC, ah.id ASC");
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function ensureApprovalRoute($id) {
        $id = intval($id);
        $exists = $this->conn->query("SELECT id FROM document_approvals WHERE document_type='quarterly_report' AND document_id=$id LIMIT 1");
        if($exists && $exists->num_rows > 0) return;

        $route = [
            [1,'School Coordinator'],
            [2,'Campus Director / Dean'],
            [3,'Extension Office'],
            [4,'VP ORIES']
        ];

        foreach($route as $r) {
            $level = intval($r[0]);
            $role = $this->conn->real_escape_string($r[1]);
            $this->conn->query("INSERT INTO document_approvals(document_type, document_id, approval_level, approval_role, status)
                VALUES('quarterly_report',$id,$level,'$role','Pending')");
        }
    }

    public function resetApprovalRoute($id) {
        $id = intval($id);
        $this->conn->query("DELETE FROM document_approvals WHERE document_type='quarterly_report' AND document_id=$id");
        $this->ensureApprovalRoute($id);
    }

    public function requiredStatusForRole($role) {
        if($role == 'School Coordinator') return 'Submitted';
        if($role == 'Campus Director') return 'School Coordinator Approved';
        if($role == 'Super Admin' || $role == 'Admin') return 'Campus Director Approved';
        if($role == 'VP ORIES') return 'Extension Office Approved';
        return '';
    }

    public function nextStatusForRole($role) {
        if($role == 'School Coordinator') return 'School Coordinator Approved';
        if($role == 'Campus Director') return 'Campus Director Approved';
        if($role == 'Super Admin' || $role == 'Admin') return 'Extension Office Approved';
        if($role == 'VP ORIES') return 'VP ORIES Approved';
        return '';
    }

    public function approvalLevelForRole($role) {
        if($role == 'School Coordinator') return 1;
        if($role == 'Campus Director') return 2;
        if($role == 'Super Admin' || $role == 'Admin') return 3;
        if($role == 'VP ORIES') return 4;
        return 0;
    }
}
?>
