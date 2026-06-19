<?php
class Program {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function esc($value) {
        return $this->conn->real_escape_string($value ?? '');
    }

    public function all() {
        $result = $this->conn->query("SELECT * FROM programs ORDER BY created_at DESC, id DESC");
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function countAll() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM programs");
        return $result ? intval($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function paginated($limit, $offset) {
        $limit = max(1, intval($limit));
        $offset = max(0, intval($offset));
        $result = $this->conn->query("SELECT * FROM programs ORDER BY created_at DESC, id DESC LIMIT $limit OFFSET $offset");
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function create($data) {
        $programTitle = $this->esc($data['program_title'] ?? '');
        $projectCost = $this->esc($data['project_cost'] ?? '0');
        $leader = $this->esc($data['leader'] ?? '');
        $assistantLeader = $this->esc($data['assistant_leader'] ?? '');
        $members = $this->esc($data['members'] ?? '');
        $startDate = $this->esc($data['start_date'] ?? '');
        $endDate = $this->esc($data['end_date'] ?? '');
        $specialOrderNo = $this->esc($data['special_order_no'] ?? '');

        return $this->conn->query("INSERT INTO programs(program_title, project_cost, leader, assistant_leader, members, start_date, end_date, special_order_no)
            VALUES('$programTitle', '$projectCost', '$leader', '$assistantLeader', '$members', '$startDate', '$endDate', '$specialOrderNo')");
    }

    public function update($data) {
        $id = intval($data['program_id'] ?? $data['id'] ?? 0);
        if($id <= 0) return false;

        $programTitle = $this->esc($data['program_title'] ?? '');
        $projectCost = $this->esc($data['project_cost'] ?? '0');
        $leader = $this->esc($data['leader'] ?? '');
        $assistantLeader = $this->esc($data['assistant_leader'] ?? '');
        $members = $this->esc($data['members'] ?? '');
        $startDate = $this->esc($data['start_date'] ?? '');
        $endDate = $this->esc($data['end_date'] ?? '');
        $specialOrderNo = $this->esc($data['special_order_no'] ?? '');

        return $this->conn->query("UPDATE programs SET
                program_title='$programTitle',
                project_cost='$projectCost',
                leader='$leader',
                assistant_leader='$assistantLeader',
                members='$members',
                start_date='$startDate',
                end_date='$endDate',
                special_order_no='$specialOrderNo'
            WHERE id=$id");
    }

    public function delete($id) {
        $id = intval($id);
        if($id <= 0) return false;

        $linked = $this->conn->query("SELECT COUNT(*) AS total FROM projects WHERE program_id=$id");
        if($linked && intval($linked->fetch_assoc()['total'] ?? 0) > 0) return false;

        return $this->conn->query("DELETE FROM programs WHERE id=$id");
    }
}
?>
