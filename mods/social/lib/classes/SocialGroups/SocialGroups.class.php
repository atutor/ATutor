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
		 $group_id = mysql_insert_id();
		 if ($result){
			 //add it to the group member table
			 $sql = 'INSERT INTO '.TABLE_PREFIX."social_groups_members (group_id, member_id) VALUES ($group_id, $_SESSION[member_id])";
			 $result = mysql_query($sql, $db);
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

		 $sql = 'UPDATE '.TABLE_PREFIX."social_groups SET `member_id`=$_SESSION[member_id], `type_id`=$type_id, `logo`='$logo', `name`='$name', `description`='$description', `last_updated`=NOW() WHERE id=$group_id";
		 debug();echo $sql;
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


	 /**
	  * Search
	  * @param	string	query string for the search
	  * @param	string	filters
	  */
	 function search($query, $filters=''){
		/* Perform a simple search for now
		 * That searches only the title? 
		 * Use Joel's search idea? Point based system search? The Google's idea?
		 *
		 * Idea:
		 * 1. Get all results that matches the query separtated by space
		 * 2. Give points to the result that has more match, or whatever our "point system" is
		 * 3. Sort it in the order of most points to the least points.  
		 */
		 global $db, $addslashes;

		 if ($query=='') return array();  //quit if search query is empty

		 $search_result = array();
		 $query = $addslashes(trim($query));
		 $words = explode(' ', $query);
		 foreach($words as $piece){
			$extra .= "`name` LIKE '%$piece%' OR ";
		 }
		 $extra = substr($extra, 0, -3);
		 $sql = 'SELECT * FROM '.TABLE_PREFIX.'social_groups WHERE '.$extra;
		 $result = mysql_query($sql, $db);
		 if ($result){
			while ($row = mysql_fetch_assoc($result)){
				$search_result[$row['id']]['obj'] = new SocialGroup($row['id']);
				$search_result[$row['id']]['weight'] = $this->inQuery($words, $row['name']);
			}
		 }
		 uasort($search_result, array($this, 'search_cmp'));
		 return array_reverse($search_result);
	 }


	 /**
	  * Return the counts of word appeareance in the given string.  
	  * @param	array	the string that hte user typed in to search for
	  * @param	string	the name of the group
	  */
	 function inQuery($words, $str){
		 $count = 0;
		 foreach ($words as $index=>$word){
			 if (trim($word)=='') continue;
			 $count += substr_count($str, strtolower($word));
		 }
		 return $count;
	 }
	

	function search_cmp($row1, $row2){
		if ($row1['weight'] == $row2['weight']) return 0;
		return ($row1['weight'] < $row2['weight']) ? -1 : 1;
	}
}
?>