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
            $message = $this->model->create($_POST) ? "Project saved successfully." : "Unable to save project.";
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
