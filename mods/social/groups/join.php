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

//Get group
$gid = intval($_GET['id']);
$group_obj = new SocialGroup($gid);
$result = $group_obj->addRequest();

if ($result){
	$msg->addFeedback('JOIN_REQUEST_SENT');
} else {
	$msg->addError('JOIN_REQUEST_FAILED');
}

//Display
header('Location: '.url_rewrite('mods/social/groups/index.php', AT_PRETTY_URL_HEADER));
exit;
?>