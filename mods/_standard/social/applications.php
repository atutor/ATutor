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
// $Id: applications.php 10055 2010-06-29 20:30:24Z cindy $
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_SOCIAL_INCLUDE.'friends.inc.php');
include(AT_SOCIAL_INCLUDE.'classes/Application.class.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

//initialization
$app = new Application();

// Install gadgets
if (isset($_POST['add_application']) && isset($_POST['app_url'])){
	//check if curl is installed
	if (!extension_loaded('curl')){
		$msg->addError('CURL_NOT_INSTALLED');
		header('Location: '. url_rewrite(AT_SOCIAL_BASENAME.'applications.php', AT_PRETTY_URL_IS_HEADER));
		exit; 
	}
	$app_url = urldecode(trim($_POST['app_url']));
	//grep the XML file out from any given URL
	preg_match('/url\=((http[s]?\:\/\/)?(.*)\.xml)/', $app_url, $matches);
	if ($matches[1]!=''){
		$app_url = $matches[1];
	}	
	//validate app_url to make sure it always has http:// on it 
	if (preg_match('/^http[s]?\:\/\//', $app_url)==0){
		$app_url = 'http://'.$app_url;
	}

	$gadget = $app->parseModulePrefs($app_url);
	$gadget = $gadget[0];

	if (empty($gadget->errors)){
		//add applicatoin to database
		$app->addApplication($gadget);
		$msg->addFeedback('GADGET_ADDED_SUCCESSFULLY');
		header('Location: '. url_rewrite(AT_SOCIAL_BASENAME.'applications.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	} else {
		$msg->addError(array('GADGET_ADDED_FAILURE', implode(', ', $gadget->errors)));
		header('Location: '. url_rewrite(AT_SOCIAL_BASENAME.'applications.php', AT_PRETTY_URL_IS_HEADER));
		exit; 
	}
}

// Show all gadgets
if (isset($_POST['show_applications'])){
	$list_of_all_apps = $app->listApplications();
}

//Display individual application
if (isset($_REQUEST['app_id'])){
	$_REQUEST['app_id'] = intval($_REQUEST['app_id']);
	$app = new Application($_REQUEST['app_id']);	//testing application 1, 2
	
	//Add application
	if (isset($_GET['add']) && intval($_GET['add'])==1){
		$app->addMemberApplication($_SESSION['member_id'], $_GET['app_id']);
		$msg->addFeedback('GADGET_ADDED_SUCCESSFULLY');
		header('Location: '. url_rewrite(AT_SOCIAL_BASENAME.'applications.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}

	//Delete application
	if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {
		$app->deleteApplication();
		$msg->addFeedback('GADGET_REMOVED_SUCCESSFULLY');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit;
	}

	//Display application settings
	if (isset($_GET['settings'])){
		include(AT_INCLUDE_PATH.'header.inc.php');
		$savant->assign('settings', $app->getSettings());	//userPrefs
		$savant->assign('user_settings', $app->getApplicationSettings($_SESSION['member_id']));
		$savant->assign('app_id', $app->getId());	//id
		$savant->display('social/application_settings.tmpl.php');
		include(AT_INCLUDE_PATH.'footer.inc.php');		
		exit;
	}

	//Save settings
	if (isset($_POST['app_settings'])){
		foreach ($app->getSettings() as $key=>$value){
			if(isset($_POST[$key])){
				//save values iff it is in the userPrefs serialized string.
				//don't save values blindly from the $_POST.
				$value = $addslashes($_POST[$key]);
				$app->setApplicationSettings($_SESSION['member_id'], $key, $value);
			}
		}
		$msg->addFeedback('GADGET_SETTINGS_SAVED');
		header('Location: '. url_rewrite(AT_SOCIAL_BASENAME.'applications.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}

	//loop through all app and print out the thumbnail
	$iframe_url = $app->getIframeUrl($_REQUEST['id'], 'canvas', $_GET['appParams']);

	//display
	include(AT_INCLUDE_PATH.'header.inc.php');
	$savant->assign('iframe_url', $iframe_url);
	$savant->assign('app', $app);
	$savant->display('social/individual_application.tmpl.php');
	include(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

//list all my applications
$list_of_my_apps = $app->listMyApplications();

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('social/pubmenu.tmpl.php');
$savant->assign('list_of_my_apps', $list_of_my_apps);
$savant->assign('list_of_all_apps', $list_of_all_apps);
$savant->display('social/applications.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
