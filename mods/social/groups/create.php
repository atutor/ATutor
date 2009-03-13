<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');


// Get activities	
$act_obj = new Activity();
$activities = $act_obj->getActivities($id);

// Get social group class
$social_groups = new SocialGroups();
$my_groups = $social_groups->getMemberGroups($_SESSION['member_id']);


if (isset($_POST['create'])){
	$social_groups->addGroup($_POST['type'], $_POST['name'], $_POST['description'], $_POST['logo']);
}

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('my_groups', $my_groups);
$savant->assign('group_types', $social_groups->getAllGroupType());
$savant->display('social_create.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>