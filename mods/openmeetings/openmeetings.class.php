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
// $Id: openmeetings.class.php 7575 2008-06-04 18:17:14Z hwong $
if (!defined('AT_INCLUDE_PATH')) { exit; }
include('SOAP_openmeetings.php');

class Openmeetings {
	var $_sid = '';		//Openmeetings session id
	var $_course_id = '';
	var $_member_id = '';
	var $_group_id = '';

	//Constructor
	function Openmeetings($course_id, $member_id, $group_id=0){
		$this->_course_id = abs($course_id);
		$this->_member_id = abs($member_id);
		$this->_group_id = abs($group_id);

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
		$sql = 'SELECT login, first_name, last_name, email FROM '.TABLE_PREFIX.'members WHERE member_id='.$this->_member_id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		// Save user instance
		$params = array(
					"username"				=> $row['login'],
					"firstname"				=> $row['first_name'],
					"lastname"				=> $row['last_name'],
					"profilePictureUrl"		=> '',
					"email"					=> $row['email']
				  );
		$om->saveUserInstance($params);
	}



	/**
	 * Add a room to the db iff it has not been created.  Each course should only have one room to it.
	 * @param int		sid is the auth session id that was logged in into openmeetings.
	 * @param array		the specification for openmeetings 
	 * @return room # of the created room, or the room # of the existed room
	 */
	function om_addRoom($room_name, $om_param=array()){
		global $_config;

		if ($this->_course_id < 0){
			return false;
		}

		//Check if the room has already been created for this
		if (($room_id = $this->om_getRoom()) !=false){
			//instead of returning room id, we might have to delete it and carry on.
			return $room_id;
		}

		//Add this room
		$om = new SOAP_openmeetings($_config['openmeetings_location'].'/services/RoomService?wsdl');
		$param = array (	
					'SID'					=> $this->_sid,
					'name'					=> $room_name,
					'numberOfPartizipants'	=> $om_param['openmeetings_num_of_participants'],
					'ispublic'				=> $om_param['openmeetings_ispublic'],
					'videoPodWidth'			=> $om_param['openmeetings_vid_w'],
					'videoPodHeight'		=> $om_param['openmeetings_vid_h'],
					'showWhiteBoard'		=> $om_param['openmeetings_show_wb'],
					'whiteBoardPanelWidth'	=> $om_param['openmeetings_wb_w'],
					'whiteBoardPanelHeight'	=> $om_param['openmeetings_wb_h'],
					'showFilesPanel'		=> $om_param['openmeetings_show_fp'],
					'filesPanelHeight'		=> $om_param['openmeetings_fp_h'],
					'filesPanelWidth'		=> $om_param['openmeetings_fp_w']
					);
		$result = $om->addRoom($param);
		//TODO: Check for error, and handles success/failure
		if ($result){
			//TODO: On success, add to DB entry.
			$sql = 'INSERT INTO '.TABLE_PREFIX.'openmeetings_rooms SET rooms_id='.$result['return'].', course_id='.$this->_course_id 
				 . ', owner_id=' . $this->_member_id;
			$rs  = mysql_query($sql);
			if (!$rs){
				return false;
			}
			$om_id = mysql_insert_id();
			$sql = 'INSERT INTO '.TABLE_PREFIX."openmeetings_groups SET om_id=$om_id, group_id=$this->_group_id";
			$rs = mysql_query($sql);
	
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
	function om_getRoom(){
//		$sql = 'SELECT rooms_id FROM '.TABLE_PREFIX.'openmeetings_rooms INNER JOIN '.TABLE_PREFIX."openmeetings_groups WHERE 
//				course_id = $this->_course_id AND owner_id = $this->_member_id AND group_id = $this->_group_id";
		$sql = 'SELECT rooms_id FROM '.TABLE_PREFIX.'openmeetings_rooms r NATURAL JOIN '.TABLE_PREFIX."openmeetings_groups g WHERE 
				course_id = $this->_course_id AND group_id = $this->_group_id";
		$result = mysql_query($sql);
//		debug($sql);
		if (mysql_numrows($result) > 0){
			$row = mysql_fetch_assoc($result);
			//instead of returning room id, we might have to delete it and carry on.
			return $row['rooms_id'];
		}
		return false;
	}


	/**
	 * Set the group id
	 * @param	int	group id.
	 */
	function setGid($gid){
		$this->_group_id = $gid;
	}

	/**
	 * Delete a room
	 */
	function om_deleteRoom($room_id){
		global $_config;
		$om = new SOAP_openmeetings($_config['openmeetings_location'].'/services/RoomService?wsdl');
		$param = array (	
					'SID'			=> $this->_sid,
					'rooms_id'		=> $room_id
					);

		$result = $om->deleteRoom($param);
		$sql = 'DELETE r, g FROM (SELECT om_id FROM '.TABLE_PREFIX."openmeetings_rooms WHERE rooms_id=$room_id) AS t, ".TABLE_PREFIX
				.'openmeetings_rooms r NATURAL JOIN '.TABLE_PREFIX.'openmeetings_groups g WHERE r.om_id =t.om_id';
		mysql_query($sql);
	}


	/**
	 * Return true if this user created the given room.
	 * @param	int	room id
	 * @return	true if it is, false otherwise.
	 */
	function isMine($room_id){
		$sql = 'SELECT * FROM '.TABLE_PREFIX."openmeetings_rooms WHERE rooms_id=$room_id AND owner_id=$this->_member_id";
		$result = mysql_query($sql);
		if (mysql_numrows($result) > 0){
			return true;
		} 
		return false;
	}
}
?>