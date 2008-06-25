<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_OPENMEETINGS);
require ('lib/openmeetings.class.php');
require ('lib/openmeetings.inc.php');

//local variables
$course_id = $_SESSION['course_id'];

//Initiate Openmeeting
$om_obj = new Openmeetings($course_id, $_SESSION['member_id']);

//Login
$om_obj->om_login();

//Handle form action
if (isset($_POST['submit']) && isset($_POST['room_id'])) {
	//delete course
	$_POST['room_id'] = intval($_POST['room_id']);
	$om_obj->om_deleteRoom($_POST['room_id']);
	$msg->addFeedback('OPENMEETINGS_DELETE_SUCEEDED');
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

	//Get the room id
	//TODO: Course title added/removed after creation.  Affects the algo here.
	if (isset($_SESSION['course_title']) && $_SESSION['course_title']!=''){
		$room_name = $_SESSION['course_title'];
	} else {
		$room_name = 'course_'.$course_id;
	}

	//add the room with the given parameters.
	$om_obj->om_addRoom($room_name, $_POST);
	$msg->addFeedback('OPENMEETINGS_ADDED_SUCEEDED');
	header('Location: index.php');
	exit;
} elseif (isset($_POST['cancel'])){
	$msg->addFeedback('OPENMEETINGS_CANCELLED');
	header('Location: index.php');
	exit;
} elseif (isset($_GET['action']) && $_GET['action'] == 'view'){
	$room_id = intval($_GET['room_id']);
	$sid	 = $addslashes($_GET['sid']);
	header('Location: view_meetings.php?room_id='.$room_id.SEP.'sid='.$sid);
	exit;
}

//Log into the room
$room_id = $om_obj->om_getRoom();

require (AT_INCLUDE_PATH.'header.inc.php');
include ('html/create_room.inc.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); 

?>