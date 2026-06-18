<?php
require_once "app/models/Sdg.php";

class SdgController {
    private $model;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new Sdg($db);
    }

    public function index() {
        requireRole(['Super Admin','Admin']);

        $message = "";

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $action = $_POST['form_action'] ?? 'create';

            if($action === 'update') {
                $message = $this->model->update($_POST) ? "SDG updated successfully." : "Unable to update SDG.";
                logActivity($this->db, 'Update SDG', 'SDG Management', 'ID '.($_POST['sdg_id'] ?? ''));
            } else {
                $message = $this->model->create($_POST) ? "SDG added successfully." : "Unable to add SDG.";
                logActivity($this->db, 'Create SDG', 'SDG Management', $_POST['title'] ?? '');
            }
        }

        $sdgs = $this->model->all();
        include "app/views/sdgs/index.php";
    }
}
?>
