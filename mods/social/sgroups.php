<?php
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// Get activities	
$act_obj = new Activity();
$activities = $act_obj->getActivities($id);


//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('sgroups.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
