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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
include(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');

//handles message deletion
$gid = intval($_REQUEST['gid']);
$mid = intval($_REQUEST['delete']);


// delete group's messages
if($gid > 0 && $mid > 0){	
	$group = new SocialGroup($gid);
	if ($_POST['submit_yes']){
		$result = $group->removeMessage($mid, $_SESSION['member_id']);
		if($result){
			$msg->addFeedback('MESSAGE_DELETE_SUCCESSFULLY');
		} else {
			$msg->addError('CANT_DELETE_MESSAGE');
		}
		header('Location: '.url_rewrite(AT_SOCIAL_BASENAME.'groups/view.php?id='.$gid, AT_PRETTY_URL_HEADER));
		exit;
	} elseif ($_POST['submit_no']){
		$msg->addFeedback('CANCELLED');
		header('Location: '.url_rewrite(AT_SOCIAL_BASENAME.'groups/view.php?id='.$gid, AT_PRETTY_URL_HEADER));
		exit;
	}
	$hidden_vars['gid'] = $gid;
	$hidden_vars['delete'] = $mid;
	$message = $group->getMessage($mid, $_SESSION['member_id']);
	if ($message==false){
		$msg->addError('INVALID');	//users tries to delete message that aren't theirs.
	} else {
		$msg->addConfirm(array('DELETE', $group->getMessage($mid, $_SESSION['member_id'])), $hidden_vars);
	}
}

include(AT_INCLUDE_PATH.'header.inc.php');
$msg->printConfirm();
include(AT_INCLUDE_PATH.'footer.inc.php');
?>