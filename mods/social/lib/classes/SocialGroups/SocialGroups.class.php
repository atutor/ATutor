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
	 * @param	string	description of the group
	 * @param	string	the filename of the logo
	 * @return	true if succeded, false otherwise.
	 */
	 function addGroup($type, $description, $logo){
		 global $db, $addslashes;

		 $type = intval($type);
		 $description = $addslashes($description);
		 $logo = $addslashes($logo);

		 $sql = 'INSERT INTO '.TABLE_PREFIX."social_groups (`member_id`, `type`, `logo`, `description`, `created_date`, `last_updated`) VALUES ($_SESSION[member_id], $type, '$logo', '$description', NOW(), NOW())";
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
		//TODO

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
	 * @param	string	description of the group
	 * @param	string	the filename of the logo
	 */
	 function updateGroup($group_id, $member_id, $description, $logo){}


	/**
	 * Get the group infromation from the sql	
	 * @return mixed	array of the group information, key=>value
	 */
	 function getGroup($group_id){return null;}
}
?>