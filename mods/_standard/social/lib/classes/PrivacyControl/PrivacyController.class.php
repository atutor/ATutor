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
include_once(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');

/**
 * Class PrivacyController
 */
class PrivacyController{
	//Constructor
	function PrivacyController(){
	}

	/**
	 * Validate user privacy preference against SESSION's, if empty, fetch from DB.
	 * @param	int			The field index that should be validated against, check lib/constnats.inc.php
	 * @param	int			Relationship between SESSION[member] and the current user's
	 * @param	mixed		The prefs array in respect to the field_id, for instance, if this is validating against profile, 
	 *						then the pref should be the profile preferences.  ([array]=>preference[profile, basic_profile, photo, ...])
	 * @return	boolean		True if access granted, false otherwise.
	 */
	function validatePrivacy($field_id, $relationship, $pref){
		//if this is the owner, return true without question
		if ($relationship==AT_SOCIAL_OWNER_VISIBILITY){
			return true;
		}

		$pref_string = $pref[$field_id];
//		debug($pref_string, $field_id);

//I have take this out so that in the settings, "Eveyerone" permission has to be switched on for everyone to see
		//if AT_SOCIAL_EVERYONE_VISIBILITY is set, relationship flag will no longer matters.
//		if ($relationship==AT_SOCIAL_EVERYONE_VISIBILITY){
//			return true;
//		}

		//all values are 1 or 0, match the key to the field_id
		if (is_array($pref_string) && !empty($pref_string)){		
			return (isset($pref_string[$relationship]) && $pref_string[$relationship]==1);
		} else {
			return false;
		}
	}

	/**
	 * Get the relationship between Session[member_id] and the given id.
	 * Relationship can be friends, friends of friends, network, family, aquaintance, etc.
	 * TODO: Confirm that the order of checks is not important.  Draw a control flow diagram to check this.
	 *		 For now, Friends of friends > Groups
	 * @param	int		the member that we want to find the relationship to the session[member]
	 * @return	relationship status
	 */
	function getRelationship($id){
		global $db;

		//if id = self, always true (cause i should be able to see my own profile)
		if ($id == $_SESSION['member_id']){
			return AT_SOCIAL_OWNER_VISIBILITY;
		}

		//is friend of friend?
		if (isFriendOfFriend($id, $_SESSION['member_id'])==true){
			return AT_SOCIAL_FRIENDS_OF_FRIENDS_VISIBILITY;
		}

		//is in some of the groups together?
		$social_groups = new SocialGroups();
		$my_group = $social_groups->getMemberGroups($_SESSION['member_id']);
		$person_group = $social_groups->getMemberGroups($id);
		$groups_intersection = array_intersect($my_group, $person_group); //groups intersection

		//If it is not empty or not null, then these 2 people share a group
		if (!empty($groups_intersection) > 0){
			return AT_SOCIAL_GROUPS_VISIBILITY;
		}

		$sql = 'SELECT relationship FROM '.TABLE_PREFIX."social_friends WHERE (member_id=$id AND friend_id=$_SESSION[member_id]) OR (member_id=$_SESSION[member_id] AND friend_id=$id)";
		$result = mysql_query($sql, $db);
//		echo $sql;
		if ($result){
			list($relationship) = mysql_fetch_row($result);
		}		

		//If the relationship is not set, this implies that it's not in the table, 
		//implying that the user has never set its privacy settings, meaning a default is needed
		if (!isset($relationship)){
			return AT_SOCIAL_NETWORK_VISIBILITY;
		}

		return $relationship;
	}

	/**
	 * Get user privacy perference
	 * @param	int		user id
	 * @Precondition: include('PrivacyObject.class.php');
	 */
	function getPrivacyObject($member_id){
		global $db;
		$member_id = intval($member_id);		
		
		//TODO: Check if this object exists in _SESSION, if so, don't pull it from db again
		$sql = 'SELECT preferences FROM '.TABLE_PREFIX.'social_privacy_preferences WHERE member_id='.$member_id;
		$result = mysql_query($sql, $db);
		if (mysql_numrows($result) > 0){
			list($prefs) = mysql_fetch_row($result);
			$privacy_obj = unserialize($prefs);

			//Should we checked if this is an actual object before returning it?
			return($privacy_obj);
		}
		//No such person
		return new PrivacyObject();
	}

	/**
	 * Update privacy preference for a single user
	 *
	 * @param	int		user id
	 * @param	mixed	preferences object
	 * @return	true if update was successful, false otherwise
	 */
	function updatePrivacyPreference($member_id, $prefs){
		global $db, $addslashes;

		$member_id = intval($member_id);
		$prefs = $addslashes(serialize($prefs));

		//TODO: Change it back to update
		$sql = 'REPLACE '.TABLE_PREFIX."social_privacy_preferences SET member_id=$member_id, preferences='$prefs'";
//		echo $sql;
		$result = mysql_query($sql, $db);
		return $result;
	}

	/**
	 * Returns an array of the user permission levels 
	 * Check constants.inc.php
	 */
	function getPermissionLevels(){
		return array (
			//checkboxes don't need to have none and everyone
//			-1										=>	_AT('none'),
			AT_SOCIAL_EVERYONE_VISIBILITY			=>	_AT('world_network'),
			AT_SOCIAL_FRIENDS_VISIBILITY			=>	_AT('friends'),
			AT_SOCIAL_FRIENDS_OF_FRIENDS_VISIBILITY =>	_AT('friends_of_friends'),
			AT_SOCIAL_NETWORK_VISIBILITY			=>	_AT('local_network'),
			AT_SOCIAL_GROUPS_VISIBILITY				=>	_AT('groups')
		);
	}
}
?>
