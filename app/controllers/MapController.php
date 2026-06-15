<?php
require_once 'app/models/Project.php';
require_once 'app/models/Location.php';
require_once 'app/data/region8_municipalities.php';

class MapController {
    private $m;
    private $locationModel;

    function __construct($db) {
        $this->m = new Project($db);
        $this->locationModel = new Location($db);
    }

    function index() {
        requireAccess(canAccessGisMap());
        $projects = $this->m->all();
        $rankingPagination = paginationParams($this->m->countAll(), 10, 'ranking_page');
        $rankingProjects = $this->m->rankingPaginated($rankingPagination['per_page'], $rankingPagination['offset']);
        $locations = array_merge($this->locationModel->all(), region8MunicipalityFallbackLocations());
        include 'app/views/map/index.php';
    }
}
?>
