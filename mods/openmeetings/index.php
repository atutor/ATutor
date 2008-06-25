<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('lib/openmeetings.class.php');
$_custom_css = $_base_path . 'mods/openmeetings/module.css'; // use a custom stylesheet

//local variables
$course_id = $_SESSION['course_id'];

/*
 * Check access
 * Disallowing improper accesses from a GET request
 */
$sql	= "SELECT `access` FROM ".TABLE_PREFIX."courses WHERE course_id=$course_id";
$result = mysql_query($sql, $db);
$course_info = mysql_fetch_assoc($result);

if ($course_info['access']!='public' && ($_SESSION['enroll'] == AT_ENROLL_NO || $_SESSION['enroll'] == AT_ENROLL_ALUMNUS)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('NOT_ENROLLED');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (!isset($_config['openmeetings_username']) || !isset($_config['openmeetings_userpass'])){
	require(AT_INCLUDE_PATH.'header.inc.php');
	echo 'Contact admin plz';
	//Please contact your administrator, om needs to be setup.
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


//Header begins here
require (AT_INCLUDE_PATH.'header.inc.php');

//Initiate Openmeeting
$om_obj = new Openmeetings($course_id, $_SESSION['member_id']);

//Login
$om_obj->om_login();

//Handles form actions
if (isset($_GET['delete']) && $_GET['room_id']){
	//have to makesure the user really do have permission over the paramater room id
	$_GET['room_id'] = intval($_GET['room_id']);
	if ($om_obj->isMine($_GET['room_id'])){
		$om_obj->om_deleteRoom($_GET['room_id']);
		$msg->addFeedback('OPENMEETINGS_DELETE_SUCEEDED');
	} else {
		$msg->addError('OPENMEETINGS_DELETE_FAILED');
	}
}

//Course meetings
include_once('html/course_meeting.inc.php');

//Group meetings
include_once('html/group_meeting.inc.php');

require (AT_INCLUDE_PATH.'footer.inc.php');
?>