<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

//Get group
$gid = intval($_REQUEST['id']);
$group_obj = new SocialGroup($gid);

//check if this group is valid
if (!$group_obj->isValid()){
	$msg->addError('GROUP_HAS_BEEN_REMOVED');
	header('Location: '.url_rewrite(AT_SOCIAL_BASENAME.'groups/index.php', AT_PRETTY_URL_HEADER));
	exit;
}

//remove group member
if (isset($_GET['remove']) && $_GET['remove']==1){
	$group_obj->removeMember($_SESSION['member_id']);
	$msg->addFeedback('LEFT_GROUP_SUCCESSFULLY');
	header('Location: '.url_rewrite(AT_SOCIAL_BASENAME.'groups/index.php', AT_PRETTY_URL_HEADER));
	exit;
}

//submit message
if (isset($_POST['submit'])){
	$body = $_POST['msg_body'];
	if ($body!=''){
		$group_obj->addMessage($body);
	}
}

// delete group
if($_GET['delete'] == "confirm"){
	//$msg->addConfirm('DELETE_GROUP', $group_obj->getName());
	//$msg->addConfirm('DELETE_GROUP', );
	$hidden_vars['id'] = $gid;
	$msg->addConfirm(array('DELETE_GROUP', $group_obj->getName()), $hidden_vars);
	header('Location: '.url_rewrite(AT_SOCIAL_BASENAME."groups/view.php?id=".$gid, AT_PRETTY_URL_HEADER));
	exit;

}else if($_POST['submit_yes']){
	header('Location: '.url_rewrite(AT_SOCIAL_BASENAME."groups/delete.php?id=".$gid, AT_PRETTY_URL_HEADER));
	exit;
}else if($_POST['submit_no']){
	$msg->addFeedback('CANCELLED');
	header('Location: '.url_rewrite(AT_SOCIAL_BASENAME."groups/view.php?id=".$gid, AT_PRETTY_URL_HEADER));
	exit;
}

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('social/pubmenu.tmpl.php');
$savant->assign('group_invitations',getGroupInvitations());
$savant->assign('group_obj', $group_obj);
$savant->display('social/sgroup_view.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>