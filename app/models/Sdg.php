<?php
class Sdg {
    private $conn;

    private $defaultSdgs = [
        [1, 'No Poverty'],
        [2, 'Zero Hunger'],
        [3, 'Good Health and Well-being'],
        [4, 'Quality Education'],
        [5, 'Gender Equality'],
        [6, 'Clean Water and Sanitation'],
        [7, 'Affordable and Clean Energy'],
        [8, 'Decent Work and Economic Growth'],
        [9, 'Industry, Innovation and Infrastructure'],
        [10, 'Reduced Inequalities'],
        [11, 'Sustainable Cities and Communities'],
        [12, 'Responsible Consumption and Production'],
        [13, 'Climate Action'],
        [14, 'Life Below Water'],
        [15, 'Life on Land'],
        [16, 'Peace, Justice and Strong Institutions'],
        [17, 'Partnerships for the Goals'],
    ];

    public function __construct($db) {
        $this->conn = $db;
        $this->ensureTables();
        $this->seedDefaults();
        $this->backfillProjectSdgs();
    }

    private function esc($value) {
        return $this->conn->real_escape_string($value ?? '');
    }

    private function ensureTables() {
        @$this->conn->query("CREATE TABLE IF NOT EXISTS sdgs (
            id int(11) NOT NULL AUTO_INCREMENT,
            sdg_number int(11) DEFAULT NULL,
            title varchar(180) NOT NULL,
            description text DEFAULT NULL,
            status enum('Active','Inactive') DEFAULT 'Active',
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            updated_at timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
            PRIMARY KEY (id),
            UNIQUE KEY unique_sdg_number (sdg_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        @$this->conn->query("CREATE TABLE IF NOT EXISTS project_sdgs (
            project_id int(11) NOT NULL,
            sdg_id int(11) NOT NULL,
            PRIMARY KEY (project_id, sdg_id),
            KEY sdg_id (sdg_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->ensureProjectSdgColumn();
    }

    private function ensureProjectSdgColumn() {
        $result = @$this->conn->query("SHOW COLUMNS FROM projects LIKE 'sdg'");
        if($result && $row = $result->fetch_assoc()) {
            if(stripos((string)($row['Type'] ?? ''), 'text') !== false) return true;
        }

        @$this->conn->query("ALTER TABLE projects MODIFY sdg text DEFAULT NULL");
        return true;
    }

    private function seedDefaults() {
        foreach($this->defaultSdgs as $sdg) {
            $number = intval($sdg[0]);
            $title = $this->esc($sdg[1]);
            @$this->conn->query("INSERT INTO sdgs(sdg_number, title, status)
                VALUES($number, '$title', 'Active')
                ON DUPLICATE KEY UPDATE title=VALUES(title)");
        }
    }

    private function backfillProjectSdgs() {
        $result = @$this->conn->query("SELECT id, sdg_number, title FROM sdgs");
        if(!$result) return;

        while($row = $result->fetch_assoc()) {
            $id = intval($row['id'] ?? 0);
            if($id <= 0) continue;

            $label = $this->esc($this->label($row));
            if($label === '') continue;

            @$this->conn->query("INSERT IGNORE INTO project_sdgs(project_id, sdg_id)
                SELECT id, $id FROM projects WHERE sdg LIKE '%$label%'");
        }
    }

    public function label($row) {
        $title = trim((string)($row['title'] ?? ''));
        $number = intval($row['sdg_number'] ?? 0);
        return $number > 0 ? "SDG $number: $title" : $title;
    }

    private function attachLabels($rows) {
        foreach($rows as &$row) {
            $row['label'] = $this->label($row);
        }
        return $rows;
    }

    public function all() {
        $sql = "SELECT sdgs.*,
                    (SELECT COUNT(*) FROM project_sdgs WHERE project_sdgs.sdg_id = sdgs.id) AS project_count
                FROM sdgs
                ORDER BY COALESCE(sdg_number, 999), title";
        $result = $this->conn->query($sql);
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $this->attachLabels($data);
    }

    public function labelsForIds($ids) {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array)$ids))));
        if(empty($ids)) return [];

        $result = $this->conn->query("SELECT * FROM sdgs WHERE id IN (".implode(',', $ids).") ORDER BY COALESCE(sdg_number, 999), title");
        $labels = [];
        if($result) {
            while($row = $result->fetch_assoc()) $labels[] = $this->label($row);
        }
        return $labels;
    }

    public function syncProject($projectId, $ids) {
        $projectId = intval($projectId);
        if($projectId <= 0) return false;

        $ids = array_values(array_unique(array_filter(array_map('intval', (array)$ids))));
        $this->conn->query("DELETE FROM project_sdgs WHERE project_id=$projectId");

        foreach($ids as $id) {
            $this->conn->query("INSERT IGNORE INTO project_sdgs(project_id, sdg_id) VALUES($projectId, $id)");
        }

        return true;
    }

    public function create($data) {
        $title = trim((string)($data['title'] ?? ''));
        if($title === '') return false;

        $numberValue = trim((string)($data['sdg_number'] ?? ''));
        $numberSql = $numberValue === '' ? 'NULL' : intval($numberValue);
        $title = $this->esc($title);
        $description = $this->esc($data['description'] ?? '');
        $status = ($data['status'] ?? 'Active') === 'Inactive' ? 'Inactive' : 'Active';

        return $this->conn->query("INSERT INTO sdgs(sdg_number, title, description, status)
            VALUES($numberSql, '$title', '$description', '$status')");
    }

    public function update($data) {
        $id = intval($data['sdg_id'] ?? $data['id'] ?? 0);
        $title = trim((string)($data['title'] ?? ''));
        if($id <= 0 || $title === '') return false;

        $numberValue = trim((string)($data['sdg_number'] ?? ''));
        $numberSql = $numberValue === '' ? 'NULL' : intval($numberValue);
        $title = $this->esc($title);
        $description = $this->esc($data['description'] ?? '');
        $status = ($data['status'] ?? 'Active') === 'Inactive' ? 'Inactive' : 'Active';

        return $this->conn->query("UPDATE sdgs SET
                sdg_number=$numberSql,
                title='$title',
                description='$description',
                status='$status'
            WHERE id=$id");
    }
}
?>
