<?php
require_once __DIR__ . "/Sdg.php";

class Project {
    private $conn;
    private $sdgModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->sdgModel = new Sdg($db);
        $this->ensureMonitoringCampusColumns();
    }

    private function ensureMonitoringCampusColumns() {
        $this->ensureMonitoringColumn('evsu_campus', "ALTER TABLE monitoring_entries ADD COLUMN evsu_campus varchar(150) DEFAULT NULL AFTER remarks");
        $this->ensureMonitoringColumn('campus_school', "ALTER TABLE monitoring_entries ADD COLUMN campus_school varchar(50) DEFAULT NULL AFTER evsu_campus");
    }

    private function ensureMonitoringColumn($column, $sql) {
        $column = $this->conn->real_escape_string($column);
        $result = @$this->conn->query("SHOW COLUMNS FROM monitoring_entries LIKE '$column'");
        if($result && $result->num_rows > 0) return true;

        @$this->conn->query($sql);
        $result = @$this->conn->query("SHOW COLUMNS FROM monitoring_entries LIKE '$column'");
        return $result && $result->num_rows > 0;
    }

    public function all() {
        return $this->queryProjects();
    }

    public function countAll() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM projects");
        return $result ? intval($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function paginated($limit, $offset) {
        $limit = max(1, intval($limit));
        $offset = max(0, intval($offset));
        return $this->queryProjects(" LIMIT $limit OFFSET $offset");
    }

    public function rankingPaginated($limit, $offset) {
        $limit = max(1, intval($limit));
        $offset = max(0, intval($offset));
        return $this->queryProjects(" LIMIT $limit OFFSET $offset", " ORDER BY esfi_score DESC, projects.created_at DESC");
    }

    private function queryProjects($limitSql = '', $orderSql = " ORDER BY projects.created_at DESC") {
        $sdgLabelsSql = "(SELECT GROUP_CONCAT(CASE WHEN s.sdg_number IS NOT NULL THEN CONCAT('SDG ', s.sdg_number, ': ', s.title) ELSE s.title END ORDER BY COALESCE(s.sdg_number, 999), s.title SEPARATOR ', ') FROM project_sdgs ps JOIN sdgs s ON s.id = ps.sdg_id WHERE ps.project_id = projects.id)";
        $sdgIdsSql = "(SELECT GROUP_CONCAT(ps.sdg_id ORDER BY COALESCE(s.sdg_number, 999), s.title SEPARATOR ',') FROM project_sdgs ps JOIN sdgs s ON s.id = ps.sdg_id WHERE ps.project_id = projects.id)";
        $sql = "SELECT projects.*, programs.program_title,
                $sdgLabelsSql AS sdg_display,
                $sdgIdsSql AS sdg_ids,
                (SELECT COUNT(*) FROM monitoring_entries WHERE monitoring_entries.project_id = projects.id) AS monitoring_count,
                (((SELECT COUNT(*) FROM monitoring_entries WHERE monitoring_entries.project_id = projects.id) * 0.70) + ((projects.participants / 100) * 0.30)) AS esfi_score,
                (SELECT me.activity_title FROM monitoring_entries me WHERE me.project_id = projects.id ORDER BY me.monitoring_date DESC, me.id DESC LIMIT 1) AS latest_monitoring_title,
                (SELECT me.monitoring_date FROM monitoring_entries me WHERE me.project_id = projects.id ORDER BY me.monitoring_date DESC, me.id DESC LIMIT 1) AS latest_monitoring_date,
                (SELECT me.status FROM monitoring_entries me WHERE me.project_id = projects.id ORDER BY me.monitoring_date DESC, me.id DESC LIMIT 1) AS latest_monitoring_status,
                (SELECT me.evsu_campus FROM monitoring_entries me WHERE me.project_id = projects.id ORDER BY me.monitoring_date DESC, me.id DESC LIMIT 1) AS evsu_campus,
                (SELECT me.campus_school FROM monitoring_entries me WHERE me.project_id = projects.id ORDER BY me.monitoring_date DESC, me.id DESC LIMIT 1) AS campus_school,
                (SELECT COALESCE(NULLIF(me.activity_description,''), NULLIF(me.remarks,''), '') FROM monitoring_entries me WHERE me.project_id = projects.id ORDER BY me.monitoring_date DESC, me.id DESC LIMIT 1) AS latest_update
                FROM projects
                JOIN programs ON programs.id = projects.program_id".
                $orderSql.$limitSql;
        $result = $this->conn->query($sql);
        $data = [];
        if($result) while($row = $result->fetch_assoc()) {
            if(trim((string)($row['sdg_display'] ?? '')) !== '') $row['sdg'] = $row['sdg_display'];
            $row['esfi'] = computeESFI($row['monitoring_count'], $row['participants']);
            $row['esfi_label'] = esfiInterpretation($row['esfi']);
            $data[] = $row;
        }
        return $data;
    }

    public function find($id) {
        $id = intval($id);
        $sdgLabelsSql = "(SELECT GROUP_CONCAT(CASE WHEN s.sdg_number IS NOT NULL THEN CONCAT('SDG ', s.sdg_number, ': ', s.title) ELSE s.title END ORDER BY COALESCE(s.sdg_number, 999), s.title SEPARATOR ', ') FROM project_sdgs ps JOIN sdgs s ON s.id = ps.sdg_id WHERE ps.project_id = projects.id)";
        $sdgIdsSql = "(SELECT GROUP_CONCAT(ps.sdg_id ORDER BY COALESCE(s.sdg_number, 999), s.title SEPARATOR ',') FROM project_sdgs ps JOIN sdgs s ON s.id = ps.sdg_id WHERE ps.project_id = projects.id)";
        $row = $this->conn->query("SELECT projects.*, programs.program_title, $sdgLabelsSql AS sdg_display, $sdgIdsSql AS sdg_ids FROM projects JOIN programs ON programs.id=projects.program_id WHERE projects.id=$id")->fetch_assoc();
        if($row && trim((string)($row['sdg_display'] ?? '')) !== '') $row['sdg'] = $row['sdg_display'];
        return $row;
    }

    public function create($data) {
        $programId = intval($data['program_id'] ?? 0);
        $program = [];

        if($programId > 0) {
            $result = $this->conn->query("SELECT leader, assistant_leader, members, project_cost, start_date, end_date, special_order_no FROM programs WHERE id=$programId");
            if($result) $program = $result->fetch_assoc() ?: [];
        }

        $esc = function($value) {
            return $this->conn->real_escape_string($value ?? '');
        };
        $inherit = function($key, $default = '') use ($data, $program, $esc) {
            $value = $data[$key] ?? null;
            if($value === null || $value === '') $value = $program[$key] ?? $default;
            return $esc($value);
        };

        $projectTitle = $esc($data['project_title'] ?? '');
        $sdgIds = $data['sdg_ids'] ?? [];
        $sdgLabels = $this->sdgModel->labelsForIds($sdgIds);
        $sdg = $esc(!empty($sdgLabels) ? implode(', ', $sdgLabels) : (isset($data['sdg_ids_submitted']) ? '' : ($data['sdg'] ?? '')));
        $partners = $esc($data['partners'] ?? '');
        $typeOfClientele = $esc($data['type_of_clientele'] ?? '');
        $participants = intval($data['participants'] ?? 0);
        $leader = $inherit('leader');
        $assistantLeader = $inherit('assistant_leader');
        $members = $inherit('members');
        $projectCost = $inherit('project_cost', '0');
        $startDate = $inherit('start_date');
        $endDate = $inherit('end_date');
        $specialOrderNo = $inherit('special_order_no');
        $barangay = $esc($data['barangay'] ?? '');
        $barangayLatitude = $esc($data['barangay_latitude'] ?? '');
        $barangayLongitude = $esc($data['barangay_longitude'] ?? '');
        $municipality = $esc($data['municipality'] ?? '');
        $province = $esc($data['province'] ?? '');
        $latitude = $esc($data['latitude'] ?? '');
        $longitude = $esc($data['longitude'] ?? '');
        $status = $esc($data['status'] ?? 'On-going');

        $ok = $this->conn->query("INSERT INTO projects(program_id, project_title, sdg, partners, type_of_clientele, leader, assistant_leader, members, participants, project_cost, start_date, end_date, special_order_no, barangay, barangay_latitude, barangay_longitude, municipality, province, latitude, longitude, status)
            VALUES('$programId', '$projectTitle', '$sdg', '$partners', '$typeOfClientele', '$leader', '$assistantLeader', '$members', '$participants', '$projectCost', '$startDate', '$endDate', '$specialOrderNo', '$barangay', '$barangayLatitude', '$barangayLongitude', '$municipality', '$province', '$latitude', '$longitude', '$status')");
        if($ok) $this->sdgModel->syncProject($this->conn->insert_id, $sdgIds);
        return $ok;
    }

    public function update($data) {
        $id = intval($data['project_id'] ?? $data['id'] ?? 0);
        if($id <= 0) return false;

        $esc = function($value) {
            return $this->conn->real_escape_string($value ?? '');
        };

        $programId = intval($data['program_id'] ?? 0);
        $projectTitle = $esc($data['project_title'] ?? '');
        $sdgIds = $data['sdg_ids'] ?? [];
        $sdgLabels = $this->sdgModel->labelsForIds($sdgIds);
        $sdg = $esc(!empty($sdgLabels) ? implode(', ', $sdgLabels) : (isset($data['sdg_ids_submitted']) ? '' : ($data['sdg'] ?? '')));
        $partners = $esc($data['partners'] ?? '');
        $typeOfClientele = $esc($data['type_of_clientele'] ?? '');
        $participants = intval($data['participants'] ?? 0);
        $barangay = $esc($data['barangay'] ?? '');
        $barangayLatitude = $esc($data['barangay_latitude'] ?? '');
        $barangayLongitude = $esc($data['barangay_longitude'] ?? '');
        $municipality = $esc($data['municipality'] ?? '');
        $province = $esc($data['province'] ?? '');
        $latitude = $esc($data['latitude'] ?? '');
        $longitude = $esc($data['longitude'] ?? '');
        $status = $esc($data['status'] ?? 'On-going');

        $ok = $this->conn->query("UPDATE projects SET
                program_id='$programId',
                project_title='$projectTitle',
                sdg='$sdg',
                partners='$partners',
                type_of_clientele='$typeOfClientele',
                participants='$participants',
                barangay='$barangay',
                barangay_latitude='$barangayLatitude',
                barangay_longitude='$barangayLongitude',
                municipality='$municipality',
                province='$province',
                latitude='$latitude',
                longitude='$longitude',
                status='$status'
            WHERE id=$id");
        if($ok) $this->sdgModel->syncProject($id, $sdgIds);
        return $ok;
    }

    public function delete($id) {
        $id = intval($id);
        if($id <= 0) return false;

        $linked = $this->conn->query("SELECT COUNT(*) AS total FROM monitoring_entries WHERE project_id=$id");
        if($linked && intval($linked->fetch_assoc()['total'] ?? 0) > 0) return false;

        return $this->conn->query("DELETE FROM projects WHERE id=$id");
    }
}
?>
