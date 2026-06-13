<?php
class Project {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

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

    private function queryProjects($limitSql = '') {
        $sql = "SELECT projects.*, programs.program_title,
                (SELECT COUNT(*) FROM monitoring_entries WHERE monitoring_entries.project_id = projects.id) AS monitoring_count,
                (SELECT me.activity_title FROM monitoring_entries me WHERE me.project_id = projects.id ORDER BY me.monitoring_date DESC, me.id DESC LIMIT 1) AS latest_monitoring_title,
                (SELECT me.monitoring_date FROM monitoring_entries me WHERE me.project_id = projects.id ORDER BY me.monitoring_date DESC, me.id DESC LIMIT 1) AS latest_monitoring_date,
                (SELECT COALESCE(NULLIF(me.activity_description,''), NULLIF(me.remarks,''), '') FROM monitoring_entries me WHERE me.project_id = projects.id ORDER BY me.monitoring_date DESC, me.id DESC LIMIT 1) AS latest_update
                FROM projects
                JOIN programs ON programs.id = projects.program_id
                ORDER BY projects.created_at DESC".$limitSql;
        $result = $this->conn->query($sql);
        $data = [];
        if($result) while($row = $result->fetch_assoc()) {
            $row['esfi'] = computeESFI($row['monitoring_count'], $row['participants']);
            $row['esfi_label'] = esfiInterpretation($row['esfi']);
            $data[] = $row;
        }
        return $data;
    }

    public function find($id) {
        $id = intval($id);
        return $this->conn->query("SELECT projects.*, programs.program_title FROM projects JOIN programs ON programs.id=projects.program_id WHERE projects.id=$id")->fetch_assoc();
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
        $sdg = $esc($data['sdg'] ?? '');
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

        return $this->conn->query("INSERT INTO projects(program_id, project_title, sdg, partners, type_of_clientele, leader, assistant_leader, members, participants, project_cost, start_date, end_date, special_order_no, barangay, barangay_latitude, barangay_longitude, municipality, province, latitude, longitude, status)
            VALUES('$programId', '$projectTitle', '$sdg', '$partners', '$typeOfClientele', '$leader', '$assistantLeader', '$members', '$participants', '$projectCost', '$startDate', '$endDate', '$specialOrderNo', '$barangay', '$barangayLatitude', '$barangayLongitude', '$municipality', '$province', '$latitude', '$longitude', '$status')");
    }

    public function update($data) {
        $id = intval($data['project_id'] ?? $data['id'] ?? 0);
        if($id <= 0) return false;

        $esc = function($value) {
            return $this->conn->real_escape_string($value ?? '');
        };

        $programId = intval($data['program_id'] ?? 0);
        $projectTitle = $esc($data['project_title'] ?? '');
        $sdg = $esc($data['sdg'] ?? '');
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

        return $this->conn->query("UPDATE projects SET
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
