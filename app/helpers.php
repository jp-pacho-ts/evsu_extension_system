<?php
function requireLogin(){ if(session_status()===PHP_SESSION_NONE) session_start(); if(!isset($_SESSION['user_id'])){ header("Location: index.php?page=login"); exit(); } }
function normalizeRole($role){ $role=strtolower(trim((string)$role)); $a=['admin'=>'admin','superadmin'=>'super admin','super admin'=>'super admin','extension staff'=>'department coordinator','staff'=>'department coordinator','department coordinator'=>'department coordinator','school coordinator'=>'school coordinator','campus director'=>'campus director','campus director / dean'=>'campus director','dean'=>'campus director','extension director'=>'extension director','vp ories'=>'vp ories','faculty'=>'faculty','reviewer'=>'reviewer','approver'=>'reviewer']; return $a[$role]??$role; }
function currentRole(){ return normalizeRole($_SESSION['role']??''); }
function hasRole($roles){ $c=currentRole(); foreach($roles as $r){ if($c===normalizeRole($r)) return true; } return false; }
function requireRole($roles){ requireLogin(); if(!hasRole($roles)){ include "app/views/access_denied.php"; exit(); } }
function roleLandingPage(){ $r=currentRole(); if($r=='department coordinator') return 'index.php?page=projects'; if($r=='faculty') return 'index.php?page=quarterly_reports'; if($r=='reviewer') return 'index.php?page=approvals'; return 'index.php?page=dashboard'; }
function logActivity($db,$action,$module,$description=''){ if(!$db) return; if(session_status()===PHP_SESSION_NONE) session_start(); $uid=isset($_SESSION['user_id'])?intval($_SESSION['user_id']):"NULL"; $action=$db->real_escape_string($action); $module=$db->real_escape_string($module); $description=$db->real_escape_string($description); $ip=$db->real_escape_string($_SERVER['REMOTE_ADDR']??''); @$db->query("INSERT INTO activity_logs(user_id,action,module,description,ip_address) VALUES($uid,'$action','$module','$description','$ip')"); }
function notifyUser($db,$uid,$title,$msg,$link=''){ if(!$db||!$uid) return; $uid=intval($uid); $title=$db->real_escape_string($title); $msg=$db->real_escape_string($msg); $link=$db->real_escape_string($link); @$db->query("INSERT INTO notifications(user_id,title,message,link) VALUES($uid,'$title','$msg','$link')"); }
function notifyUsersByRole($db,$roles,$title,$msg,$link=''){ if(!$db) return; $arr=[]; foreach($roles as $r) $arr[]="'".$db->real_escape_string($r)."'"; $rs=@$db->query("SELECT id FROM users WHERE role IN (".implode(',',$arr).") AND (account_status='Active' OR account_status IS NULL)"); if($rs) while($u=$rs->fetch_assoc()) notifyUser($db,$u['id'],$title,$msg,$link); }
function unreadNotificationCount($db){ if(!$db) return 0; if(session_status()===PHP_SESSION_NONE) session_start(); if(!isset($_SESSION['user_id'])) return 0; $uid=intval($_SESSION['user_id']); $rs=@$db->query("SELECT COUNT(*) total FROM notifications WHERE user_id=$uid AND is_read=0"); return $rs?intval($rs->fetch_assoc()['total']):0; }
function statusBadge($s){ return ['On-going'=>'primary','Completed'=>'success','Terminated'=>'dark','Inactive'=>'warning','Expired'=>'danger'][$s]??'secondary'; }
function statusColor($s){ return ['On-going'=>'#2563eb','Completed'=>'#16a34a','Terminated'=>'#111827','Inactive'=>'#f59e0b','Expired'=>'#dc2626'][$s]??'#6b7280'; }
function computeESFI($m,$p){ return round(($m*0.70)+(($p/100)*0.30),2); }
function esfiInterpretation($e){ if($e<1.50)return "Low Service Coverage"; if($e<3.00)return "Moderate Service Distribution"; return "High Service Concentration"; }
function esfiBadge($e){ if($e<1.50)return "secondary"; if($e<3.00)return "warning"; return "danger"; }
function decisionSupport($s){
    return [
        'On-going' => 'Continue monitoring implementation progress and document the next action.',
        'Completed' => 'Validate outcomes, secure terminal documentation, and prepare closeout records.',
        'Inactive' => 'Check implementation blockers and prepare a reactivation plan.',
        'Expired' => 'Review extension requirements or move the project toward closeout.',
        'Terminated' => 'Document the termination reason and archive supporting decisions.'
    ][$s] ?? "Review project status.";
}

function userHasExtensionServices($db, $userId = null) {
    if(!$db) return false;
    ensureUserExtensionServicesColumn($db);
    if(session_status() === PHP_SESSION_NONE) session_start();
    $uid = $userId !== null ? intval($userId) : intval($_SESSION['user_id'] ?? 0);
    if($uid <= 0) return false;

    $rs = @$db->query("SELECT has_extension_services FROM users WHERE id=$uid LIMIT 1");
    if(!$rs) return false;
    $row = $rs->fetch_assoc();
    return intval($row['has_extension_services'] ?? 0) === 1;
}

function ensureUserExtensionServicesColumn($db) {
    if(!$db) return false;
    $rs = @$db->query("SHOW COLUMNS FROM users LIKE 'has_extension_services'");
    if($rs && $rs->num_rows > 0) return true;
    @$db->query("ALTER TABLE users ADD COLUMN has_extension_services tinyint(1) DEFAULT 0 AFTER signatory_title");
    $rs = @$db->query("SHOW COLUMNS FROM users LIKE 'has_extension_services'");
    return $rs && $rs->num_rows > 0;
}

function pendingApprovalCount($db) {
    if(!$db) return 0;
    if (session_status() === PHP_SESSION_NONE) session_start();
    $role = currentRole();
    $expected = '';

    if($role == 'department coordinator') $expected = 'Submitted';
    elseif($role == 'school coordinator') $expected = 'Department Coordinator Approved';
    elseif($role == 'campus director') $expected = 'School Coordinator Approved';
    elseif($role == 'vp ories') $expected = 'Campus Director Approved';
    else $expected = '';

    if($expected == '') return 0;
    $expected = $db->real_escape_string($expected);
    $rs = @$db->query("SELECT COUNT(*) AS total FROM quarterly_reports WHERE submission_status='$expected'");
    if($rs) return intval($rs->fetch_assoc()['total']);
    return 0;
}
?>
