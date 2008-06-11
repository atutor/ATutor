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
include('SOAP_openmeetings.php');

class Openmeetings {
	var $_sid = '';		//Openmeetings session id
	var $course_id = '';

	//Constructor
	function Openmeetings($course_id){
		$this->course_id = abs($course_id);
	}

	/**
	 * Login to openmeetings
	 * Login process is, login, saveuserinstance
	 */
	function om_login() {
		global $_config;
		$om = new SOAP_openmeetings($_config['openmeetings_location'].'/services/UserService?wsdl');
		$param = array (	'username' => $_config['openmeetings_username'], 
							'userpass' => $_config['openmeetings_userpass']);

		/**
		 * Login to the openmeetings
		 * ref: http://code.google.com/p/openmeetings/wiki/DirectLoginSoapGeneralFlow
		 */
		$result = $om->login($param);
		if ($result < 0){
			debug($om->getError($result), 'error');
			return;
		} 
		
		//If no error, then get the generated OM session id
		$this->_sid = $om->getSid();

		//Retrieve members information
		$sql = 'SELECT first_name, last_name, email FROM '.TABLE_PREFIX.'members WHERE member_id='.$_SESSION['member_id'];
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		// Save user instance
		$params = array(
					"username"				=> $_SESSION['login'],
					"firstname"				=> $row['first_name'],
					"lastname"				=> $row['last_name'],
					"profilePictureUrl"		=> '',
					"email"					=> $row['email']
				  );
		$om->saveUserInstance($params);
	}



	/**
	 * Add a room to the db iff it has not been created.  Each course should only have one room to it.
	 * @param sid is the auth session id that was logged in into openmeetings.
	 * @return room # of the created room, or the room # of the existed room
	 */
	function om_getRoom( $room_name){
		global $_config; 

		if ($this->course_id < 0){
			return false;
		}

		//Check if the room has already been created for this
		if (($room_id = $this->isRoomOpen($this->course_id)) !=false){
			return $room_id;
		}

		$sql = 'SELECT rooms_id FROM '.TABLE_PREFIX.'openmeetings_rooms WHERE course_id = '.$this->course_id;
		$result = mysql_query($sql);
		if (mysql_numrows($result) > 0){
			$row = mysql_fetch_assoc($result);
			//instead of returning room id, we might have to delete it and carry on.
			return $row['rooms_id'];
		}

		//Add this room
		$om = new SOAP_openmeetings($_config['openmeetings_location'].'/services/RoomService?wsdl');
		$username = 'atutor';
		$password = 'atutor';

		$param = array (	
					'SID'			=> $this->_sid,
					'name'			=> $room_name				
					);

		$result = $om->addRoom($param);
		//TODO: Check for error, and handles success/failure
		if ($result){
			//TODO: On success, add to DB entry.
			$sql = 'INSERT INTO '.TABLE_PREFIX.'openmeetings_rooms SET rooms_id='.$result['return'].', course_id='.$this->course_id;
			$rs  = mysql_query($sql);
			if ($rs){
				return $result['return'];
			}
		} 
		return false;	
	}

	/**
	 * Retrieve Session id
	 */
	function getSid(){
		return $this->_sid;
	}

	/**
	 * Checks if there is a room for the given course id.
	 *
	 * @param	course id
	 * @return	the room id if there is a room already assigned to this course; false otherwise
	 */
	function isRoomOpen(){
		$sql = 'SELECT rooms_id FROM '.TABLE_PREFIX.'openmeetings_rooms WHERE course_id = '.$this->course_id;
		$result = mysql_query($sql);
		if (mysql_numrows($result) > 0){
			$row = mysql_fetch_assoc($result);
			//instead of returning room id, we might have to delete it and carry on.
			return $row['rooms_id'];
		}
		return false;
	}
}
?>