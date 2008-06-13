<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('openmeetings.class.php');

//Validate 
if (isset($_GET['group_id'])){
	$group_id = intval($_GET['group_id']);
	
	//TODO
	//Handles instrcutor as an exception, cuz instructor can go in and create room as well
	$sql = 'SELECT g.title FROM '.TABLE_PREFIX."groups_members gm INNER JOIN ".TABLE_PREFIX."groups g WHERE gm.group_id=$group_id AND gm.member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	if (mysql_numrows($result) <= 0){
		echo 'You do not belong to this group';
		exit;
	} else {
		$row = mysql_fetch_assoc($result);
	}
}

//Initiate Openmeeting
$om_obj = new Openmeetings($_SESSION['course_id'], $_SESSION['member_id'], $group_id);

//Login
$om_obj->om_login();

//Get the room id
//TODO: Course title added/removed after creation.  Affects the algo here.
if ($_row['title']!=''){
	$room_name = $_row['title'];
} else {
	$room_name = 'group_'.$group_id;
}

//Log into the room
$room_id = $om_obj->om_getRoom($room_name);

header('Location: view_meetings.php?room_id='.$room_id.SEP.'sid='.$om_obj->getSid());
?>