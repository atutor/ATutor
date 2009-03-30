<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');

if (isset($_GET['invitation']) || isset($_GET['request'])){
	$id = intval($_GET['id']);
	$sender_id = intval($_GET['sender_id']);
	if ($id > 0){
		//handle invitations
		if ($_GET['invitation']=='accept'){
			acceptGroupInvitation($id);
		} elseif ($_GET['invitation']=='reject'){
			rejectGroupInvitation($id);
		}

		//handle requests (requests to join a group from some member)
		if ($sender_id > 0){
			if ($_GET['request']=='accept'){
				acceptGroupRequest($id, $sender_id);
			} elseif ($_GET['request']=='reject'){
				rejectGroupRequest($id, $sender_id);
			}
		}
	}
	$msg->addFeedback('GROUP_JOINED');
	header('Location: '.url_rewrite('mods/social/groups/view.php?id='.$id), AT_PRETTY_URL_HEADER);
	exit;
}

header('Location: '.url_rewrite('mods/social/groups/index.php', AT_PRETTY_URL_HEADER));
exit;
?>