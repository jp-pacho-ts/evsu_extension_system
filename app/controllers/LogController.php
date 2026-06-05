<?php
class LogController {
    private $db;
    public function __construct($db) { $this->db = $db; }
    public function index() {
        requireRole(['Super Admin','Admin']);
        $result = $this->db->query("SELECT * FROM activity_logs ORDER BY created_at DESC");
        $logs = [];
        if($result) while($row = $result->fetch_assoc()) $logs[] = $row;
        include "app/views/logs/index.php";
    }
}
?>
