<?php
require_once 'app/models/Program.php';

class ProgramController {
    private $model;

    public function __construct($db) {
        $this->model = new Program($db);
    }

    public function index() {
        requireAccess(canAccessPrograms());

        $message = '';
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['form_action'] ?? 'create';

            if($action === 'update') {
                $message = $this->model->update($_POST) ? 'Program updated successfully.' : 'Unable to update program.';
            } elseif($action === 'delete') {
                $message = $this->model->delete($_POST['program_id'] ?? 0)
                    ? 'Program deleted successfully.'
                    : 'Unable to delete program. Remove related projects first.';
            } else {
                $message = $this->model->create($_POST) ? 'Program saved successfully.' : 'Unable to save program.';
            }
        }

        $programs = $this->model->all();
        include 'app/views/programs/index.php';
    }
}
?>
