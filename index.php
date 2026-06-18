<?php
session_start();
require_once "app/config/database.php";
require_once "app/helpers.php";

$db = (new Database())->connect();
$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'login':
        require_once "app/controllers/AuthController.php";
        (new AuthController($db))->login();
        break;
    case 'logout':
        require_once "app/controllers/AuthController.php";
        (new AuthController($db))->logout();
        break;
    case 'dashboard':
        require_once "app/controllers/DashboardController.php";
        (new DashboardController($db))->index();
        break;
    case 'programs':
        require_once "app/controllers/ProgramController.php";
        (new ProgramController($db))->index();
        break;
    case 'projects':
        require_once "app/controllers/ProjectController.php";
        (new ProjectController($db))->index();
        break;
    case 'update_monitoring_status':
        require_once "app/controllers/MonitoringController.php";
        (new MonitoringController($db))->updateStatus();
        break;

    case 'monitoring':
        require_once "app/controllers/MonitoringController.php";
        (new MonitoringController($db))->index();
        break;
    case 'map':
        require_once "app/controllers/MapController.php";
        (new MapController($db))->index();
        break;
    case 'report':
        require_once "app/controllers/ReportController.php";
        (new ReportController($db))->index();
        break;
    case 'edit_quarterly_report':
        require_once "app/controllers/QuarterlyReportController.php";
        (new QuarterlyReportController($db))->edit();
        break;

    case 'recall_quarterly_report':
        require_once "app/controllers/QuarterlyReportController.php";
        (new QuarterlyReportController($db))->recall();
        break;

    case 'delete_quarterly_report':
        require_once "app/controllers/QuarterlyReportController.php";
        (new QuarterlyReportController($db))->delete();
        break;

    case 'quarterly_reports':
        require_once "app/controllers/QuarterlyReportController.php";
        (new QuarterlyReportController($db))->index();
        break;
    case 'submit_quarterly_report':
        require_once "app/controllers/QuarterlyReportController.php";
        (new QuarterlyReportController($db))->submit();
        break;

    case 'view_quarterly_report':
        require_once "app/controllers/QuarterlyReportController.php";
        (new QuarterlyReportController($db))->show();
        break;
    case 'approval_action':
        require_once "app/controllers/ApprovalController.php";
        (new ApprovalController($db))->action();
        break;

    case 'approvals':
        require_once "app/controllers/ApprovalController.php";
        (new ApprovalController($db))->index();
        break;
    case 'users':
        require_once "app/controllers/UserManagementController.php";
        (new UserManagementController($db))->index();
        break;
    case 'sdgs':
        require_once "app/controllers/SdgController.php";
        (new SdgController($db))->index();
        break;

    case 'notifications':
        require_once "app/controllers/NotificationController.php";
        (new NotificationController($db))->index();
        break;
    case 'notification_action':
        require_once "app/controllers/NotificationController.php";
        (new NotificationController($db))->action();
        break;

    case 'logs':
        require_once "app/controllers/LogController.php";
        (new LogController($db))->index();
        break;

    case 'approval_center':
        require_once "app/controllers/ApprovalCenterController.php";
        (new ApprovalCenterController($db))->index();
        break;

    default:
        header("Location: index.php?page=login");
        exit();
}
?>
