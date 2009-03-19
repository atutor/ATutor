<?php
/**
 * Class for individual social group
 */
class SocialGroup {
	var $group_id;			//group id
	var $user_id;			//group creator
	var $logo;				//logo
	var $name;				//group name
	var $type_id;			//the type_id
	var $description;		//description of this group
	var $created_date;		//sql timestamp
	var $last_updated;		//sql timestamp
	var $group_members;		//group members
	var $group_activities;	//group activities

	/**
	 * Constructor
	 */
	function SocialGroup($group_id){
		global $db;
		$this->group_id = intval($group_id);

		if ($this->group_id > 0){
			$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_groups WHERE id='.$this->group_id;
			$result = mysql_query($sql, $db);
			if ($result){
				$row = mysql_fetch_assoc($result);
				$this->user_id			= $row['member_id'];
				$this->logo				= $row['logo'];
				$this->name				= $row['name'];
				$this->type_id			= $row['type_id'];
				$this->description		= $row['description'];
				$this->created_date		= $row['created_date'];
				$this->last_updated		= $row['last_updated'];
				$this->group_members	= $this->getGroupMembers();
				$this->group_activities = $this->getGroupActivities();
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
		if (!empty($this->group_member)){
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
		 $sql = 'SELECT a,id AS id, a.title AS title FROM '.TABLE_PREFIX.'social_groups_activities g LEFT JOIN '.TABLE_PREFIX.'activities a ON g.activity_id=a.id WHERE g.group_id='.$this->group_id;
		 $result = mysql_query($sql, $db);
		 if ($result){
			 while($row = mysql_fetch_assoc($result)){
				 $activities[$row['id']] = $row['title'];
			 }
		 }
		 return $activities;
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
		if (file_exists($this->logo)) {
			$str = '<img src="get_sgroup_logo.php?id='.$id.'" alt="" />';
		} else {
			$str = '<img src="mods/social/images/nogroup.gif" alt="" />';
		}
		 return $str;
	 }
	 function getName() {
		 return $this->name;
	 }
	 function getDescription(){
		 return $this->description;
	 }
	 function getCreatedDate(){
		 return $this->created_date;
	 }
	 function getLastUpdated(){
		 return $this->last_updated;
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
			$str1 = printSocialName($friend_id).' has joined the group, <a href="social/groups/view.php?id='.$this->getID().'">'.$this->getName().'</a>';
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

		 $sql = 'INSERT INTO '.TABLE_PREFIX.'social_group_invitations (sender_id, member_id, group_id) VALUES ('
				.$_SESSION['member_id'].', '.$member_id.', '.$this->group_id.')';
		 $result = mysql_query($sql, $db);
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
	 * Delete all group activities
	 * ToDo: Delete just one?
	 */
	 function removeGroupActivities(){
		 global $db;

		 $act_obj = new Activity();

		 //First remove groups activities from activity table
		 $allActs = $social_group->getActivities();
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
		include(AT_INCLUDE_PATH.'lib/forums.inc.php');

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
}
?>