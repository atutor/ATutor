<?php
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_SOCIAL_INCLUDE.'friends.inc.php');
include(AT_SOCIAL_INCLUDE.'classes/Application.class.php');
$_custom_css = $_base_path . 'mods/social/module.css'; // use a custom stylesheet

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
	$app_url = $addslashes($_POST['app_url']);
	$gadget = $app->parseModulePrefs($app_url);
	$gadget = $gadget[0];

	if (empty($gadget->errors)){
		//add applicatoin to database
		$app->addApplication($gadget);
	} else {
		debug($gadget);
	}
}

// Show all gadgets
if (isset($_POST['show_applications'])){
	$list_of_all_apps = $app->listApplications();
}

//Display individual application
if (isset($_GET['app_id'])){
	$_GET['app_id'] = intval($_GET['app_id']);
	$app = new Application($_GET['app_id']);	//testing application 1, 2

	
	//Add application
	if (isset($_GET['add']) && intval($_GET['add'])==1){
		$app->addMemberApplication($_SESSION['member_id'], $_GET['app_id']);
		$msg->addFeedback('gadget_added_successfully');
		header('Location: '. url_rewrite('mods/social/applications.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}

	//Delete application
	if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {
		$app->deleteApplication();
		$msg->addFeedback('gadget_deleted_successfully');
		header('Location: '. url_rewrite('mods/social/applications.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}

	//Display application settings
	if (isset($_GET['settings'])){
		include(AT_INCLUDE_PATH.'header.inc.php');
		$savant->assign('settings', $app->getSettings());	//userPrefs
		$savant->display('application_settings.tmpl.php');
		include(AT_INCLUDE_PATH.'footer.inc.php');		
		exit;
	}

	//loop through all app and print out the thumbnail
	$iframe_url = $app->getIframeUrl($_REQUEST['id'], 'canvas', $_GET['appParams']);

	//display
	include(AT_INCLUDE_PATH.'header.inc.php');
	$savant->assign('iframe_url', $iframe_url);
	$savant->assign('gadget', $gadget);
	$savant->assign('app', $app);
	$savant->display('individual_application.tmpl.php');
	include(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

//list all my applications
$list_of_my_apps = $app->listMyApplications();

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('list_of_my_apps', $list_of_my_apps);
$savant->assign('list_of_all_apps', $list_of_all_apps);
$savant->display('applications.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
