<?php
namespace gameme;
use gameme\PHPGamification\DAO;

global $_base_path;
$_user_location	= 'admin';
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
    if($_SESSION['is_admin'] >0){
	    header("Location: ".AT_BASE_HREF."mods/_standard/gameme/index_instructor.php");
	}else{
	    header("Location: ".AT_BASE_HREF."mods/_standard/gameme/index_admin.php");
	}
	exit;
} else if (isset($_POST['submit_yes'])) {
    if($_SESSION['course_id'] > 0){
        $course_id = $_SESSION['course_id'];
    }else{
        $course_id=0;
    }
    $sql = "DELETE FROM %sgm_events WHERE id=%d AND course_id = %d LIMIT 1";
    queryDB($sql, array(TABLE_PREFIX, $_POST['event_id'], $course_id));
    $msg->addFeedback('GM_EVENT_REMOVED');
    if($_SESSION['is_admin'] >0){
	    header("Location: ".AT_BASE_HREF."mods/_standard/gameme/index_instructor.php");
	}else{
	    header("Location: ".AT_BASE_HREF."mods/_standard/gameme/index_admin.php");
	}
	exit;
}
require (AT_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);
$hidden_vars['event_id'] = intval($_GET['id']);
if($_SESSION['is_admin']){
    $msg->addConfirm(array('GM_DELETE_EVENT'), $hidden_vars);
} else {
    $msg->addConfirm(array('GM_DELETE_EVENT_ADMIN'), $hidden_vars);
}
$msg->printConfirm();

require (AT_INCLUDE_PATH.'footer.inc.php'); ?>