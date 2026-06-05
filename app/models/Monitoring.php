<?php
class Monitoring {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function esc($value) {
        return $this->conn->real_escape_string($value ?? '');
    }

    public function all() {
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
                ORDER BY me.monitoring_date DESC, me.id DESC";

        $result = $this->conn->query($sql);
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
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
            remarks
        ) VALUES (
            $project_id,
            '$activity_title',
            $monitoringDateSql,
            '$source_of_fund',
            '$status',
            $terminalDateSql,
            '$activity_description',
            '$remarks'
        )");

        if($ok && $project_id > 0) {
            $this->conn->query("UPDATE projects SET status='$status' WHERE id=$project_id");
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
}
?>
