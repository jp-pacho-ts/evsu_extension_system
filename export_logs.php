<?php
require_once "config/database.php";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="activity_logs.csv"');

$out = fopen("php://output", "w");
fputcsv($out, ['ID','User','Action','Module','Description','IP Address','Date/Time']);

$result = $db->query("SELECT * FROM activity_logs ORDER BY created_at DESC");
if($result) {
    while($row = $result->fetch_assoc()) {
        fputcsv($out, [
            $row['id'] ?? '',
            $row['user_name'] ?? ($row['username'] ?? ''),
            $row['action'] ?? '',
            $row['module'] ?? '',
            $row['description'] ?? ($row['details'] ?? ''),
            $row['ip_address'] ?? '',
            $row['created_at'] ?? ''
        ]);
    }
}
fclose($out);
exit;
?>
