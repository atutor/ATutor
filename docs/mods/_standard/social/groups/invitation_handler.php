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
// $Id: invitation_handler.php 8485 2009-05-25 20:50:55Z hwong $
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');

if (isset($_GET['invitation']) || isset($_GET['request'])){
	$id = intval($_GET['id']);
	$sender_id = intval($_GET['sender_id']);
	$status = -1;
	$group_obj = new SocialGroup($id);
	if ($id > 0){
		//handle invitations
		if ($_GET['invitation']=='accept'){

			$sql = "SELECT sender_id from ".TABLE_PREFIX."social_groups_invitations WHERE  member_id = '$_SESSION[member_id]' AND group_id = '$id'";

			$result_sender = mysql_query($sql, $db);
			$sender = mysql_fetch_array($result_sender);

			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			foreach ($sender as $sid){

				$sql_notify = "SELECT first_name, last_name, email FROM ".TABLE_PREFIX."members WHERE member_id=$sid";
				$result_notify = mysql_query($sql_notify, $db);
				$row_notify = mysql_fetch_assoc($result_notify);
				//require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
				if ($row_notify['email'] != '') {
					//require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
					$body = _AT('notification_group_invite_accepted', get_display_name($_SESSION['member_id']),$group_obj->getName() , $_base_href.AT_SOCIAL_BASENAME.'index_mystart.php');
					$sender = get_display_name($_SESSION['member_id']);
					$mail = new ATutorMailer;
					$mail->AddAddress($row_notify['email'], $sender);
					$mail->FromName = $_config['site_name'];
					$mail->From     = $_config['contact_email'];
					$mail->Subject  = _AT('group_invitation_accepted');
					$mail->Body     = $body;
		
					if(!$mail->Send()) {
						$msg->addError('SENDING_ERROR');
					}
					unset($mail);
				}
			}

			acceptGroupInvitation($id);
			$status = 1;
		} elseif ($_GET['invitation']=='reject'){
			rejectGroupInvitation($id);
			$status = 2;
		}

		//handle requests (requests to join a group from some member)
		if ($sender_id > 0){
			if ($_GET['request']=='accept'){

			$sql = "SELECT sender_id from ".TABLE_PREFIX."social_groups_requests WHERE  member_id = '$_SESSION[member_id]' AND group_id = '$id'";

			$result_sender = mysql_query($sql, $db);
			$sender = mysql_fetch_array($result_sender);

			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			foreach ($sender as $sid){

				$sql_notify = "SELECT first_name, last_name, email FROM ".TABLE_PREFIX."members WHERE member_id=$sid";
				$result_notify = mysql_query($sql_notify, $db);
				$row_notify = mysql_fetch_assoc($result_notify);

				if ($row_notify['email'] != '') {
					$body = _AT('notification_group_request_accepted', $group_obj->getName() , $_base_href.AT_SOCIAL_BASENAME.'index_mystart.php');
					$sender = get_display_name($_SESSION['member_id']);
					$mail = new ATutorMailer;
					$mail->AddAddress($row_notify['email'], $sender);
					$mail->FromName = $_config['site_name'];
					$mail->From     = $_config['contact_email'];
					$mail->Subject  = _AT('group_request_accepted');
					$mail->Body     = $body;
		
					if(!$mail->Send()) {
						$msg->addError('SENDING_ERROR');
					}
					unset($mail);
				}
			}
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
	header('Location: '.url_rewrite(AT_SOCIAL_BASENAME.'groups/view.php?id='.$id, AT_PRETTY_URL_HEADER));
	exit;
}

header('Location: '.url_rewrite(AT_SOCIAL_BASENAME.'groups/index.php', AT_PRETTY_URL_HEADER));
exit;
?>