<?php

	//Temp script to set hardcoded PrivacyControl into the db.
	//This should be done via a user perference config page

	//TODO
	//use vitals.inc.php, not the scatter ones.
	define('AT_INCLUDE_PATH', '../../../../../include/');
	include_once(AT_INCLUDE_PATH.'config.inc.php');
	require_once(AT_INCLUDE_PATH.'lib/constants.inc.php');
	require_once(AT_INCLUDE_PATH.'lib/mysql_connect.inc.php');
	include('../../constants.inc.php');
	include('PrivacyObject.class.php');
	include('PrivacyController.class.php');

	//get user id
	$user_id = (isset($_SESSION['member_id'])?$_SESSION['member_id']:1);
	
	//profile_prefs
	//profile[bsaic info, detailed info, 
	$profile_prefs = array(
							array(AT_SOCIAL_FRIENDS_VISIBILITY),	//basic info
							array(AT_SOCIAL_FRIENDS_VISIBILITY),	//detailed info
							array(AT_SOCIAL_FRIENDS_VISIBILITY),	//profile status update
							array(AT_SOCIAL_FRIENDS_VISIBILITY),	//media
							array(),								//connection
							array(),	//education
							array(AT_SOCIAL_FRIENDS_VISIBILITY)		//job position
						  );
print_r($profile_prefs);
echo '<hr>';
	//search_prefs
	$search_prefs = array();

	//activity prefs
	$activity_prefs = array();

	//Updates
	$po = new PrivacyObject();
	$po->setProfile($profile_prefs);
	$po->setSearch($search_prefs);
	$po->setActivity($activity_prefs);

	//update privacy preference
	PrivacyController::updatePrivacyPreference($user_id, $po);
?>