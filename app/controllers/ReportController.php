<?php
require_once 'app/models/Monitoring.php';

class ReportController {
    private $m;

    function __construct($db) {
        $this->m = new Monitoring($db);
    }

    function index() {
        requireRole(['Super Admin','Admin','Extension Director']);

        $printAll = (string)($_GET['print'] ?? '') === '1';
        $pagination = null;

        if($printAll) {
            $entries = $this->m->all();
        } else {
            $pagination = paginationParams($this->m->countAll(), 10);
            $entries = $this->m->paginated($pagination['per_page'], $pagination['offset']);
        }

        include 'app/views/reports/index.php';
    }
}
?>
