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
// $Id: index_admin.php 8406 2009-04-01 20:38:44Z hwong $
$_user_location	= 'admin';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
include(AT_SOCIAL_INCLUDE.'classes/Application.class.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

//initialization
$apps = new Applications();

//handles deletion
if (isset($_POST['delete'])){
	if (isset($_POST['apps']) && !empty($_POST['apps'])){
		//need confirm box
		$apps->deleteApplications($_POST['apps']);
		$msg->addFeedback('GADGET_DELETED_SUCCESSFULLY');		
	} else {
		//cannot be empty
		$msg->addError('GADGET_DELETED_CANNOT_BE_EMPTY');
	}
}

//data
$all_apps = $apps->listApplications();

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('all_apps', $all_apps);
$savant->display('admin/delete_applications.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>