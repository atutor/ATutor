<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
$_custom_css = $_base_path . 'mods/social/module.css'; // use a custom stylesheet

//Get group
$gid = intval($_REQUEST['id']);
$group_obj = new SocialGroup($gid);

//handles submit
if (isset($_POST['inviteMember']) && isset($_POST['new_members'])){
//	debug($_POST['new_members']);
	//add to request table
	foreach ($_POST['new_members'] as $k=>$v){
		$k = intval($k);

		//TODO, move the following function from friends.inc.php to the SocialGroup.class object
		addGroupInvitation($k, $gid);
	}
	$msg->addFeedback('INVITATION_SENT');
}

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('group_obj', $group_obj);
$savant->display('sgroup_invite.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>