<?php
/**
 * Class for individual social group
 */
class SocialGroup {
	var $group_id;		//group id
	var $user_id;		//group creator
	var $logo;			//logo
	var $type;			//the type_id
	var $description;	//description of this group
	var $created_date;	//sql timestamp
	var $last_updated;	//sql timestamp

	/**
	 * Constructor
	 */
	function SocialGroup($group_id){
		$this->group_id = $group_id;
	}

	/**
	 * Retrieve a list of group members 
	 * @param	int		group id
	 * @return	mixed	array of members object
	 */
	 function getGroupMembers(){
		global $db;
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
		 $activities = array();
		 $sql = 'SELECT a,id AS id, a.title AS title FROM '.TABLE_PREFIX.'social_groups_activities g LEFT JOIN '.TABLE_PREFIX'.activities a ON g.activity_id=a.id WHERE g.group_id='.$this->group_id;
		 $result = mysql_query($sql, $db);
		 if ($result){
			 while($row = mysql_fetch_assoc($result)){
				 $activities[$row['id']] = $row['title'];
			 }
		 }
		 return $result;
	 }


	/**
	 * Get the group information
	 */
	 function getUser(){
		return $this->user_id;
	 }
	 function getGroupType(){
		//or maybe print out the exact type name
		return $this->type_id;
	 }
	 function getLogo()	{
		 return $this->logo;
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
		$member_id = intval($member_id);

		$sql = 'INSERT INTO '.TABLE_PREFIX.'social_groups_members (group_id, member_id) VALUES ('.$this->group_id.", $member_id)";
		$result = mysql_query($sql, $db);
		if ($result){
			return true;
		}
		return false;
	 }


	 /** 
	  * Remove a member from the group
	  * @param	int		member_id
	  * @retrun	boolean	troe successful and false otherwise.
	  */
	 function removeMember($member_id){
		global $db;
		$member_id = intval($member_id);

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
	function removeGroupForums(){}

	
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