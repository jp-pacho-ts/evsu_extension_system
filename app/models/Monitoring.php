<?php
class Monitoring {
    private $conn;
    private $statuses = ['On-going','Completed','Inactive','Expired','Terminated'];

    public function __construct($db) {
        $this->conn = $db;
        $this->ensureCampusColumns();
    }

    private function esc($value) {
        return $this->conn->real_escape_string($value ?? '');
    }

    private function ensureCampusColumns() {
        $this->ensureColumn('evsu_campus', "ALTER TABLE monitoring_entries ADD COLUMN evsu_campus varchar(150) DEFAULT NULL AFTER remarks");
        $this->ensureColumn('campus_school', "ALTER TABLE monitoring_entries ADD COLUMN campus_school varchar(50) DEFAULT NULL AFTER evsu_campus");
    }

    private function ensureColumn($column, $sql) {
        $column = $this->esc($column);
        $result = @$this->conn->query("SHOW COLUMNS FROM monitoring_entries LIKE '$column'");
        if($result && $result->num_rows > 0) return true;

        @$this->conn->query($sql);
        $result = @$this->conn->query("SHOW COLUMNS FROM monitoring_entries LIKE '$column'");
        return $result && $result->num_rows > 0;
    }

    public function all() {
        return $this->queryMonitoring();
    }

    public function countAll() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM monitoring_entries");
        return $result ? intval($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function countFiltered($filters = []) {
        $whereSql = $this->filterWhereSql($filters);
        $sql = "SELECT COUNT(*) AS total
                FROM monitoring_entries me
                LEFT JOIN projects pr ON pr.id = me.project_id
                LEFT JOIN programs pg ON pg.id = pr.program_id".$whereSql;

        $result = $this->conn->query($sql);
        return $result ? intval($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function paginated($limit, $offset) {
        $limit = max(1, intval($limit));
        $offset = max(0, intval($offset));
        return $this->queryMonitoring('', " LIMIT $limit OFFSET $offset");
    }

    public function paginatedFiltered($filters, $limit, $offset) {
        $limit = max(1, intval($limit));
        $offset = max(0, intval($offset));
        return $this->queryMonitoring($this->filterWhereSql($filters), " LIMIT $limit OFFSET $offset");
    }

    public function filterOptions() {
        return [
            'statuses' => $this->statuses,
            'campuses' => $this->distinctValues('evsu_campus'),
            'schools' => $this->distinctValues('campus_school')
        ];
    }

    private function queryMonitoring($whereSql = '', $limitSql = '') {
        $sql = "SELECT
                    me.*,
                    pg.program_title,
                    pr.project_title,
                    pr.sdg,
                    pr.partners AS partner,
                    pr.leader,
                    pr.assistant_leader AS assistant,
                    pr.members,
                    pr.special_order_no,
                    pr.type_of_clientele,
                    COALESCE(NULLIF(me.status,''), pr.status, 'On-going') AS status,
                    COALESCE(NULLIF(me.barangay,''), pr.barangay, '') AS barangay,
                    COALESCE(NULLIF(me.municipality,''), pr.municipality, '') AS municipality,
                    COALESCE(NULLIF(me.province,''), pr.province, '') AS province
                FROM monitoring_entries me
                LEFT JOIN projects pr ON pr.id = me.project_id
                LEFT JOIN programs pg ON pg.id = pr.program_id
                ".$whereSql."
                ORDER BY me.monitoring_date DESC, me.id DESC".$limitSql;

        $result = $this->conn->query($sql);
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    private function filterWhereSql($filters) {
        $filters = is_array($filters) ? $filters : [];
        $where = [];

        $q = trim((string)($filters['q'] ?? ''));
        if($q !== '') {
            $term = $this->esc($q);
            $like = "'%$term%'";
            $where[] = "(CAST(me.id AS CHAR) LIKE $like
                OR CAST(me.project_id AS CHAR) LIKE $like
                OR me.activity_title LIKE $like
                OR me.source_of_fund LIKE $like
                OR me.activity_description LIKE $like
                OR me.remarks LIKE $like
                OR me.evsu_campus LIKE $like
                OR me.campus_school LIKE $like
                OR COALESCE(NULLIF(me.barangay,''), pr.barangay, '') LIKE $like
                OR COALESCE(NULLIF(me.municipality,''), pr.municipality, '') LIKE $like
                OR COALESCE(NULLIF(me.province,''), pr.province, '') LIKE $like
                OR pg.program_title LIKE $like
                OR pr.project_title LIKE $like
                OR pr.sdg LIKE $like
                OR pr.partners LIKE $like
                OR pr.type_of_clientele LIKE $like
                OR pr.leader LIKE $like
                OR pr.assistant_leader LIKE $like
                OR pr.members LIKE $like
                OR pr.special_order_no LIKE $like)";
        }

        $status = trim((string)($filters['status'] ?? ''));
        if($status !== '' && in_array($status, $this->statuses, true)) {
            $status = $this->esc($status);
            $where[] = "COALESCE(NULLIF(me.status,''), pr.status, 'On-going') = '$status'";
        }

        $campus = trim((string)($filters['campus'] ?? ''));
        if($campus !== '') {
            $campus = $this->esc($campus);
            $where[] = "me.evsu_campus = '$campus'";
        }

        $school = trim((string)($filters['school'] ?? ''));
        if($school !== '') {
            $school = $this->esc($school);
            $where[] = "me.campus_school = '$school'";
        }

        $dateFrom = trim((string)($filters['date_from'] ?? ''));
        if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = $this->esc($dateFrom);
            $where[] = "me.monitoring_date >= '$dateFrom'";
        }

        $dateTo = trim((string)($filters['date_to'] ?? ''));
        if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = $this->esc($dateTo);
            $where[] = "me.monitoring_date <= '$dateTo'";
        }

        return empty($where) ? '' : ' WHERE '.implode(' AND ', $where);
    }

    private function distinctValues($column) {
        $allowed = ['evsu_campus', 'campus_school'];
        if(!in_array($column, $allowed, true)) return [];

        $result = $this->conn->query("SELECT DISTINCT $column AS value
            FROM monitoring_entries
            WHERE $column IS NOT NULL AND TRIM($column) <> ''
            ORDER BY $column ASC");

        $values = [];
        if($result) {
            while($row = $result->fetch_assoc()) $values[] = $row['value'];
        }

        return $values;
    }

    public function create($data) {
        $project_id = intval($data['project_id'] ?? 0);

        $project = $this->conn->query("SELECT status FROM projects WHERE id=$project_id")->fetch_assoc();

        $activity_title = $this->esc($data['activity_title'] ?? '');
        $monitoring_date = $this->esc($data['monitoring_date'] ?? '');
        $source_of_fund = $this->esc($data['source_of_fund'] ?? '');
        $terminal_report_date = $this->esc($data['terminal_report_date'] ?? '');
        $activity_description = $this->esc($data['activity_description'] ?? '');
        $remarks = $this->esc($data['remarks'] ?? '');
        $evsu_campus = $this->esc($data['evsu_campus'] ?? '');
        $campus_school = $this->esc($data['campus_school'] ?? '');
        $status = $this->esc($data['status'] ?? ($project['status'] ?? 'On-going'));

        $monitoringDateSql = $monitoring_date ? "'$monitoring_date'" : "NULL";
        $terminalDateSql = $terminal_report_date ? "'$terminal_report_date'" : "NULL";

        $ok = $this->conn->query("INSERT INTO monitoring_entries(
            project_id,
            activity_title,
            monitoring_date,
            source_of_fund,
            status,
            terminal_report_date,
            activity_description,
            remarks,
            evsu_campus,
            campus_school
        ) VALUES (
            $project_id,
            '$activity_title',
            $monitoringDateSql,
            '$source_of_fund',
            '$status',
            $terminalDateSql,
            '$activity_description',
            '$remarks',
            '$evsu_campus',
            '$campus_school'
        )");

        if($ok && $project_id > 0) {
            $this->conn->query("UPDATE projects SET status='$status' WHERE id=$project_id");
        }

        return $ok;
    }

    public function update($id, $data) {
        $id = intval($id);
        if($id <= 0) return false;

        $oldEntry = $this->conn->query("SELECT project_id FROM monitoring_entries WHERE id=$id")->fetch_assoc();
        if(!$oldEntry) return false;

        $project_id = intval($data['project_id'] ?? 0);
        $activity_title = $this->esc($data['activity_title'] ?? '');
        $monitoring_date = $this->esc($data['monitoring_date'] ?? '');
        $source_of_fund = $this->esc($data['source_of_fund'] ?? '');
        $terminal_report_date = $this->esc($data['terminal_report_date'] ?? '');
        $activity_description = $this->esc($data['activity_description'] ?? '');
        $remarks = $this->esc($data['remarks'] ?? '');
        $evsu_campus = $this->esc($data['evsu_campus'] ?? '');
        $campus_school = $this->esc($data['campus_school'] ?? '');
        $status = $this->esc($data['status'] ?? 'On-going');

        $monitoringDateSql = $monitoring_date ? "'$monitoring_date'" : "NULL";
        $terminalDateSql = $terminal_report_date ? "'$terminal_report_date'" : "NULL";

        $ok = $this->conn->query("UPDATE monitoring_entries SET
                project_id=$project_id,
                activity_title='$activity_title',
                monitoring_date=$monitoringDateSql,
                source_of_fund='$source_of_fund',
                status='$status',
                terminal_report_date=$terminalDateSql,
                activity_description='$activity_description',
                remarks='$remarks',
                evsu_campus='$evsu_campus',
                campus_school='$campus_school'
            WHERE id=$id");

        if($ok) {
            if($project_id > 0) {
                $this->conn->query("UPDATE projects SET status='$status' WHERE id=$project_id");
            }

            $oldProjectId = intval($oldEntry['project_id'] ?? 0);
            if($oldProjectId > 0 && $oldProjectId !== $project_id) {
                $this->syncProjectStatus($oldProjectId);
            }
        }

        return $ok;
    }

    public function delete($id) {
        $id = intval($id);
        if($id <= 0) return false;

        $entry = $this->conn->query("SELECT project_id FROM monitoring_entries WHERE id=$id")->fetch_assoc();
        if(!$entry) return false;

        $ok = $this->conn->query("DELETE FROM monitoring_entries WHERE id=$id");
        if($ok) {
            $this->syncProjectStatus(intval($entry['project_id'] ?? 0));
        }

        return $ok;
    }

    public function updateStatus($id, $status) {
        $id = intval($id);
        $status = $this->esc($status);

        $ok = $this->conn->query("UPDATE monitoring_entries SET status='$status' WHERE id=$id");

        $entry = $this->conn->query("SELECT project_id FROM monitoring_entries WHERE id=$id")->fetch_assoc();
        if($entry && !empty($entry['project_id'])) {
            $project_id = intval($entry['project_id']);
            $this->conn->query("UPDATE projects SET status='$status' WHERE id=$project_id");
        }

        return $ok;
    }

    private function syncProjectStatus($project_id) {
        $project_id = intval($project_id);
        if($project_id <= 0) return;

        $latest = $this->conn->query("SELECT status FROM monitoring_entries WHERE project_id=$project_id ORDER BY monitoring_date DESC, id DESC LIMIT 1")->fetch_assoc();
        if($latest && trim((string)($latest['status'] ?? '')) !== '') {
            $status = $this->esc($latest['status']);
            $this->conn->query("UPDATE projects SET status='$status' WHERE id=$project_id");
        }
    }
}
?>
