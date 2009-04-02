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
		if ($gid != ''){
			$sql_notify = "SELECT first_name, last_name, email FROM ".TABLE_PREFIX."members WHERE member_id=$k";
			$result_notify = mysql_query($sql_notify, $db);
			$row_notify = mysql_fetch_assoc($result_notify);

			if ($row_notify['email'] != '') {
				require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
				$body = _AT('notification_group_invite', get_display_name($_SESSION['member_id']),$group_obj->getName() , $_base_href.'mods/social/index_mystart.php');
				$sender = get_display_name($_SESSION['member_id']);
				$mail = new ATutorMailer;
				$mail->AddAddress($row_notify['email'], $sender);
				$mail->FromName = $_config['site_name'];
				$mail->From     = $_config['contact_email'];
				$mail->Subject  = _AT('group_invitation');
				$mail->Body     = $body;
	
				if(!$mail->Send()) {
					$msg->addError('SENDING_ERROR');
				}
				unset($mail);
			}
		//TODO, move the following function from friends.inc.php to the SocialGroup.class object
		addGroupInvitation($k, $gid);
		}
	}
	$msg->addFeedback('INVITATION_SENT');
}

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('group_obj', $group_obj);
$savant->display('sgroup_invite.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>