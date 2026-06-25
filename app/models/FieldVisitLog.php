<?php
class FieldVisitLog {
    private $conn;
    private $documentType = 'field_visit_log';

    public function __construct($db) {
        $this->conn = $db;
        $this->ensureSchema();
    }

    private function ensureSchema() {
        @$this->conn->query("CREATE TABLE IF NOT EXISTS field_visit_logs (
            id int(11) NOT NULL AUTO_INCREMENT,
            program_id int(11) DEFAULT NULL,
            project_id int(11) DEFAULT NULL,
            quarter tinyint(1) NOT NULL DEFAULT 1,
            report_year smallint(6) NOT NULL,
            duration_start date DEFAULT NULL,
            duration_end date DEFAULT NULL,
            implementing_unit varchar(255) DEFAULT NULL,
            campus varchar(150) DEFAULT NULL,
            control_no varchar(100) DEFAULT 'EVSU-ORDE&S-F-076',
            revision_no varchar(50) DEFAULT '00',
            form_date date DEFAULT NULL,
            prepared_by varchar(180) DEFAULT NULL,
            prepared_title varchar(180) DEFAULT NULL,
            noted_by varchar(180) DEFAULT NULL,
            noted_title varchar(180) DEFAULT NULL,
            attested_by varchar(180) DEFAULT NULL,
            attested_title varchar(180) DEFAULT NULL,
            approved_by varchar(180) DEFAULT NULL,
            approved_title varchar(180) DEFAULT NULL,
            created_by int(11) DEFAULT NULL,
            submission_status varchar(80) NOT NULL DEFAULT 'Draft',
            submitted_by int(11) DEFAULT NULL,
            submitted_at datetime DEFAULT NULL,
            recalled_by int(11) DEFAULT NULL,
            recalled_at datetime DEFAULT NULL,
            updated_by int(11) DEFAULT NULL,
            updated_at datetime DEFAULT NULL,
            revision_notes text DEFAULT NULL,
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id),
            KEY project_id (project_id),
            KEY created_by (created_by),
            KEY reporting_period (report_year, quarter)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        @$this->conn->query("CREATE TABLE IF NOT EXISTS field_visit_items (
            id int(11) NOT NULL AUTO_INCREMENT,
            log_id int(11) NOT NULL,
            objectives text DEFAULT NULL,
            activities text DEFAULT NULL,
            visit_date date DEFAULT NULL,
            place varchar(255) DEFAULT NULL,
            time_start time DEFAULT NULL,
            time_end time DEFAULT NULL,
            expected_parameter text DEFAULT NULL,
            expected_target text DEFAULT NULL,
            person_contacted varchar(180) DEFAULT NULL,
            contact_position varchar(180) DEFAULT NULL,
            results text DEFAULT NULL,
            observations text DEFAULT NULL,
            issues text DEFAULT NULL,
            action_points text DEFAULT NULL,
            comments text DEFAULT NULL,
            PRIMARY KEY (id),
            KEY log_id (log_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    private function esc($value) { return $this->conn->real_escape_string((string)($value ?? '')); }

    public function projectOptions() {
        $sql = "SELECT p.id, p.project_title, p.program_id, p.start_date, p.end_date, pr.program_title
                FROM projects p JOIN programs pr ON pr.id=p.program_id
                ORDER BY pr.program_title, p.project_title";
        $result=$this->conn->query($sql); $data=[];
        if($result) while($row=$result->fetch_assoc()) $data[]=$row;
        return $data;
    }

    public function countAll() {
        $result=$this->conn->query("SELECT COUNT(*) total FROM field_visit_logs");
        return $result ? intval($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function paginated($limit,$offset) {
        $limit=max(1,intval($limit)); $offset=max(0,intval($offset));
        $sql="SELECT l.*,p.project_title,pr.program_title,u.fullname created_by_name,
            (SELECT COUNT(*) FROM field_visit_items i WHERE i.log_id=l.id) item_count
            FROM field_visit_logs l
            LEFT JOIN projects p ON p.id=l.project_id
            LEFT JOIN programs pr ON pr.id=l.program_id
            LEFT JOIN users u ON u.id=l.created_by
            ORDER BY l.report_year DESC,l.quarter DESC,l.created_at DESC LIMIT $limit OFFSET $offset";
        $result=$this->conn->query($sql); $data=[];
        if($result) while($row=$result->fetch_assoc()) $data[]=$row;
        return $data;
    }

    public function find($id) {
        $id=intval($id);
        $result=$this->conn->query("SELECT l.*,p.project_title,pr.program_title,u.fullname created_by_name
            FROM field_visit_logs l
            LEFT JOIN projects p ON p.id=l.project_id
            LEFT JOIN programs pr ON pr.id=l.program_id
            LEFT JOIN users u ON u.id=l.created_by WHERE l.id=$id LIMIT 1");
        return $result ? ($result->fetch_assoc() ?: null) : null;
    }

    public function items($id) {
        $id=intval($id); $result=$this->conn->query("SELECT * FROM field_visit_items WHERE log_id=$id ORDER BY visit_date,id");
        $data=[]; if($result) while($row=$result->fetch_assoc()) $data[]=$row; return $data;
    }

    public function approvals($id) {
        $id=intval($id); $type=$this->esc($this->documentType);
        $result=$this->conn->query("SELECT da.*,u.fullname,u.signatory_title FROM document_approvals da LEFT JOIN users u ON u.id=da.approver_user_id WHERE da.document_type='$type' AND da.document_id=$id ORDER BY da.approval_level");
        $data=[]; if($result) while($row=$result->fetch_assoc()) $data[]=$row; return $data;
    }

    public function canEdit($report) { return in_array($report['submission_status'] ?? 'Draft',['Draft','Recalled','For Revision','Not Approved'],true); }
    public function canRecall($report) { return in_array($report['submission_status'] ?? '',['Submitted','Under Review'],true); }
    public function canDelete($report) { return $this->canEdit($report); }

    private function headerValues($data) {
        $keys=['implementing_unit','campus','control_no','revision_no','form_date','duration_start','duration_end','prepared_by','prepared_title','noted_by','noted_title','attested_by','attested_title','approved_by','approved_title'];
        $values=[]; foreach($keys as $key) $values[$key]=$this->esc($data[$key] ?? '');
        $values['project_id']=intval($data['project_id'] ?? 0);
        $values['program_id']=0;
        if($values['project_id']>0) {
            $project=$this->conn->query("SELECT program_id FROM projects WHERE id={$values['project_id']} LIMIT 1");
            $project=$project?$project->fetch_assoc():null;
            $values['program_id']=intval($project['program_id'] ?? 0);
        }
        $values['quarter']=min(4,max(1,intval($data['quarter'] ?? 1)));
        $values['report_year']=min(2100,max(2000,intval($data['report_year'] ?? date('Y'))));
        return $values;
    }

    private function saveItems($logId,$items) {
        $logId=intval($logId);
        foreach($items as $item) {
            $activities=trim((string)($item['activities'] ?? ''));
            $date=trim((string)($item['visit_date'] ?? ''));
            if($activities==='' && $date==='') continue;
            $objectives=$this->esc($item['objectives'] ?? '');
            $activities=$this->esc($activities);
            $visitDate=$this->esc($date);
            $place=$this->esc($item['place'] ?? '');
            $start=$this->esc($item['time_start'] ?? '');
            $end=$this->esc($item['time_end'] ?? '');
            $parameter=$this->esc($item['expected_parameter'] ?? '');
            $target=$this->esc($item['expected_target'] ?? '');
            $person=$this->esc($item['person_contacted'] ?? '');
            $position=$this->esc($item['contact_position'] ?? '');
            $results=$this->esc($item['results'] ?? '');
            $observations=$this->esc($item['observations'] ?? '');
            $issues=$this->esc($item['issues'] ?? '');
            $actions=$this->esc($item['action_points'] ?? '');
            $comments=$this->esc($item['comments'] ?? '');
            $sql="INSERT INTO field_visit_items(log_id,objectives,activities,visit_date,place,time_start,time_end,expected_parameter,expected_target,person_contacted,contact_position,results,observations,issues,action_points,comments)
                VALUES($logId,'$objectives','$activities',NULLIF('$visitDate',''),'$place',NULLIF('$start',''),NULLIF('$end',''),'$parameter','$target','$person','$position','$results','$observations','$issues','$actions','$comments')";
            if(!$this->conn->query($sql)) return false;
        }
        return true;
    }

    public function create($data,$items,$userId) {
        $userId=intval($userId); $v=$this->headerValues($data);
        $status=($data['save_action'] ?? 'draft')==='submit'?'Submitted':'Draft';
        $submittedBy=$status==='Submitted'?$userId:'NULL'; $submittedAt=$status==='Submitted'?'NOW()':'NULL';
        $this->conn->begin_transaction();
        $sql="INSERT INTO field_visit_logs(program_id,project_id,quarter,report_year,duration_start,duration_end,implementing_unit,campus,control_no,revision_no,form_date,prepared_by,prepared_title,noted_by,noted_title,attested_by,attested_title,approved_by,approved_title,created_by,submission_status,submitted_by,submitted_at)
            VALUES(".($v['program_id']?:'NULL').",".($v['project_id']?:'NULL').",{$v['quarter']},{$v['report_year']},NULLIF('{$v['duration_start']}',''),NULLIF('{$v['duration_end']}',''),'{$v['implementing_unit']}','{$v['campus']}','{$v['control_no']}','{$v['revision_no']}',NULLIF('{$v['form_date']}',''),'{$v['prepared_by']}','{$v['prepared_title']}','{$v['noted_by']}','{$v['noted_title']}','{$v['attested_by']}','{$v['attested_title']}','{$v['approved_by']}','{$v['approved_title']}',$userId,'$status',$submittedBy,$submittedAt)";
        if(!$this->conn->query($sql)) { $this->conn->rollback(); return false; }
        $id=intval($this->conn->insert_id);
        if(!$this->saveItems($id,$items)) { $this->conn->rollback(); return false; }
        $this->conn->commit();
        if($status==='Submitted') $this->notifySubmitted();
        return $id;
    }

    public function update($id,$data,$items,$userId) {
        $id=intval($id); $report=$this->find($id); if(!$report || !$this->canEdit($report)) return false;
        $userId=intval($userId); $v=$this->headerValues($data);
        $status=($data['save_action'] ?? 'draft')==='submit'?'Submitted':($report['submission_status'] ?? 'Draft');
        $submitSql=$status==='Submitted'?",submitted_by=$userId,submitted_at=NOW(),recalled_by=NULL,recalled_at=NULL":'';
        $this->conn->begin_transaction();
        $sql="UPDATE field_visit_logs SET program_id=".($v['program_id']?:'NULL').",project_id=".($v['project_id']?:'NULL').",quarter={$v['quarter']},report_year={$v['report_year']},duration_start=NULLIF('{$v['duration_start']}',''),duration_end=NULLIF('{$v['duration_end']}',''),implementing_unit='{$v['implementing_unit']}',campus='{$v['campus']}',control_no='{$v['control_no']}',revision_no='{$v['revision_no']}',form_date=NULLIF('{$v['form_date']}',''),prepared_by='{$v['prepared_by']}',prepared_title='{$v['prepared_title']}',noted_by='{$v['noted_by']}',noted_title='{$v['noted_title']}',attested_by='{$v['attested_by']}',attested_title='{$v['attested_title']}',approved_by='{$v['approved_by']}',approved_title='{$v['approved_title']}',submission_status='$status',updated_by=$userId,updated_at=NOW()$submitSql WHERE id=$id";
        if(!$this->conn->query($sql) || !$this->conn->query("DELETE FROM field_visit_items WHERE log_id=$id") || !$this->saveItems($id,$items)) { $this->conn->rollback(); return false; }
        $this->conn->commit();
        if($status==='Submitted') $this->notifySubmitted();
        return true;
    }

    public function submit($id,$userId) {
        $id=intval($id); $userId=intval($userId); $report=$this->find($id);
        if(!$report || !$this->canEdit($report)) return false;
        $ok=$this->conn->query("UPDATE field_visit_logs SET submission_status='Submitted',submitted_by=$userId,submitted_at=NOW(),recalled_by=NULL,recalled_at=NULL WHERE id=$id");
        if($ok) $this->notifySubmitted(); return $ok;
    }

    public function recall($id,$userId) {
        $id=intval($id); $userId=intval($userId); $report=$this->find($id);
        if(!$report || !$this->canRecall($report)) return false;
        return $this->conn->query("UPDATE field_visit_logs SET submission_status='Recalled',recalled_by=$userId,recalled_at=NOW() WHERE id=$id");
    }

    public function delete($id) {
        $id=intval($id); $report=$this->find($id);
        if(!$report || (!$this->canDelete($report) && !hasRole(['Super Admin']))) return false;
        $type=$this->esc($this->documentType); $this->conn->begin_transaction();
        $ok=$this->conn->query("DELETE FROM field_visit_items WHERE log_id=$id")
            && $this->conn->query("DELETE FROM document_approvals WHERE document_type='$type' AND document_id=$id")
            && $this->conn->query("DELETE FROM field_visit_logs WHERE id=$id");
        $ok?$this->conn->commit():$this->conn->rollback(); return $ok;
    }

    private function notifySubmitted() {
        notifyUsersByRole($this->conn,['Department Coordinator'],'Field Visit Log Submitted','A field visit log is waiting for your approval.','index.php?page=approval_center');
    }

    private function expectedStatus($role) {
        $role=normalizeRole($role);
        if($role==='department coordinator') return 'Submitted';
        if($role==='school coordinator') return 'Department Coordinator Approved';
        if($role==='campus director') return 'School Coordinator Approved';
        if($role==='vp ories') return 'Campus Director Approved';
        return '';
    }

    public function approvalQueueForUser($user) {
        $expected=$this->expectedStatus($user['role'] ?? ''); if($expected==='') return [];
        $expected=$this->esc($expected);
        $result=$this->conn->query("SELECT l.*,p.project_title,pr.program_title,'field_visit_log' document_type,'Field Visit Log' document_label,
            CONCAT(l.quarter,CASE l.quarter WHEN 1 THEN 'st' WHEN 2 THEN 'nd' WHEN 3 THEN 'rd' ELSE 'th' END,' Quarter ',l.report_year) period_covered,
            l.implementing_unit department,l.campus college
            FROM field_visit_logs l LEFT JOIN projects p ON p.id=l.project_id LEFT JOIN programs pr ON pr.id=l.program_id
            WHERE l.submission_status='$expected' ORDER BY l.submitted_at");
        $data=[]; if($result) while($row=$result->fetch_assoc()) $data[]=$row; return $data;
    }

    public function approveAsCurrentUser($id,$user,$remarks='') { return $this->recordDecision($id,$user,'Approved',$remarks); }
    public function notApproveAsCurrentUser($id,$user,$remarks='') { return $this->recordDecision($id,$user,'Not Approved',$remarks); }

    private function recordDecision($id,$user,$decision,$remarks) {
        $id=intval($id); $uid=intval($user['id'] ?? 0); $role=normalizeRole($user['role'] ?? '');
        $report=$this->find($id); $expected=$this->expectedStatus($role);
        if(!$report || $expected==='' || ($report['submission_status'] ?? '')!==$expected) return false;
        $levels=['department coordinator'=>1,'school coordinator'=>2,'campus director'=>3,'vp ories'=>4];
        $labels=['department coordinator'=>'Department Coordinator','school coordinator'=>'School Coordinator','campus director'=>'Campus Director / Dean','vp ories'=>'VP ORIES'];
        $next=['department coordinator'=>'Department Coordinator Approved','school coordinator'=>'School Coordinator Approved','campus director'=>'Campus Director Approved','vp ories'=>'Approved'];
        $level=$levels[$role]; $label=$this->esc($labels[$role]); $remarks=$this->esc($remarks); $type=$this->esc($this->documentType);
        $status=$decision==='Approved'?$next[$role]:'Not Approved'; $this->conn->begin_transaction();
        $ok=$this->conn->query("DELETE FROM document_approvals WHERE document_type='$type' AND document_id=$id AND approval_level=$level")
            && $this->conn->query("INSERT INTO document_approvals(document_type,document_id,approval_level,approval_role,approver_user_id,status,remarks,signed_at) VALUES('$type',$id,$level,'$label',$uid,'$decision','$remarks',NOW())")
            && $this->conn->query("UPDATE field_visit_logs SET submission_status='$status',revision_notes='$remarks',updated_at=NOW() WHERE id=$id");
        $ok?$this->conn->commit():$this->conn->rollback();
        if($ok) {
            $owner=intval($report['created_by'] ?? 0);
            if($status==='Approved' || $status==='Not Approved') notifyUser($this->conn,$owner,'Field Visit Log '.$status,'Your field visit log was '.$status.'.','index.php?page=view_field_visit_log&id='.$id);
            else {
                $nextRole=['Department Coordinator Approved'=>'School Coordinator','School Coordinator Approved'=>'Campus Director','Campus Director Approved'=>'VP ORIES'][$status] ?? '';
                if($nextRole!=='') notifyUsersByRole($this->conn,[$nextRole],'Field Visit Log Approval','A field visit log needs your approval.','index.php?page=approval_center');
            }
        }
        return $ok;
    }
}
?>
