<?php
/**
 * Social Groups, this is different from the Groups class in ATutor.
 */
class SocialGroups{

	/**
	 * constructor
	 */
	 function SocialGroups(){}

	/**
	 * Adding a group
	 * @param	int		the group type specified in the table, social_groups_types
	 * @param	string	name of the group
	 * @param	string	description of the group
	 * @param	string	the filename of the logo
	 * @return	true if succeded, false otherwise.
	 */
	 function addGroup($type_id, $name, $description, $logo){
		 global $db, $addslashes;

		 $type_id = intval($type_id);
		 $description = $addslashes($description);
		 $logo = $addslashes($logo);

		 $sql = 'INSERT INTO '.TABLE_PREFIX."social_groups (`member_id`, `type_id`, `logo`, `name`, `description`, `created_date`, `last_updated`) VALUES ($_SESSION[member_id], $type_id, '$logo', '$name', '$description', NOW(), NOW())";
		 $result = mysql_query($sql, $db);
		 if ($result){
			 return true;
		 }
		 return false;
	 }

	/**
	 * Removing a group, invovles removing everything related to this group.
	 * @param	int		the group id
	 */
	 function removeGroup($id){
		 global $db;
		 $social_group = new SocialGroup($id);

		 //remove group activities
		 $social_group->removeGroupActivities();

		 //remove group forums
		$social_group->removeGroupForums();

		 //remove group members
		 $social_group->removeGroupMembers();

		 //remove groups 
		 $sql = 'DELETE FROM '.TABLE_PREFIX.'social_groups WHERE id='.$id;
		 mysql_query($sql, $db);
	 }


	/**
	 * Update a group
	 * @param	int		group id
	 * @param	int		member_id, to update the owner of this group
	 * @param	int		the group type specified in the table, social_groups_type
	 * @param	string	name of the group
	 * @param	string	description of the group
	 * @param	string	the filename of the logo
	 */
	 function updateGroup($group_id, $member_id, $type_id, $name, $description, $logo){
		 global $db, $addslashes;

		 $group_id = intval($group_id);
		 $member_id = intval($member_id);
		 $type_id = intval($type_id);
		 $name = $addslashes($name);
		 $description = $addslashes($description);
		 $logo = $addslashes($logo);

		 $sql = 'UPDATE '.TABLE_PREFIX."social_groups SET `member_id`=$_SESSION[member_id], `type_id`=$type_id, `logo`='$logo', `name`='$name', `description`='$description', `last_updated`=NOW() WHERE group_id=$group_id";
		 $result = mysql_query($sql, $db);
		 if ($result){
			 return true;
		 } 
		 return false;
	 }


	/**
	 * Get all the group types
	 * @return	list of group types
	 */
	 function getAllGroupType(){
		 global $db, $addslashes;
		 $group_types = array();

		 $sql = 'SELECT * FROM '.TABLE_PREFIX.'social_groups_types';
		 $result = mysql_query($sql, $db);
		 if ($result){
			 while($row = mysql_fetch_assoc($result)){
				$group_types[$row['type_id']] = $row['title'];
			 }
		 }
		 return $group_types;
	 }

	  
	/**
	 * Get the group infromation from the sql	
	 * @param	int		group id
	 * @return mixed	SocialGroup obj
	 */
	 function getGroup($group_id){
		$socialGroup = new SocialGroup($group_id);
		return $socialGroup;
	 }

	
	/**
	 * Get ALL of a person's groups
	 */
	 function getMemberGroups($member_id){
		 global $db;
		 $my_groups = array();

		 $sql = 'SELECT group_id FROM '.TABLE_PREFIX.'social_groups_members WHERE member_id='.$member_id;
		 $result = mysql_query($sql, $db);
		 if ($result){
			 while($row = mysql_fetch_assoc($result)){
				$my_groups[] = $row['group_id'];
			 }
		 }
		 return $my_groups;
	 }
}
?>