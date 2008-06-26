<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Cindy Qi Li, Harris Wong		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: add_group_meetings.php 7575 2008-06-02 18:17:14Z hwong $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('lib/openmeetings.class.php');
require ('lib/openmeetings.inc.php');

//Validate 
if (isset($_GET['group_id'])){
	$group_id = intval($_GET['group_id']);
	
	//TODO
	//Handles instrcutor as an exception, cuz instructor can go in and create room as well
	if (authenticate(AT_PRIV_OPENMEETINGS, true)){
		$sql = 'SELECT g.title FROM '.TABLE_PREFIX."groups g WHERE g.group_id=$group_id";
	} else {
		$sql = 'SELECT g.title FROM '.TABLE_PREFIX."groups_members gm INNER JOIN ".TABLE_PREFIX."groups g WHERE gm.group_id=$group_id AND gm.member_id=$_SESSION[member_id]";
	}
	if (mysql_numrows($result) <= 0){
		$msg->addError('OPENMEETINGS_ADD_FAILED');
		header('index.php');
		exit;
	} 
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
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

$room_id = $om_obj->om_getRoom();

require (AT_INCLUDE_PATH.'header.inc.php');
include ('html/create_room.inc.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>