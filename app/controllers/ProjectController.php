<?php
require_once "app/models/Project.php";
require_once "app/models/Program.php";
require_once "app/models/Location.php";
require_once "app/models/BarangayLocation.php";

class ProjectController {
    private $model;
    private $programModel;
    private $locationModel;
    private $barangayLocationModel;

    public function __construct($db) {
        $this->model = new Project($db);
        $this->programModel = new Program($db);
        $this->locationModel = new Location($db);
        $this->barangayLocationModel = new BarangayLocation($db);
    }

    public function index() {
        requireRole(['Department Coordinator','Extension Staff','Super Admin','Admin']);

        $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $action = $_POST['form_action'] ?? 'create';

            if($action === 'update') {
                $message = $this->model->update($_POST) ? "Project updated successfully." : "Unable to update project.";
            } elseif($action === 'delete') {
                $message = $this->model->delete($_POST['project_id'] ?? 0)
                    ? "Project deleted successfully."
                    : "Unable to delete project. Remove related monitoring records first.";
            } else {
                $message = $this->model->create($_POST) ? "Project saved successfully." : "Unable to save project.";
            }
        }

        $programs = $this->programModel->all();
        $projects = $this->model->all();

        $locations = $this->locationModel->all();
        $barangayLocations = $this->barangayLocationModel->all();

        include "app/views/projects/index.php";
    }

    public function create() {
        requireRole(['Department Coordinator','Extension Staff','Super Admin','Admin']);

        $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $message = $this->model->create($_POST) ? "Project saved successfully." : "Unable to save project.";
        }

        $programs = $this->programModel->all();
        $projects = $this->model->all();

        $locations = $this->locationModel->all();
        $barangayLocations = $this->barangayLocationModel->all();

        include "app/views/projects/index.php";
    }
}
?>
