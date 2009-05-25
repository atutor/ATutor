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
// $Id: privacy_settings.php 8423 2009-04-03 20:04:55Z hwong $
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
include(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyObject.class.php');
include(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyController.class.php');
include(AT_SOCIAL_INCLUDE.'classes/Application.class.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$controller = new PrivacyController();
$private_obj = $controller->getPrivacyObject($_SESSION['member_id']);

//headers start here
include(AT_INCLUDE_PATH.'header.inc.php'); 
$savant->display('pubmenu.tmpl.php');
$savant->display('settings/settings_menu.tmpl.php');
if (isset($_REQUEST['n']) && $_REQUEST['n']=='account_settings'){
	//TODO
	//Default to account settings
	//Page prints from here
	$savant->display('settings/account_settings.tmpl.php');
} elseif (isset($_REQUEST['n']) && $_REQUEST['n']=='application_settings'){
	$app = new Application();
	//handle application setting updates
	if (isset($_POST['submit'])){
		//Updates
		$app->setHomeDisplaySettings($_POST['app']);
		//TODO print message/feedback
		$msg->addFeedback('SETTINGS_UPDATED');
		$msg->printAll();
	}
	
	//initialization
	$savant->assign('home_display', $app->getHomeDisplaySettings());
	$savant->assign('my_apps', $list_of_my_apps = $app->listMyApplications());
	$savant->display('settings/application_settings.tmpl.php');
} else {
	//handle privacy setting updates
	if (isset($_POST['submit'])){
		//Updates
		$private_obj->setProfile($_POST['profile_prefs']);
		$private_obj->setSearch($_POST['search_prefs']);
		$private_obj->setActivity($activity_prefs);

		//update privacy preference
		PrivacyController::updatePrivacyPreference($_SESSION['member_id'], $private_obj);

		//TODO print message/feedback
		$msg->addFeedback('SETTINGS_UPDATED');
		$msg->printAll();
	}

	//Page prints from here
	$savant->assign('controller', $controller);
	$savant->assign('profile_prefs', $private_obj->getProfile());
	$savant->assign('search_prefs', $private_obj->getSearch());
	$savant->assign('application_prefs', $private_obj->getActivity());
	$savant->display('settings/privacy_settings.tmpl.php');
}
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>