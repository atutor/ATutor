<?php
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');

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

// Get social group class
$social_group = new SocialGroups();
$my_groups = $social_group->getMemberGroups($_SESSION['member_id']);


//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('my_groups', $my_groups);
$savant->display('sgroups.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
