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
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');

//Get group
$gid = intval($_GET['id']);
$group_obj = new SocialGroup($gid);
$result = $group_obj->addRequest();

if ($result){
	$sql = "SELECT member_id from ".TABLE_PREFIX."social_groups WHERE id = '$gid'";

	$result_sender = mysql_query($sql, $db);
	$grpadmins = mysql_fetch_row($result_sender);
	$grpadmin = $grpadmins['0'];

	require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
	$sql_notify = "SELECT first_name, last_name, email FROM ".TABLE_PREFIX."members WHERE member_id=$grpadmin";
	$result_notify = mysql_query($sql_notify, $db);
	$row_notify = mysql_fetch_assoc($result_notify);

	if ($row_notify['email'] != '') {
		$body = _AT('notification_group_request', $group_obj->getName() , $_base_href.'mods/social/index_mystart.php');
		$sender = get_display_name($_SESSION['member_id']);
		$mail = new ATutorMailer;
		$mail->AddAddress($row_notify['email'], $sender);
		$mail->FromName = $_config['site_name'];
		$mail->From     = $_config['contact_email'];
		$mail->Subject  = _AT('group_request');
		$mail->Body     = $body;

		if(!$mail->Send()) {
			$msg->addError('SENDING_ERROR');
		}
		unset($mail);
	}

	$msg->addFeedback('JOIN_REQUEST_SENT');
} else {
	$msg->addError('JOIN_REQUEST_FAILED');
}

//Display
header('Location: '.url_rewrite('mods/social/groups/index.php', AT_PRETTY_URL_HEADER));
exit;
?>