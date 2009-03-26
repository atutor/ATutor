<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');

//Get group
$gid = intval($_REQUEST['id']);
$group_obj = new SocialGroup($gid);

//Todo: Implements the add group request feature
//adds to a 

addGroupRequest($_SESSION['member_id'], $gid);
$msg->addFeedback('INVITATION_SENT');

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('group_obj', $group_obj);
$savant->display('sgroup_invite.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>