<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Harris Wong								*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: openmeetings.inc.php 7575 2008-06-04 18:17:14Z hwong $
if (!defined('AT_INCLUDE_PATH')) { exit; }


/**
 * Login to openmeetings
 */




/**
 * Add a room to the db iff it has not been created.  Each course should only have one room to it.
 * @param sid is the auth session id that was logged in into openmeetings.
 * @return room # of the created room, or the room # of the existed room
 */
function om_getRoom($sid, $course_id, $room_name){
	$course_id = abs($course_id);
//	$room_name = $addslashes($room_name);

	//Check if the room has already been created for this
	$sql = 'SELECT rooms_id FROM '.TABLE_PREFIX.'openmeetings_rooms WHERE course_id = '.$course_id;
	$result = mysql_query($sql);
	if (mysql_numrows($result) > 0){
		$row = mysql_fetch_assoc($result);
		return $row['rooms_id'];
	}

	//Add this room
	$om = new SOAP_openmeetings($_config['openmeetings_location'].'/services/RoomService?wsdl');
	$username = 'atutor';
	$password = 'atutor';

	$param = array (	
				'SID'			=> $sid,
				'name'			=> $room_name				
				);

	$result = $om->addRoom($param);
	//TODO: Check for error, and handles success/failure
	if ($result){
		//TODO: On success, add to DB entry.
		$sql = 'INSERT INTO '.TABLE_PREFIX.'openmeetings_rooms SET rooms_id='.$result['return'].', course_id='.$course_id;
		$rs  = mysql_query($sql);
		if ($rs){
			return $result['return'];
		}
	} 
	return false;	
}
?>