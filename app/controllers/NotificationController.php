<?php
require_once "app/models/Notification.php";

class NotificationController {
    private $m;

    function __construct($db) {
        $this->m = new Notification($db);
    }

    function index() {
        requireLogin();
        if(isset($_GET['read'])) $this->m->markRead($_GET['read'], $_SESSION['user_id']);
        if(isset($_GET['delete'])) $this->m->delete($_GET['delete'], $_SESSION['user_id']);
        if(isset($_GET['mark_all'])) $this->m->markAllRead($_SESSION['user_id']);
        $notifications = $this->m->forUser($_SESSION['user_id']);
        include "app/views/notifications/index.php";
    }

    function action() {
        requireLogin();
        $uid = intval($_SESSION['user_id'] ?? 0);
        $action = $_POST['notification_action'] ?? $_GET['notification_action'] ?? '';
        $id = intval($_POST['notification_id'] ?? $_GET['notification_id'] ?? 0);
        $redirect = $this->safeRedirect($_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php?page=dashboard');

        if($action == 'mark_all') {
            $this->m->markAllRead($uid);
        } elseif($action == 'read' && $id > 0) {
            $this->m->markRead($id, $uid);
        } elseif($action == 'delete' && $id > 0) {
            $this->m->delete($id, $uid);
        } elseif($action == 'open' && $id > 0) {
            $notification = $this->m->findForUser($id, $uid);
            if($notification) {
                $this->m->markRead($id, $uid);
                $link = trim((string)($notification['link'] ?? ''));
                if($this->isSafeLocalLink($link)) $redirect = $link;
            }
        }

        header('Location: '.$redirect);
        exit();
    }

    private function safeRedirect($redirect) {
        $redirect = trim((string)$redirect);
        if(!$this->isSafeLocalLink($redirect)) return 'index.php?page=dashboard';
        return $redirect;
    }

    private function isSafeLocalLink($link) {
        $link = trim((string)$link);
        if($link === '') return false;
        if(str_contains($link, "\r") || str_contains($link, "\n")) return false;
        if(str_starts_with($link, '//')) return false;
        if(preg_match('/^[a-z][a-z0-9+.-]*:/i', $link)) return false;
        return true;
    }
}
?>
