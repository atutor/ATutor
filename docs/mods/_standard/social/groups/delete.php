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
// $Id: delete.php 8485 2009-05-25 20:50:55Z hwong $
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');


// Get social group class
$social_groups = new SocialGroups();

// Get this group
$id = intval($_REQUEST['id']);  //make sure $_GET and $_POST don't overlap the use of 'id'
$sg = new SocialGroup($id);
$sgs = new SocialGroups();

//validate if this user is the administrator of the group
if ($sg->getUser() != $_SESSION['member_id']){
	$msg->addError('CANT_DELETE_GROUP');
	header('Location: index.php');
	exit;
}

//delete group 
$msg->addFeedback('GROUP_DELETED');
$sgs->removeGroup($id);
header('Location: '.url_rewrite(AT_SOCIAL_BASENAME.'groups/index.php', AT_PRETTY_URL_HEADER));
exit;

//Display
/*
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('group_obj', $group);
$savant->assign('group_types', $social_groups->getAllGroupType());
$savant->display('sgroup_edit.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
*/
?>