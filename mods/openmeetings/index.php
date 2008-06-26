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
// $Id: index.php 7575 2008-06-02 18:17:14Z hwong $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('lib/openmeetings.class.php');
require ('lib/openmeetings.inc.php');

//css
$_custom_css = $_base_path . 'mods/openmeetings/module.css'; // use a custom stylesheet

//local variables
$course_id = $_SESSION['course_id'];

// Check access
checkAccess($course_id);

//Header begins here
require (AT_INCLUDE_PATH.'header.inc.php');

//Initiate Openmeeting
$om_obj = new Openmeetings($course_id, $_SESSION['member_id']);

//Login
$om_obj->om_login();

//Handles form actions
if (isset($_GET['delete']) && isset($_GET['room_id'])){
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