<?php
class QuarterlyReport {
    private $conn;
    public function __construct($db){ $this->conn=$db; $this->ensureApprovalStatusEnums(); $this->ensureOwnershipColumn(); }

    private function ensureApprovalStatusEnums(){
        @$this->conn->query("ALTER TABLE quarterly_reports MODIFY submission_status enum('Draft','Submitted','Under Review','Recalled','For Revision','Not Approved','Department Coordinator Approved','School Coordinator Approved','Campus Director Approved','Extension Office Approved','VP ORIES Approved','Approved','Archived') DEFAULT 'Draft'");
        @$this->conn->query("ALTER TABLE document_approvals MODIFY status enum('Pending','Approved','Not Approved','For Revision','Recalled') DEFAULT 'Pending'");
    }

    private function ensureOwnershipColumn(){
        $rs = @$this->conn->query("SHOW COLUMNS FROM quarterly_reports LIKE 'created_by'");
        if($rs && $rs->num_rows > 0) return true;
        @$this->conn->query("ALTER TABLE quarterly_reports ADD COLUMN created_by int(11) DEFAULT NULL AFTER approved_title");
        $rs = @$this->conn->query("SHOW COLUMNS FROM quarterly_reports LIKE 'created_by'");
        return $rs && $rs->num_rows > 0;
    }

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

    public function phaseSummary(){
        $sql = "SELECT qri.project_phase, COUNT(*) AS total
                FROM quarterly_report_items qri
                JOIN quarterly_reports qr ON qr.id = qri.report_id
                WHERE TRIM(COALESCE(qri.title_of_extension_project, '')) <> ''
                GROUP BY qri.project_phase
                ORDER BY CAST(qri.project_phase AS UNSIGNED), qri.project_phase";
        $r = $this->conn->query($sql);
        $d = [];
        if($r) while($x = $r->fetch_assoc()) $d[] = $x;
        return $d;
    }

    public function latestPhaseItems($limit = 8){
        $limit = intval($limit);
        if($limit <= 0) $limit = 8;

        $sql = "SELECT
                    qri.*,
                    qr.period_covered,
                    qr.submission_status,
                    qr.report_date,
                    qr.updated_at,
                    qr.submitted_at,
                    qr.created_at
                FROM quarterly_report_items qri
                JOIN quarterly_reports qr ON qr.id = qri.report_id
                WHERE TRIM(COALESCE(qri.title_of_extension_project, '')) <> ''
                ORDER BY COALESCE(qr.updated_at, qr.submitted_at, qr.report_date, qr.created_at) DESC, qri.id DESC
                LIMIT $limit";
        $r = $this->conn->query($sql);
        $d = [];
        if($r) while($x = $r->fetch_assoc()) $d[] = $x;
        return $d;
    }

    public function canEdit($report){ return in_array($report['submission_status']??'Draft',['Draft','Recalled','For Revision','Not Approved']); }
    public function canRecall($report){ return in_array($report['submission_status']??'Draft',['Submitted','Under Review']); }
    public function canDelete($report){ return in_array($report['submission_status']??'Draft',['Draft','Recalled','For Revision','Not Approved']); }

    private function esc($v){ return $this->conn->real_escape_string($v ?? ''); }

    private function saveItems($id,$items){
        $id=intval($id);
        foreach($items as $item){
            if(trim($item['title_of_extension_project']??'')==='') continue;
            foreach($item as $k=>$v) $item[$k]=$this->esc($v);
            $this->conn->query("INSERT INTO quarterly_report_items(report_id,title_of_extension_project,proponents,date_conducted,location,source_of_fund,total_project_cost,project_phase) VALUES($id,'{$item['title_of_extension_project']}','{$item['proponents']}','{$item['date_conducted']}','{$item['location']}','{$item['source_of_fund']}','{$item['total_project_cost']}','{$item['project_phase']}')");
        }
    }

    private function reportLink($id) {
        return 'index.php?page=view_quarterly_report&id='.intval($id);
    }

    private function reportLabel($report) {
        $period = trim((string)($report['period_covered'] ?? ''));
        $department = trim((string)($report['department'] ?? ''));
        $parts = [];
        if($period !== '') $parts[] = $period;
        if($department !== '') $parts[] = $department;
        return $parts ? implode(' - ', $parts) : 'Quarterly report #'.intval($report['id'] ?? 0);
    }

    private function projectPhaseLabel($id) {
        $id = intval($id);
        $result = $this->conn->query("SELECT DISTINCT project_phase FROM quarterly_report_items WHERE report_id=$id AND TRIM(COALESCE(project_phase,''))<>''");
        $phases = [];
        if($result) {
            while($row = $result->fetch_assoc()) {
                $phase = trim((string)($row['project_phase'] ?? ''));
                if($phase === '') continue;
                $number = preg_replace('/[^0-9]/', '', $phase);
                $key = $number !== '' ? intval($number) : $phase;
                $label = stripos($phase, 'phase') === 0 ? $phase : 'Phase '.$phase;
                $phases[$key] = $label;
            }
        }

        if(empty($phases)) return 'No phase indicated';
        ksort($phases);
        return count($phases) === 1 ? reset($phases) : implode(', ', array_values($phases));
    }

    private function approvalRoleLabel($role) {
        $role = function_exists('normalizeRole') ? normalizeRole($role) : strtolower(trim((string)$role));
        if($role == 'department coordinator') return 'Department Coordinator';
        if($role == 'school coordinator') return 'School Coordinator';
        if($role == 'campus director') return 'Campus Director / Dean';
        if($role == 'vp ories') return 'VP ORIES';
        return ucwords($role);
    }

    private function nextApproverRoleForStatus($status) {
        if($status == 'Department Coordinator Approved') return 'School Coordinator';
        if($status == 'School Coordinator Approved') return 'Campus Director';
        if($status == 'Campus Director Approved') return 'VP ORIES';
        return '';
    }

    private function notifyReportOwners($report, $title, $message, $excludeUserId = 0) {
        $ids = [];
        foreach(['created_by','submitted_by'] as $key) {
            $uid = intval($report[$key] ?? 0);
            if($uid > 0 && $uid !== intval($excludeUserId)) $ids[$uid] = true;
        }

        foreach(array_keys($ids) as $uid) {
            notifyUser($this->conn, $uid, $title, $message, $this->reportLink($report['id'] ?? 0));
        }
    }

    private function notifyInitialApprovalNeeded($id, $title) {
        $report = $this->find($id);
        if(!$report) return;
        $label = $this->reportLabel($report);
        $phase = $this->projectPhaseLabel($id);
        notifyUsersByRole(
            $this->conn,
            ['Department Coordinator'],
            $title,
            "$label is waiting for Department Coordinator approval. Current project phase: $phase.",
            'index.php?page=approval_center'
        );
    }

    public function create($data,$items){
        foreach($data as $k=>$v) if(!is_array($v)) $data[$k]=$this->esc($v);
        $status=($data['save_action']??'draft')=='submit'?'Submitted':'Draft';
        $uid=isset($_SESSION['user_id'])?intval($_SESSION['user_id']):0;
        $submittedBy=$status=='Submitted'?$uid:"NULL";
        $submittedAt=$status=='Submitted'?"NOW()":"NULL";
        $ok=$this->conn->query("INSERT INTO quarterly_reports(college,campus,department,period_covered,control_no,revision_no,report_date,prepared_by,prepared_title,noted_by_dean,noted_by_dean_title,noted_by_extension_director,noted_by_extension_director_title,approved_by,approved_title,created_by,submission_status,submitted_by,submitted_at) VALUES('{$data['college']}','{$data['campus']}','{$data['department']}','{$data['period_covered']}','{$data['control_no']}','{$data['revision_no']}','{$data['report_date']}','{$data['prepared_by']}','{$data['prepared_title']}','{$data['noted_by_dean']}','{$data['noted_by_dean_title']}','{$data['noted_by_extension_director']}','{$data['noted_by_extension_director_title']}','{$data['approved_by']}','{$data['approved_title']}',$uid,'$status',$submittedBy,$submittedAt)");
        if(!$ok) return false;
        $id=$this->conn->insert_id;
        $this->saveItems($id,$items);
        if($status=='Submitted') {
            $this->resetApprovalRoute($id);
            $this->addApprovalHistory($id, 'Submitted', 'Draft/Recalled/For Revision/Not Approved', 'Submitted', 'Report submitted for approval.');
            $this->notifyInitialApprovalNeeded($id, 'New Quarterly Report Submitted');
        }
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
            $this->resetApprovalRoute($id);
            $this->addApprovalHistory($id, 'Resubmitted', $report['submission_status'] ?? 'Draft', 'Submitted', 'Report resubmitted for approval.');
            $this->notifyInitialApprovalNeeded($id, 'Quarterly Report Resubmitted');
        }
        return true;
    }

    public function submit($id,$uid){
        $id=intval($id); $uid=intval($uid); $report=$this->find($id);
        if(!$report || !in_array($report['submission_status'],['Draft','Recalled','For Revision','Not Approved'])) return false;
        $this->conn->query("UPDATE quarterly_reports SET submission_status='Submitted',submitted_by=$uid,submitted_at=NOW(),recalled_by=NULL,recalled_at=NULL WHERE id=$id");
        $this->resetApprovalRoute($id);
        $this->addApprovalHistory($id, 'Submitted', 'Draft/Recalled/For Revision/Not Approved', 'Submitted', 'Report submitted for approval.');
        $this->notifyInitialApprovalNeeded($id, 'New Quarterly Report Submitted');
        return true;
    }

    public function recall($id,$uid){
        $id=intval($id); $uid=intval($uid); $report=$this->find($id);
        if(!$report || !$this->canRecall($report)) return false;
        $this->conn->query("UPDATE quarterly_reports SET submission_status='Recalled',recalled_by=$uid,recalled_at=NOW(),approval_remarks='Submission recalled by coordinator for correction.' WHERE id=$id");
        $this->conn->query("DELETE FROM document_approvals WHERE document_type='quarterly_report' AND document_id=$id");
        notifyUsersByRole($this->conn,['School Coordinator'],'Quarterly Report Recalled','A submitted quarterly report was recalled by the coordinator.','index.php?page=approval_center');
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

        $label = $this->reportLabel($report);
        $phase = $this->projectPhaseLabel($id);
        $roleLabel = $this->approvalRoleLabel($role);
        $nextRole = $this->nextApproverRoleForStatus($next);
        $nextText = $nextRole ? ' It is now waiting for '.$this->approvalRoleLabel($nextRole).' approval.' : ' Final status: Approved.';
        $remarksText = $remarks !== '' ? ' Remarks: '.$remarks : '';

        $this->notifyReportOwners(
            $report,
            $next == 'Approved' ? 'Quarterly Report Fully Approved' : 'Quarterly Report Approved',
            "$label was approved by $approverName ($roleLabel). Current status: $next. Current project phase: $phase.$nextText$remarksText",
            $uid
        );

        if($nextRole) {
            notifyUsersByRole(
                $this->conn,
                [$nextRole],
                'Report Ready for Approval',
                "$label was approved by $roleLabel and is now ready for your approval. Current project phase: $phase. Current status: $next.",
                'index.php?page=approval_center'
            );
        }

        return true;
    }
    public function notApproveAsCurrentUser($id, $user, $remarks=''){
        $id = intval($id);
        $remarks = $this->conn->real_escape_string($remarks);
        $report = $this->find($id);
        if(!$report) return false;
        $old = $report['submission_status'] ?? '';

        $role = $user['role'] ?? '';
        $required = $this->requiredStatusForRole($role);
        $level = $this->approvalLevelForRole($role);

        if($required == '' || $level == 0) return false;
        if($old != $required) return false;

        $uid = intval($user['id'] ?? $_SESSION['user_id'] ?? 0);
        $approver = $this->conn->query("SELECT fullname, signature_image FROM users WHERE id=$uid")->fetch_assoc();
        $approverName = $this->conn->real_escape_string($approver['fullname'] ?? '');
        $approverSignature = $this->conn->real_escape_string($approver['signature_image'] ?? '');

        $this->ensureApprovalRoute($id);
        $this->conn->query("UPDATE document_approvals
            SET status='Not Approved', approver_user_id=$uid, approver_name='$approverName', approver_signature_image='$approverSignature', remarks='$remarks', signed_at=NOW()
            WHERE document_type='quarterly_report' AND document_id=$id AND approval_level=$level");

        $this->conn->query("UPDATE quarterly_reports SET submission_status='Not Approved', approval_remarks='$remarks' WHERE id=$id");
        $this->addApprovalHistory($id, 'Not Approved', $old, 'Not Approved', $remarks);

        $label = $this->reportLabel($report);
        $phase = $this->projectPhaseLabel($id);
        $roleLabel = $this->approvalRoleLabel($role);
        $remarksText = $remarks !== '' ? ' Remarks: '.$remarks : '';
        $this->notifyReportOwners(
            $report,
            'Quarterly Report Not Approved',
            "$label was not approved by $approverName ($roleLabel). Current project phase: $phase.$remarksText",
            $uid
        );
        return true;
    }
    public function returnForRevision($id, $user, $remarks=''){
        return $this->notApproveAsCurrentUser($id, $user, $remarks);
    }
    private function statusAfterApprovalRole($role){ 
        if($role=='Department Coordinator') return 'Department Coordinator Approved'; 
        if($role=='School Coordinator') return 'School Coordinator Approved'; 
        if($role=='Campus Director') return 'Campus Director Approved'; 
        if($role=='VP ORIES') return 'Approved'; 
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
        if($exists && $exists->num_rows > 0) {
            $routeCheck = $this->conn->query("SELECT approval_role FROM document_approvals WHERE document_type='quarterly_report' AND document_id=$id AND approval_level=1 LIMIT 1");
            $route = $routeCheck ? $routeCheck->fetch_assoc() : null;
            if(($route['approval_role'] ?? '') == 'Department Coordinator') return;

            $this->conn->query("DELETE FROM document_approvals WHERE document_type='quarterly_report' AND document_id=$id");
        }

        $route = [
            [1,'Department Coordinator'],
            [2,'School Coordinator'],
            [3,'Campus Director / Dean'],
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
        $role = function_exists('normalizeRole') ? normalizeRole($role) : strtolower(trim((string)$role));
        if($role == 'department coordinator') return 'Submitted';
        if($role == 'school coordinator') return 'Department Coordinator Approved';
        if($role == 'campus director') return 'School Coordinator Approved';
        if($role == 'vp ories') return 'Campus Director Approved';
        return '';
    }

    public function nextStatusForRole($role) {
        $role = function_exists('normalizeRole') ? normalizeRole($role) : strtolower(trim((string)$role));
        if($role == 'department coordinator') return 'Department Coordinator Approved';
        if($role == 'school coordinator') return 'School Coordinator Approved';
        if($role == 'campus director') return 'Campus Director Approved';
        if($role == 'vp ories') return 'Approved';
        return '';
    }

    public function approvalLevelForRole($role) {
        $role = function_exists('normalizeRole') ? normalizeRole($role) : strtolower(trim((string)$role));
        if($role == 'department coordinator') return 1;
        if($role == 'school coordinator') return 2;
        if($role == 'campus director') return 3;
        if($role == 'vp ories') return 4;
        return 0;
    }
}
?>
