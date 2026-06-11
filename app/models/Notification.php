<?php
class Notification {
    private $c;

    function __construct($db) {
        $this->c = $db;
    }

    function forUser($u, $limit = 0) {
        $u = intval($u);
        $limit = intval($limit);
        $limitSql = $limit > 0 ? " LIMIT $limit" : "";
        $r = $this->c->query("SELECT * FROM notifications WHERE user_id=$u ORDER BY created_at DESC".$limitSql);
        $d = [];
        if($r) while($x = $r->fetch_assoc()) $d[] = $x;
        return $d;
    }

    function findForUser($id, $u) {
        $id = intval($id);
        $u = intval($u);
        $r = $this->c->query("SELECT * FROM notifications WHERE id=$id AND user_id=$u LIMIT 1");
        return $r ? $r->fetch_assoc() : null;
    }

    function markRead($id, $u) {
        $id = intval($id);
        $u = intval($u);
        return $this->c->query("UPDATE notifications SET is_read=1 WHERE id=$id AND user_id=$u");
    }

    function markAllRead($u) {
        $u = intval($u);
        return $this->c->query("UPDATE notifications SET is_read=1 WHERE user_id=$u");
    }

    function delete($id, $u) {
        $id = intval($id);
        $u = intval($u);
        return $this->c->query("DELETE FROM notifications WHERE id=$id AND user_id=$u");
    }
}
?>
