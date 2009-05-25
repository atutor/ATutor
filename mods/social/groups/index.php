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
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

//paginator settings
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * SOCIAL_GROUP_MAX) + 1;
$offset = ($page-1) * SOCIAL_GROUP_MAX;

// Get activities	
$act_obj = new Activity();
$activities = $act_obj->getActivities($id);

// Get social group class
$social_group = new SocialGroups();
$my_groups = $social_group->getMemberGroups($_SESSION['member_id']);	//to get the size
$num_pages = sizeof($my_groups)/SOCIAL_GROUP_MAX;
$my_groups = $social_group->getMemberGroups($_SESSION['member_id'], $offset);

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('pubmenu.tmpl.php');
print_paginator($page, $num_pages, '', 1); 
$savant->assign('my_groups', $my_groups);
$savant->display('sgroups.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
