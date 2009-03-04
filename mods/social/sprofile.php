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

// Get profile object
$id = intval($_GET['id']);
if ($id=='' || $id==0){
	$id = $_SESSION['member_id'];
}

// Get member friends
$friends = getFriends($id);

// Get activities	
$act_obj = new Activity();
$activities = $act_obj->getActivities($id);

//Member object
$profile = new Member($id);

//Privacy Controller
$pc = new PrivacyController();
$privacy_obj = $pc->getPrivacyObject($id);
$relationship = $pc->getRelationship($id);

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('scope', ($id!=$_SESSION['member_id']) ? 'viewer' : 'owner');
$savant->assign('profile', $profile->getDetails());
$savant->assign('friends', $friends);
$savant->assign('activities', $activities);
$savant->display('sprofile.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>