<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('lib/openmeetings.class.php');
//$_custom_css = $_base_path . 'mods/openmeetings/module.css'; // use a custom stylesheet

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

$_GET['gid'] = intval($_GET['gid']);

//Initiate Openmeeting
$om_obj = new Openmeetings($course_id, $_SESSION['member_id']);

//Login
$om_obj->om_login();

//Group meetings
$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=$_GET[gid] ORDER BY title";
//TODO: Check group permission from group table.
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);

if (mysql_numrows($result) == 0){
	echo '<div>'._AT('openmeetings_no_group_meetings').'</div>';
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} 

//Check in the db and see if this group has a meeting alrdy, create on if not.
$om_obj->setGid($_GET['gid']);
if ($om_obj->om_getRoom()){
	//Log into the room
	$room_id = $om_obj->om_addRoom($room_name);
	header('Location: '.AT_BASE_HREF.'mods/openmeetings/view_meetings.php?room_id='.$room_id.SEP.'sid='.$om_obj->getSid());
	exit;
} else {
	//Header begins here
	require (AT_INCLUDE_PATH.'header.inc.php');
	echo '<ul>';
	echo '<li>'.$row['title'].'<a href="mods/openmeetings/add_group_meetings.php?group_id='.$_GET['gid'].'"> Start a conference </a>'.'</li>';
	echo '</ul>';
	require (AT_INCLUDE_PATH.'footer.inc.php');
}
?>