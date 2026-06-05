<?php
class BarangayLocation {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function all() {
        $result = $this->conn->query("SELECT * FROM barangay_locations ORDER BY province, municipality, barangay");
        $data = [];
        if($result) {
            while($row = $result->fetch_assoc()) $data[] = $row;
        }
        return $data;
    }
}
?>
