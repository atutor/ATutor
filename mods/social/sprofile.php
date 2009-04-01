<?php
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyController.class.php');
require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyObject.class.php');
$_custom_css = $_base_path . 'mods/social/module.css'; // use a custom stylesheet

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

// Member object
$profile = new Member($id);

// Privacy Controller
$pc = new PrivacyController();
$privacy_obj = $pc->getPrivacyObject($id);
if ($privacy_obj==null){
	//no such person
	//add error and redirect back to is own page?
//	header('Location: sprofile.php');
//	exit;	
} else {
	$relationship = $pc->getRelationship($id);
	$profile_prefs = $privacy_obj->getProfile();
}

// Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('scope', ($id!=$_SESSION['member_id']) ? 'viewer' : 'owner');
$savant->assign('profile', $profile->getDetails());
$savant->assign('education', $profile->getEducation());
$savant->assign('position', $profile->getPosition());
$savant->assign('friends', $friends);
$savant->assign('activities', $activities);
$savant->assign('prefs', $profile_prefs);
$savant->assign('relationship', $relationship);
$savant->display('sprofile.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>