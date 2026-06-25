<?php
class QuarterlyAccomplishment {
    private $conn;
    private $documentType = 'quarterly_accomplishment';

    public function __construct($db) {
        $this->conn = $db;
        $this->ensureSchema();
    }

    private function ensureSchema() {
        @$this->conn->query("CREATE TABLE IF NOT EXISTS quarterly_accomplishment_reports (
            id int(11) NOT NULL AUTO_INCREMENT,
            college varchar(150) DEFAULT NULL,
            campus varchar(150) DEFAULT NULL,
            department varchar(150) DEFAULT NULL,
            quarter tinyint(1) NOT NULL DEFAULT 1,
            report_year smallint(6) NOT NULL,
            control_no varchar(100) DEFAULT 'EVSU-ORDE&S-F-027',
            revision_no varchar(50) DEFAULT '03',
            effectivity_date date DEFAULT NULL,
            prepared_by varchar(180) DEFAULT NULL,
            prepared_title varchar(180) DEFAULT NULL,
            checked_by varchar(180) DEFAULT NULL,
            checked_title varchar(180) DEFAULT NULL,
            noted_by varchar(180) DEFAULT NULL,
            noted_title varchar(180) DEFAULT NULL,
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
            KEY created_by (created_by),
            KEY reporting_period (report_year, quarter)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        @$this->conn->query("CREATE TABLE IF NOT EXISTS quarterly_accomplishment_items (
            id int(11) NOT NULL AUTO_INCREMENT,
            report_id int(11) NOT NULL,
            program_id int(11) DEFAULT NULL,
            project_id int(11) DEFAULT NULL,
            inclusive_date_start date DEFAULT NULL,
            inclusive_date_end date DEFAULT NULL,
            activity_title text DEFAULT NULL,
            beneficiary_type text DEFAULT NULL,
            male_count int(11) NOT NULL DEFAULT 0,
            female_count int(11) NOT NULL DEFAULT 0,
            quality_rating decimal(5,2) DEFAULT NULL,
            duration_hours decimal(8,2) NOT NULL DEFAULT 0.00,
            service_type varchar(180) DEFAULT NULL,
            partner_agency text DEFAULT NULL,
            faculty_staff_involved text DEFAULT NULL,
            students_involved int(11) NOT NULL DEFAULT 0,
            nature_of_participation text DEFAULT NULL,
            project_cost decimal(12,2) NOT NULL DEFAULT 0.00,
            funding_source varchar(180) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY report_id (report_id),
            KEY project_id (project_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    private function esc($value) {
        return $this->conn->real_escape_string((string)($value ?? ''));
    }

    public function projectOptions() {
        $sql = "SELECT p.id, p.project_title, p.program_id, p.start_date, p.end_date, pr.program_title
                FROM projects p JOIN programs pr ON pr.id=p.program_id
                ORDER BY pr.program_title, p.project_title";
        $result = $this->conn->query($sql);
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function countAll() {
        $result = $this->conn->query("SELECT COUNT(*) total FROM quarterly_accomplishment_reports");
        return $result ? intval($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function paginated($limit, $offset) {
        $limit = max(1, intval($limit));
        $offset = max(0, intval($offset));
        $sql = "SELECT r.*, u.fullname created_by_name,
                (SELECT COUNT(*) FROM quarterly_accomplishment_items i WHERE i.report_id=r.id) item_count
                FROM quarterly_accomplishment_reports r
                LEFT JOIN users u ON u.id=r.created_by
                ORDER BY r.report_year DESC, r.quarter DESC, r.created_at DESC
                LIMIT $limit OFFSET $offset";
        $result = $this->conn->query($sql);
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function find($id) {
        $id = intval($id);
        $result = $this->conn->query("SELECT r.*, u.fullname created_by_name
            FROM quarterly_accomplishment_reports r LEFT JOIN users u ON u.id=r.created_by WHERE r.id=$id LIMIT 1");
        return $result ? ($result->fetch_assoc() ?: null) : null;
    }

    public function items($id) {
        $id = intval($id);
        $sql = "SELECT i.*, p.project_title, pr.program_title
                FROM quarterly_accomplishment_items i
                LEFT JOIN projects p ON p.id=i.project_id
                LEFT JOIN programs pr ON pr.id=i.program_id
                WHERE i.report_id=$id ORDER BY i.id";
        $result = $this->conn->query($sql);
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function approvals($id) {
        $id = intval($id);
        $type = $this->esc($this->documentType);
        $result = $this->conn->query("SELECT da.*, u.fullname, u.signatory_title
            FROM document_approvals da LEFT JOIN users u ON u.id=da.approver_user_id
            WHERE da.document_type='$type' AND da.document_id=$id ORDER BY da.approval_level");
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function canEdit($report) {
        return in_array($report['submission_status'] ?? 'Draft', ['Draft','Recalled','For Revision','Not Approved'], true);
    }

    public function canRecall($report) {
        return in_array($report['submission_status'] ?? '', ['Submitted','Under Review'], true);
    }

    public function canDelete($report) {
        return $this->canEdit($report);
    }

    private function saveItems($reportId, $items) {
        $reportId = intval($reportId);
        foreach($items as $item) {
            $projectId = intval($item['project_id'] ?? 0);
            $activityTitle = trim((string)($item['activity_title'] ?? ''));
            if($projectId <= 0 && $activityTitle === '') continue;

            $programId = 0;
            if($projectId > 0) {
                $project = $this->conn->query("SELECT program_id FROM projects WHERE id=$projectId LIMIT 1");
                $project = $project ? $project->fetch_assoc() : null;
                $programId = intval($project['program_id'] ?? 0);
            }

            $start = $this->esc($item['inclusive_date_start'] ?? '');
            $end = $this->esc($item['inclusive_date_end'] ?? '');
            $activity = $this->esc($activityTitle);
            $beneficiary = $this->esc($item['beneficiary_type'] ?? '');
            $male = max(0, intval($item['male_count'] ?? 0));
            $female = max(0, intval($item['female_count'] ?? 0));
            $ratingValue = trim((string)($item['quality_rating'] ?? ''));
            $rating = $ratingValue === '' ? 'NULL' : floatval($ratingValue);
            $hours = max(0, floatval($item['duration_hours'] ?? 0));
            $service = $this->esc($item['service_type'] ?? '');
            $partner = $this->esc($item['partner_agency'] ?? '');
            $faculty = $this->esc($item['faculty_staff_involved'] ?? '');
            $students = max(0, intval($item['students_involved'] ?? 0));
            $participation = $this->esc($item['nature_of_participation'] ?? '');
            $cost = max(0, floatval($item['project_cost'] ?? 0));
            $funding = $this->esc($item['funding_source'] ?? '');

            $sql = "INSERT INTO quarterly_accomplishment_items
                (report_id,program_id,project_id,inclusive_date_start,inclusive_date_end,activity_title,beneficiary_type,male_count,female_count,quality_rating,duration_hours,service_type,partner_agency,faculty_staff_involved,students_involved,nature_of_participation,project_cost,funding_source)
                VALUES ($reportId,".($programId ?: 'NULL').",".($projectId ?: 'NULL').",NULLIF('$start',''),NULLIF('$end',''),'$activity','$beneficiary',$male,$female,$rating,$hours,'$service','$partner','$faculty',$students,'$participation',$cost,'$funding')";
            if(!$this->conn->query($sql)) return false;
        }
        return true;
    }

    public function create($data, $items, $userId) {
        $userId = intval($userId);
        $status = ($data['save_action'] ?? 'draft') === 'submit' ? 'Submitted' : 'Draft';
        $fields = $this->headerValues($data);
        $submittedBy = $status === 'Submitted' ? $userId : 'NULL';
        $submittedAt = $status === 'Submitted' ? 'NOW()' : 'NULL';
        $this->conn->begin_transaction();
        $sql = "INSERT INTO quarterly_accomplishment_reports
            (college,campus,department,quarter,report_year,control_no,revision_no,effectivity_date,prepared_by,prepared_title,checked_by,checked_title,noted_by,noted_title,approved_by,approved_title,created_by,submission_status,submitted_by,submitted_at)
            VALUES ('{$fields['college']}','{$fields['campus']}','{$fields['department']}',{$fields['quarter']},{$fields['report_year']},'{$fields['control_no']}','{$fields['revision_no']}',NULLIF('{$fields['effectivity_date']}',''),'{$fields['prepared_by']}','{$fields['prepared_title']}','{$fields['checked_by']}','{$fields['checked_title']}','{$fields['noted_by']}','{$fields['noted_title']}','{$fields['approved_by']}','{$fields['approved_title']}',$userId,'$status',$submittedBy,$submittedAt)";
        if(!$this->conn->query($sql)) { $this->conn->rollback(); return false; }
        $id = intval($this->conn->insert_id);
        if(!$this->saveItems($id, $items)) { $this->conn->rollback(); return false; }
        $this->conn->commit();
        if($status === 'Submitted') $this->notifySubmitted($id);
        return $id;
    }

    public function update($id, $data, $items, $userId) {
        $id = intval($id);
        $report = $this->find($id);
        if(!$report || !$this->canEdit($report)) return false;
        $fields = $this->headerValues($data);
        $userId = intval($userId);
        $status = ($data['save_action'] ?? 'draft') === 'submit' ? 'Submitted' : ($report['submission_status'] ?? 'Draft');
        $submitSql = $status === 'Submitted' ? ",submitted_by=$userId,submitted_at=NOW(),recalled_by=NULL,recalled_at=NULL" : '';
        $this->conn->begin_transaction();
        $sql = "UPDATE quarterly_accomplishment_reports SET
            college='{$fields['college']}',campus='{$fields['campus']}',department='{$fields['department']}',quarter={$fields['quarter']},report_year={$fields['report_year']},control_no='{$fields['control_no']}',revision_no='{$fields['revision_no']}',effectivity_date=NULLIF('{$fields['effectivity_date']}',''),prepared_by='{$fields['prepared_by']}',prepared_title='{$fields['prepared_title']}',checked_by='{$fields['checked_by']}',checked_title='{$fields['checked_title']}',noted_by='{$fields['noted_by']}',noted_title='{$fields['noted_title']}',approved_by='{$fields['approved_by']}',approved_title='{$fields['approved_title']}',submission_status='$status',updated_by=$userId,updated_at=NOW()$submitSql WHERE id=$id";
        if(!$this->conn->query($sql) || !$this->conn->query("DELETE FROM quarterly_accomplishment_items WHERE report_id=$id") || !$this->saveItems($id, $items)) {
            $this->conn->rollback(); return false;
        }
        $this->conn->commit();
        if($status === 'Submitted') $this->notifySubmitted($id);
        return true;
    }

    private function headerValues($data) {
        $keys = ['college','campus','department','control_no','revision_no','effectivity_date','prepared_by','prepared_title','checked_by','checked_title','noted_by','noted_title','approved_by','approved_title'];
        $values = [];
        foreach($keys as $key) $values[$key] = $this->esc($data[$key] ?? '');
        $values['quarter'] = min(4, max(1, intval($data['quarter'] ?? 1)));
        $values['report_year'] = min(2100, max(2000, intval($data['report_year'] ?? date('Y'))));
        return $values;
    }

    public function submit($id, $userId) {
        $id = intval($id); $userId = intval($userId);
        $report = $this->find($id);
        if(!$report || !$this->canEdit($report)) return false;
        $ok = $this->conn->query("UPDATE quarterly_accomplishment_reports SET submission_status='Submitted',submitted_by=$userId,submitted_at=NOW(),recalled_by=NULL,recalled_at=NULL WHERE id=$id");
        if($ok) $this->notifySubmitted($id);
        return $ok;
    }

    public function recall($id, $userId) {
        $id = intval($id); $userId = intval($userId);
        $report = $this->find($id);
        if(!$report || !$this->canRecall($report)) return false;
        return $this->conn->query("UPDATE quarterly_accomplishment_reports SET submission_status='Recalled',recalled_by=$userId,recalled_at=NOW() WHERE id=$id");
    }

    public function delete($id) {
        $id = intval($id);
        $report = $this->find($id);
        if(!$report || (!$this->canDelete($report) && !hasRole(['Super Admin']))) return false;
        $type = $this->esc($this->documentType);
        $this->conn->begin_transaction();
        $ok = $this->conn->query("DELETE FROM quarterly_accomplishment_items WHERE report_id=$id")
            && $this->conn->query("DELETE FROM document_approvals WHERE document_type='$type' AND document_id=$id")
            && $this->conn->query("DELETE FROM quarterly_accomplishment_reports WHERE id=$id");
        $ok ? $this->conn->commit() : $this->conn->rollback();
        return $ok;
    }

    private function notifySubmitted($id) {
        notifyUsersByRole($this->conn, ['Department Coordinator'], 'Quarterly Accomplishment Submitted', 'A quarterly accomplishment report is waiting for your approval.', 'index.php?page=approval_center');
    }

    private function expectedStatus($role) {
        $role = normalizeRole($role);
        if($role === 'department coordinator') return 'Submitted';
        if($role === 'school coordinator') return 'Department Coordinator Approved';
        if($role === 'campus director') return 'School Coordinator Approved';
        if($role === 'vp ories') return 'Campus Director Approved';
        return '';
    }

    public function approvalQueueForUser($user) {
        $expected = $this->expectedStatus($user['role'] ?? '');
        if($expected === '') return [];
        $expected = $this->esc($expected);
        $result = $this->conn->query("SELECT r.*, 'quarterly_accomplishment' document_type, 'Quarterly Accomplishment' document_label,
            CONCAT(r.quarter, CASE r.quarter WHEN 1 THEN 'st' WHEN 2 THEN 'nd' WHEN 3 THEN 'rd' ELSE 'th' END, ' Quarter ', r.report_year) period_covered
            FROM quarterly_accomplishment_reports r WHERE r.submission_status='$expected' ORDER BY r.submitted_at");
        $data=[]; if($result) while($row=$result->fetch_assoc()) $data[]=$row; return $data;
    }

    public function approveAsCurrentUser($id, $user, $remarks='') {
        return $this->recordDecision($id, $user, 'Approved', $remarks);
    }

    public function notApproveAsCurrentUser($id, $user, $remarks='') {
        return $this->recordDecision($id, $user, 'Not Approved', $remarks);
    }

    private function recordDecision($id, $user, $decision, $remarks) {
        $id=intval($id); $uid=intval($user['id'] ?? 0); $role=normalizeRole($user['role'] ?? '');
        $report=$this->find($id); $expected=$this->expectedStatus($role);
        if(!$report || $expected==='' || ($report['submission_status'] ?? '')!==$expected) return false;
        $levels=['department coordinator'=>1,'school coordinator'=>2,'campus director'=>3,'vp ories'=>4];
        $labels=['department coordinator'=>'Department Coordinator','school coordinator'=>'School Coordinator','campus director'=>'Campus Director / Dean','vp ories'=>'VP ORIES'];
        $next=['department coordinator'=>'Department Coordinator Approved','school coordinator'=>'School Coordinator Approved','campus director'=>'Campus Director Approved','vp ories'=>'Approved'];
        $level=$levels[$role]; $label=$this->esc($labels[$role]); $remarks=$this->esc($remarks); $type=$this->esc($this->documentType);
        $status=$decision==='Approved'?$next[$role]:'Not Approved';
        $this->conn->begin_transaction();
        $ok=$this->conn->query("DELETE FROM document_approvals WHERE document_type='$type' AND document_id=$id AND approval_level=$level")
            && $this->conn->query("INSERT INTO document_approvals(document_type,document_id,approval_level,approval_role,approver_user_id,status,remarks,signed_at) VALUES('$type',$id,$level,'$label',$uid,'$decision','$remarks',NOW())")
            && $this->conn->query("UPDATE quarterly_accomplishment_reports SET submission_status='$status',revision_notes='$remarks',updated_at=NOW() WHERE id=$id");
        $ok ? $this->conn->commit() : $this->conn->rollback();
        if($ok) {
            $owner=intval($report['created_by'] ?? 0);
            if($status==='Approved' || $status==='Not Approved') notifyUser($this->conn,$owner,'Quarterly Accomplishment '.$status,'Your quarterly accomplishment report was '.$status.'.','index.php?page=view_quarterly_accomplishment&id='.$id);
            else {
                $nextRole=['Department Coordinator Approved'=>'School Coordinator','School Coordinator Approved'=>'Campus Director','Campus Director Approved'=>'VP ORIES'][$status] ?? '';
                if($nextRole!=='') notifyUsersByRole($this->conn,[$nextRole],'Quarterly Accomplishment Approval','A quarterly accomplishment report needs your approval.','index.php?page=approval_center');
            }
        }
        return $ok;
    }
}
?>
