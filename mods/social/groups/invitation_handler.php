<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');

if (isset($_GET['invitation']) || isset($_GET['request'])){
	$id = intval($_GET['id']);
	$sender_id = intval($_GET['sender_id']);
	$status = -1;
	if ($id > 0){
		//handle invitations
		if ($_GET['invitation']=='accept'){
			acceptGroupInvitation($id);
			$status = 1;
		} elseif ($_GET['invitation']=='reject'){
			rejectGroupInvitation($id);
			$status = 2;
		}

		//handle requests (requests to join a group from some member)
		if ($sender_id > 0){
			if ($_GET['request']=='accept'){
				acceptGroupRequest($id, $sender_id);
				$status = 3;
			} elseif ($_GET['request']=='reject'){
				rejectGroupRequest($id, $sender_id);
				$status = 4;
			}
		}
	}

	switch($status){
		case 1:
			$msg->addFeedback('ACCEPT_GROUP_INVITATION');
			break;
		case 2:
			$msg->addFeedback('REJECT_GROUP_INVITATION');
			break;
		case 3:
			$msg->addFeedback('ACCEPT_GROUP_REQUEST');
			break;
		case 4:
			$msg->addFeedback('REJECT_GROUP_REQUEST');
			break;
		default:
			break;
	}
	header('Location: '.url_rewrite('mods/social/groups/view.php?id='.$id, AT_PRETTY_URL_HEADER));
	exit;
}

header('Location: '.url_rewrite('mods/social/groups/index.php', AT_PRETTY_URL_HEADER));
exit;
?>