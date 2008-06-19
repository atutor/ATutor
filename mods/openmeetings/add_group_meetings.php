<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('openmeetings.class.php');
require ('openmeetings.inc.php');

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

//Form action
//Handle form action
if (isset($_POST['submit']) && isset($_POST['room_id'])) {
	//delete course
	$_POST['room_id'] = intval($_POST['room_id']);
	$om_obj->om_deleteRoom($_POST['room_id']);

} elseif (isset($_POST['submit'])){
	//mysql escape
	$_POST['openmeetings_num_of_participants']	= intval($_POST['openmeetings_num_of_participants']);
	(intval($_POST['openmeetings_ispublic']) == 1)?$_POST['openmeetings_ispublic']='true':$_POST['openmeetings_ispublic']='false';
	$_POST['openmeetings_vid_w']				= intval($_POST['openmeetings_vid_w']);
	$_POST['openmeetings_vid_h']				= intval($_POST['openmeetings_vid_h']);
	(intval($_POST['openmeetings_show_wb']) == 1)?$_POST['openmeetings_show_wb']='true':$_POST['openmeetings_show_wb']='false';
	$_POST['openmeetings_wb_w']					= intval($_POST['openmeetings_wb_w']);
	$_POST['openmeetings_wb_h']					= intval($_POST['openmeetings_wb_h']);
	(intval($_POST['openmeetings_show_fp']) == 1)?$_POST['openmeetings_show_fp']='true':$_POST['openmeetings_show_fp']='false';
	$_POST['openmeetings_fp_w']					= intval($_POST['openmeetings_fp_w']);
	$_POST['openmeetings_fp_h']					= intval($_POST['openmeetings_fp_h']);

	//add the room with the given parameters.
	$om_obj->om_addRoom($room_name, $_POST);
} elseif (isset($_POST['cancel'])){
	header('Location: index.php');
} elseif (isset($_GET['action']) && $_GET['action'] == 'view'){
	$room_id = intval($_GET['room_id']);
	$sid	 = $addslashes($_GET['sid']);
	header('Location: view_meetings.php?room_id='.$room_id.SEP.'sid='.$sid);
	exit;
}

$room_id = $om_obj->om_getRoom();
debug($room_id, 'You have a room'); 

require (AT_INCLUDE_PATH.'header.inc.php');
include ('html/create_room.inc.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); 

?>