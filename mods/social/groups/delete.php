<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');

// Get social group class
$social_groups = new SocialGroups();

// Get this group
$id = intval($_REQUEST['id']);  //make sure $_GET and $_POST don't overlap the use of 'id'
$sg = new SocialGroup($id);
$sgs = new SocialGroups();

//validate if this user is the administrator of the group
if ($sg->getUser() != $_SESSION['member_id']){
	$msg->addError('delete_group');
	header('Location: index.php');
	exit;
}

//delete group 
$sgs->removeGroup($id);
header('Location: '.url_rewrite('mods/social/groups/index.php', AT_PRETTY_URL_HEADER));
exit;

//Display
/*
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('group_obj', $group);
$savant->assign('group_types', $social_groups->getAllGroupType());
$savant->display('sgroup_edit.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
*/
?>