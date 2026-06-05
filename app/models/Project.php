<?php
class Project {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function all() {
        $sql = "SELECT projects.*, programs.program_title,
                (SELECT COUNT(*) FROM monitoring_entries WHERE monitoring_entries.project_id = projects.id) AS monitoring_count
                FROM projects
                JOIN programs ON programs.id = projects.program_id
                ORDER BY projects.created_at DESC";
        $result = $this->conn->query($sql);
        $data = [];
        while($row = $result->fetch_assoc()) {
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
        foreach($data as $key=>$value) $data[$key] = $this->conn->real_escape_string($value);
        return $this->conn->query("INSERT INTO projects(program_id, project_title, sdg, partners, type_of_clientele, leader, assistant_leader, members, participants, project_cost, start_date, end_date, special_order_no, barangay, barangay_latitude, barangay_longitude, municipality, province, latitude, longitude, status)
            VALUES('{$data['program_id']}', '{$data['project_title']}', '{$data['sdg']}', '{$data['partners']}', '{$data['type_of_clientele']}', '{$data['leader']}', '{$data['assistant_leader']}', '{$data['members']}', '{$data['participants']}', '{$data['project_cost']}', '{$data['start_date']}', '{$data['end_date']}', '{$data['special_order_no']}', '{$data['barangay']}', '{$data['barangay_latitude']}', '{$data['barangay_longitude']}', '{$data['municipality']}', '{$data['province']}', '{$data['latitude']}', '{$data['longitude']}', '{$data['status']}')");
    }
}
?>
