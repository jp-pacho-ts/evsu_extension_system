<?php
require_once "app/models/Monitoring.php";
require_once "app/models/Project.php";

class MonitoringController {
    private $model;
    private $projectModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new Monitoring($db);
        $this->projectModel = new Project($db);
    }

    public function index() {
        requireRole(['Department Coordinator','Extension Staff','School Coordinator','Campus Director','VP ORIES','Super Admin','Admin']);

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
            $this->model->create($_POST);

            if(function_exists('logActivity')) {
                logActivity($this->db, "Create Monitoring Entry", "Monitoring", "Added monitoring entry.");
            }

            header("Location: index.php?page=monitoring&saved=1");
            exit();
        }

        $projects = $this->projectModel->all();
        $monitoring = $this->model->all();

        include "app/views/monitoring/index.php";
    }

    public function updateStatus() {
        requireRole(['Department Coordinator','Extension Staff','School Coordinator','Campus Director','VP ORIES','Super Admin','Admin']);

        $id = intval($_POST['monitoring_id'] ?? 0);
        $status = $_POST['status'] ?? 'On-going';

        if($id > 0) {
            $this->model->updateStatus($id, $status);

            if(function_exists('logActivity')) {
                logActivity($this->db, "Update Monitoring Status", "Monitoring", "Changed monitoring entry #".$id." to ".$status);
            }
        }

        header("Location: index.php?page=monitoring");
        exit();
    }
}
?>
