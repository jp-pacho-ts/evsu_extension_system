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
        requireAccess(canAccessMonitoringRecords());

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['form_action'] ?? 'create';
            $redirect = $this->safeMonitoringRedirect($_POST['redirect'] ?? 'index.php?page=monitoring');

            if($action === 'update') {
                $id = intval($_POST['monitoring_id'] ?? 0);
                $ok = $this->model->update($id, $_POST);

                if($ok && function_exists('logActivity')) {
                    logActivity($this->db, "Update Monitoring Entry", "Monitoring", "Updated monitoring entry #".$id);
                }

                header("Location: ".$this->withStatusFlag($redirect, $ok ? 'updated' : 'error'));
                exit();
            }

            if($action === 'delete') {
                $id = intval($_POST['monitoring_id'] ?? 0);
                $ok = $this->model->delete($id);

                if($ok && function_exists('logActivity')) {
                    logActivity($this->db, "Delete Monitoring Entry", "Monitoring", "Deleted monitoring entry #".$id);
                }

                header("Location: ".$this->withStatusFlag($redirect, $ok ? 'deleted' : 'error'));
                exit();
            }

            if(isset($_POST['project_id'])) {
                $ok = $this->model->create($_POST);

                if($ok && function_exists('logActivity')) {
                    logActivity($this->db, "Create Monitoring Entry", "Monitoring", "Added monitoring entry.");
                }

                header("Location: ".$this->withStatusFlag($redirect, $ok ? 'saved' : 'error'));
                exit();
            }
        }

        $monitoringFilters = $this->monitoringFilters();
        $filterOptions = $this->model->filterOptions();
        $projects = $this->projectModel->all();
        $pagination = paginationParams($this->model->countFiltered($monitoringFilters), 10);
        $monitoring = $this->model->paginatedFiltered($monitoringFilters, $pagination['per_page'], $pagination['offset']);

        include "app/views/monitoring/index.php";
    }

    public function updateStatus() {
        requireAccess(canAccessMonitoringRecords());

        $id = intval($_POST['monitoring_id'] ?? 0);
        $status = $_POST['status'] ?? 'On-going';

        if($id > 0) {
            $this->model->updateStatus($id, $status);

            if(function_exists('logActivity')) {
                logActivity($this->db, "Update Monitoring Status", "Monitoring", "Changed monitoring entry #".$id." to ".$status);
            }
        }

        $redirect = $this->safeMonitoringRedirect($_POST['redirect'] ?? 'index.php?page=monitoring');

        header("Location: ".$redirect);
        exit();
    }

    private function safeMonitoringRedirect($redirect) {
        $redirect = trim((string)$redirect);
        $parts = parse_url($redirect);
        if($redirect === '' || $parts === false || ($parts['path'] ?? '') !== 'index.php') {
            return 'index.php?page=monitoring';
        }

        parse_str($parts['query'] ?? '', $params);
        if(($params['page'] ?? '') !== 'monitoring') {
            return 'index.php?page=monitoring';
        }

        return 'index.php?'.http_build_query($params);
    }

    private function withStatusFlag($redirect, $flag) {
        $join = strpos($redirect, '?') === false ? '?' : '&';
        return $redirect.$join.$flag.'=1';
    }

    private function monitoringFilters() {
        $keys = ['q', 'status', 'campus', 'school', 'date_from', 'date_to'];
        $filters = [];

        foreach($keys as $key) {
            $filters[$key] = trim((string)($_GET[$key] ?? ''));
        }

        return $filters;
    }
}
?>
