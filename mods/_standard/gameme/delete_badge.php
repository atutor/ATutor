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
    }else if($course_id == -1){
        $course_id=0;
    }
    // remove the badge file
    $sql = "SELECT image_url FROM %sgm_badges WHERE course_id = %d AND id=%d";
    $badge_file = queryDB($sql, array(TABLE_PREFIX,  $course_id, $_POST['badge_id']), TRUE);
    
    $badge_file_array = explode('/',$badge_file['image_url']);

    array_shift($badge_file_array);
    $badge_file_stem = implode('/',$badge_file_array);

    unlink(AT_CONTENT_DIR.$badge_file_stem);
    
    // remove the badge from the DB
    $sql = "DELETE FROM %sgm_badges WHERE id=%d AND course_id = %d LIMIT 1";
    queryDB($sql, array(TABLE_PREFIX, $_POST['badge_id'], $course_id));
    
    $msg->addFeedback('GM_BADGE_REMOVED');
    
    if($_SESSION['is_admin'] >0){
	    header("Location: ".AT_BASE_HREF."mods/_standard/gameme/index_instructor.php");
	}else{
	    header("Location: ".AT_BASE_HREF."mods/_standard/gameme/index_admin.php");
	}
	exit;
}
require (AT_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);
$hidden_vars['badge_id'] = intval($_GET['id']);

$msg->addConfirm(array('GM_DELETE_BADGE'), $hidden_vars);

$msg->printConfirm();

require (AT_INCLUDE_PATH.'footer.inc.php'); ?>