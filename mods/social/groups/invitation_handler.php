<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');

if (isset($_GET['action'])){
	$id = intval($_GET['id']);
	if ($id > 0){
		if ($_GET['action']=='accept'){
			acceptGroupInvitation($id);
		} elseif ($_GET['action']=='reject'){
			rejectGroupInvitation($id);
		}
	}
	$msg->addFeedback('GROUP_JOINED');
	header('Location: '.url_rewrite('mods/social/groups/view.php?id='.$id), AT_PRETTY_URL_HEADER);
	exit;
}

header('Location: '.url_rewrite('mods/social/groups/index.php', AT_PRETTY_URL_HEADER));
exit;
?>