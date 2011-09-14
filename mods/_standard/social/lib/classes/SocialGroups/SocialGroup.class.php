<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

require_once(AT_SOCIAL_INCLUDE.'classes/Activity.class.php');

/**
 * Class for individual social group
 */
class SocialGroup {
	var $group_id;			//group id
	var $user_id;			//group creator
	var $logo;				//logo
	var $name;				//group name
	var $type_id;			//the type_id
	var $privacy;			//privacy, 0 for public, 1 for private
	var $description;		//description of this group
	var $created_date;		//sql timestamp
	var $last_updated;		//sql timestamp
	var $group_members;		//group members
	var $group_activities;	//group activities
	var $is_valid;			//set false if this is not a valid group

	/**
	 * Constructor
	 */
	function SocialGroup($group_id){
		global $db;
		$this->group_id = intval($group_id);

		if ($this->group_id > 0){
			$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_groups WHERE id='.$this->group_id;
			$result = mysql_query($sql, $db);
			if (mysql_num_rows($result) > 0){
				$row = mysql_fetch_assoc($result);
				$this->user_id			= $row['member_id'];
				$this->logo				= $row['logo'];
				$this->name				= $row['name'];
				$this->type_id			= $row['type_id'];
				$this->privacy			= $row['privacy'];
				$this->description		= $row['description'];
				$this->created_date		= $row['created_date'];
				$this->last_updated		= $row['last_updated'];
				$this->group_members	= $this->getGroupMembers();
				$this->group_activities = $this->getGroupActivities();
				$this->is_valid			= true;
			} else {
				//group does not exist, most likely deleted
				$this->is_valid			= false;
			}
		}
	}


	/**
	 * Retrieve a list of group members 
	 * @param	int		group id
	 * @return	mixed	array of members object
	 */
	 function getGroupMembers(){
		global $db;
		if (!empty($this->group_members)){
			return $this->group_members;
		}
		$members = array();
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_groups_members WHERE group_id='.$this->group_id;
		$result = mysql_query($sql, $db);
		if ($result){
			while ($row = mysql_fetch_assoc($result)){
				$members[] = new Member($row['member_id']);
			}
		}

		//TODO Return empty array or should i return error?
		return $members;
	 } 


	/**
	 * Get the group activities, all the activities that happens in this group.
	 * @param	int		group id
	 * @return	mixed	array of activities 
	 */
	 function getGroupActivities(){
		 global $db;
		 if (!empty($this->group_activities)){
			return $this->group_activities;
		}
		 $activities = array();
		 $sql = 'SELECT a,id AS id, a.title AS title FROM '.TABLE_PREFIX.'social_groups_activities g LEFT JOIN '.TABLE_PREFIX.'social_activities a ON g.activity_id=a.id WHERE g.group_id='.$this->group_id;
		 $result = mysql_query($sql, $db);
		 if ($result){
			 while($row = mysql_fetch_assoc($result)){
				 $activities[$row['id']] = $row['title'];
			 }
		 }
		 return $activities;
	 }

	/**
	 * Get a specific mesage from the given user.
	 * @param	int		the message id.
	 * @param	int		the member id, the member who created this message, or the moderator.
	 * @return the text of the message
	 */
	function getMessage($id, $member_id){
		global $db;
		$id = intval($id);
		$member_id = intval($member_id);
		
		$sql = 'SELECT body FROM '.TABLE_PREFIX.'social_groups_board WHERE group_id='.$this->getID().' AND id='.$id;
		
		//if not moderator
		if($member_id!=$this->user_id){
			$sql .= ' AND member_id='.$member_id;
		} 

		$rs = mysql_query($sql, $db);
		if($rs){
			list($body) = mysql_fetch_array($rs);
			return htmlentities_utf8($body);
		}
		return false;
	}

	/**
	 * Get message boards message, return a list sorted by date, in descending order
	 */
	 function getMessages(){
		 global $db;
		 $result = array();	

		 $sql = 'SELECT * FROM '.TABLE_PREFIX.'social_groups_board WHERE group_id='.$this->getID().' ORDER BY created_date DESC';
		 $rs = mysql_query($sql, $db);

		 if ($rs){
			 while ($row = mysql_fetch_assoc($rs)){
				$row['body'] = htmlentities_utf8($row['body']);	//escape xss attack
				$result [$row['id']] = $row;
			 }
		 }
		 return $result;
	 }


	/**
	 * Get the group information
	 */
	 function getID(){
		 return $this->group_id;
	 }
	 function getUser(){
		return $this->user_id;
	 }
	 function getGroupType(){
		 global $db;
		//or maybe print out the exact type name
		$sql = 'SELECT title FROM '.TABLE_PREFIX.'social_groups_types WHERE type_id='.$this->type_id;
		$result = mysql_query($sql, $db);
		list($type_name) = mysql_fetch_row($result);
		return _AT($type_name);
	 }
	 function getLogo()	{
		if (!empty($this->logo)) {
			$str = '<a href="'.url_rewrite(AT_SOCIAL_BASENAME.'groups/view.php?id='.$this->getID()).'"><img border="0" src="'.AT_SOCIAL_BASENAME.'groups/get_sgroup_logo.php?id='.$this->getID().'" alt="'.$this->getName().'" title="'.$this->getName().'"/></a>';
		} else {
			$str = '<img src="'.AT_SOCIAL_BASENAME.'images/placelogo.png" alt="'._AT('placelogo').'" title="'._AT('placelogo').'"/>';
		}
		
		 return $str;
	 }
	 function getName() {
		 return htmlentities_utf8($this->name);
	 }
	 //@param boolean change all carrier returns to <br/> if true.
	 function getDescription($use_nl2br=true){
		 return htmlentities_utf8($this->description, $use_nl2br);
	 }
	 function getCreatedDate(){
		 return $this->created_date;
	 }
	 function getPrivacy(){
		 //0 for public, 1 for private
		 return $this->privacy;
	 }
	 function getLastUpdated(){
		 return $this->last_updated;
	 }
	 function isValid(){
		 return $this->is_valid;
	 }

	/**
	 * Add a member to the group
	 * @param	int		member id
	 * @return	boolean	true if succeded, false otherwise.
	 */
	 function addMember($member_id){
		global $db;
		$member_id = intval($member_id);

		$sql = 'INSERT INTO '.TABLE_PREFIX.'social_groups_members (group_id, member_id) VALUES ('.$this->group_id.", $member_id)";
		$result = mysql_query($sql, $db);
		if ($result){
			//add a record to the activities
			$act = new Activity();		
			$str1 = _AT('has_joined_group', '<a href="'. url_rewrite(AT_SOCIAL_BASENAME . 'groups/view.php?id='.$this->getID(), AT_PRETTY_URL_IS_HEADER).'">'.htmlentities_utf8($this->getName()).'</a>');

			$act->addActivity($member_id, $str1);
			unset($act);
			return true;
		}
		return false;
	 }


	/**
	 * Sends an invitation to a member
	 * @param	int		member_id
	 */
	 function addInvitation($member_id) {
		global $db;
		$member_id = intval($member_id);

		$sql = 'INSERT INTO '.TABLE_PREFIX.'social_groups_invitations (sender_id, member_id, group_id) VALUES ('
				.$_SESSION['member_id'].', '.$member_id.', '.$this->getID().')';
		$result = mysql_query($sql, $db);
	 }


	/** 
	 * Sends a request to the group creator 
	 * @param	int		member_id
	 */
	function addRequest(){
		global $db;
	
		$sql = 'INSERT INTO '.TABLE_PREFIX.'social_groups_requests (sender_id, member_id, group_id) VALUES ('
				.$_SESSION['member_id'].', '.$this->getUser().', '.$this->getID().')';
		$result = mysql_query($sql, $db);
		return $result;
	}
	
	/**
	 * Add message to the message board
	 * @param	string	the message body
	 */
	 function addMessage($body) {
		 global $db, $addslashes;
		 $body = $addslashes($body);
		 $member_id = $_SESSION['member_id'];
		 $group_id = $this->getID();

		 $sql = 'INSERT INTO '.TABLE_PREFIX."social_groups_board (member_id, group_id, body, created_date) VALUES ($member_id, $group_id, '$body', NOW())";
		 $result = mysql_query($sql, $db);
		 return $result;
	 }

	
	/**
	 * Update group logo
	 * @param	string	filename of the logo. <name.extension>
	 */
	function updateGroupLogo($logo) {
		global $db, $addslashes;
		$logo = $addslashes($logo);
		$sql = 'UPDATE '.TABLE_PREFIX."social_groups SET logo='$logo' WHERE id=".$this->getID();
		$result = mysql_query($sql, $db);
		return $result;
	}
	

	/** 
	 * Edit message 
	 * @param	int		the id of the message
	 * @param	string	message body
	 */
	 function updateMessage($id, $body){
		 global $db, $addslashes;
		 $id = intval($id);
		 $body = $addslashes($body);
		if ($id <= 0){
			return false;
		}

		$sql = 'UPDATE '.TABLE_PREFIX."social_groups_board SET body='$body' WHERE id=$id";
		$result = mysql_query($sql, $db);
		return $result;
	 }
	

	/** 
	 * Remove a member from the group
	 * @param	int		member_id
	 * @retrun	boolean	troe successful and false otherwise.
	 */
	 function removeMember($member_id){
		global $db;
		$member_id = intval($member_id);

		//quit if member_id = creator id
		if ($member_id == $this->getUser()){
			return false;
		}

		$sql = 'DELETE FROM '.TABLE_PREFIX."social_groups_members WHERE member_id=$member_id AND group_id=".$this->group_id;
		$result = mysql_query($sql, $db);
		if ($result){
			return true;
		}
		return false;
	 }

	/**
	 * Remove logo from content/social folder
	 */
	function removeGroupLogo(){	
		if ($this->logo!=''){
			unlink(AT_CONTENT_DIR.'social/'. $this->logo);
		}
		return file_exists(AT_CONTENT_DIR.'social/'. $this->logo);
	}



	/**
	 * Delete all group activities
	 * ToDo: Delete just one?
	 */
	 function removeGroupActivities(){
		 global $db;

		 $act_obj = new Activity();

		 //First remove groups activities from activity table
		 $allActs = $this->group_activities;
		 foreach ($allActs as $id=>$garbage) {
			$act_obj->deleteActivity($id);
		 }

		 //Then remove the associations from social_groups_activities
		 $sql = 'DELETE FROM '.TABLE_PREFIX."social_groups_activities WHERE group_id=".$this->group_id;
		 $result = mysql_query($sql, $db);
		 if ($result){
			 return true;
		 }
		 return false;
	 }


	/**
	 * Delete all group forums
	 */
	function removeGroupForums(){
		global $db;
		include(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');
		
		//delete all forums for this social group
		$sql = 'SELECT forum_id FROM '.TABLE_PREFIX.'social_groups_forums WHERE group_id='.$this->group_id;
		$result = mysql_query($sql, $db);
		if ($result){
			while ($row = mysql_fetch_assoc($result)){
				delete_forum($row['forum_id']);
			}
		}

		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_groups_forums WHERE group_id='.$this->group_id;
		$result = mysql_query($sql, $db);
		if ($result){
			return true;
		} 
		return false;
	}

	
	/**
	 * Delete all group members
	 */
	function removeGroupMembers(){
		global $db;
		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_groups_members WHERE group_id='.$this->group_id;
		$result = mysql_query($sql, $db);
		if ($result){
			return true;
		}
		return false;
	}


	/** 
	 * Delete all requests inside this group
	 */
	function removeGroupRequests(){
		global $db;
		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_groups_requests WHERE group_id='.$this->group_id;
		$result = mysql_query($sql, $db);
		if ($result){
			return true;
		}
		return false;
	}


	/**
	 * Delete all invitations inside this group
	 */
	function removeGroupInvitations(){
		global $db;
		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_groups_invitations WHERE group_id='.$this->group_id;
		$result = mysql_query($sql, $db);
		if ($result){
			return true;
		}
		return false;
	}

	/** 
	 * Delete a message from the board
	 * @param	int		member_id
	 */
	function removeMessage($id, $member_id){
		global $db;
		$id = intval ($id);
		$member_id = intval($member_id);
		$sql = 'DELETE FROM '.TABLE_PREFIX."social_groups_board WHERE id=$id ";

		//if not moderator.
		if ($member_id != $this->user_id){
			$sql .= " AND member_id=$member_id";			
		} 
		$result = mysql_query($sql, $db);
		return $result;
	}

	
	/**
	 * Delete all the messages from the board
	 */
	function removeAllMessages(){
		global $db;

		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_groups_board WHERE group_id='.$this->getID();
		$result = mysql_query($sql, $db);
		return $result;
	}


	/**
	 * Search members
	 * TODO: Maybe to make a general search($string, $member_obj_array) that takess any member obj array.  
	 *		 This can be used for friends search as well.  Optimize the code and structure a bit.
	 * @param	string	member name
	 * @return	array  of Members Object
	 */
	function searchMembers($name){
		global $db, $addslashes;

		//break the names by space, then accumulate the query
		$name = $addslashes($name);	
		$sub_names = explode(' ', $name);
		foreach($sub_names as $piece){
			$query .= "(first_name LIKE '%$piece%' OR second_name LIKE '%$piece%' OR last_name LIKE '%$piece%' OR email LIKE '$piece') AND ";
		}
		//trim back the extra "AND "
		$query = substr($query, 0, -4);
		
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_groups_members g LEFT JOIN '.TABLE_PREFIX.'members m ON g.member_id=m.member_id WHERE g.group_id='.$this->getID().' AND '.$query;
		$rs = mysql_query($sql, $db);

		while ($row=mysql_fetch_assoc($rs)) {
			$result[$row['member_id']] = new Member($row['member_id']);
		}
		return $result;
	}
}
?>