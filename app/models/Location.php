<?php
class Location {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function all() {
        $result = $this->conn->query("SELECT * FROM locations ORDER BY province, municipality");
        $data = [];
        if($result) while($row = $result->fetch_assoc()) $data[] = $row;
        return $data;
    }
}
?>
