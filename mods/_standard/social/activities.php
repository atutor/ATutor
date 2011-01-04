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
// $Id: activities.php 10055 2010-06-29 20:30:24Z cindy $
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
//require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyObject.class.php');
//require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyController.class.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME. 'module.css'; // use a custom stylesheet

$actvity_obj = new Activity();

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('pubmenu.tmpl.php');
$savant->assign('activities', $actvity_obj->getFriendsActivities($_SESSION['member_id'], true));
$savant->display('activities.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>