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

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
include(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyObject.class.php');
include(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyController.class.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

$controller = new PrivacyController();
$private_obj = $controller->getPrivacyObject($_SESSION['member_id']);

//handle privacy setting updates
if (isset($_POST['submit'])){
	//Updates
	$private_obj->setProfile($_POST['profile_prefs']);
	$private_obj->setSearch($_POST['search_prefs']);
	$private_obj->setActivity($activity_prefs);

	//update privacy preference
	PrivacyController::updatePrivacyPreference($_SESSION['member_id'], $private_obj);
}

//Page prints from here
include(AT_INCLUDE_PATH.'header.inc.php'); 
$savant->display('pubmenu.tmpl.php');
$savant->assign('controller', $controller);
$savant->assign('profile_prefs', $private_obj->getProfile());
$savant->assign('search_prefs', $private_obj->getSearch());
$savant->assign('application_prefs', $private_obj->getActivity());
$savant->display('settings/privacy_settings.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>