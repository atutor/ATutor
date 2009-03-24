<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');

//Get group
$gid = intval($_GET['id']);
$group_obj = new SocialGroup($gid);

//check if this group is valid
if (!$group_obj->isValid()){
	$msg->addError('group_has_been_removed');
	header('Location: '.url_rewrite('mods/social/groups/index.php', AT_PRETTY_URL_HEADER));
	exit;
}

//remove group member
if (isset($_GET['remove']) && $_GET['remove']==1){
	$group_obj->removeMember($_SESSION['member_id']);
}

//submit message
if (isset($_POST['submit'])){
	$body = $_POST['msg_body'];
	if ($body!=''){
		$group_obj->addMessage($body);
	}
}

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('groupsInvitations',getGroupInvitations());
$savant->assign('group_obj', $group_obj);
$savant->display('sgroup_view.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>