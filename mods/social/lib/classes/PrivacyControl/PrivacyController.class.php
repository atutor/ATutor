<?php
/**
 * Class PrivacyController
 */
class PrivacyController{
	//Constructor
	function __construct(){
	}

	/**
	 * Validate user privacy preference against SESSION's, if empty, fetch from DB.
	 * @param	int			The field that should be validated against.
	 * @param	int			Relationship between SESSION[member] and the current user's
	 * @param	mixed		The prefs array in respect to the field_id, for instance, if this is validating against profile, then the pref should be the profile								preferences.  ([array]=>preference[profile, basic_profile, photo, ...])
	 * @return	boolean		True if access granted, false otherwise.
	 */
	function validatePrivacy($field_id, $relationship, $pref){
		//
	}

	/**
	 * Get the relationship between Session[member_id] and the given id.
	 * Relationship can be friends, friends of friends, network, family, aquaintance, etc.
	 * @param	int		the member that we want to find the relationship to the session[member]
	 * @return	relationship status
	 */
	function getRelationship($id){
		global $db;
		$sql = 'SELECT relationship FROM'.TABLE_PREFIX."friends WHERE (member_id=$id AND friend_id=$_SESSION[member_id]) OR (member_id=$_SESSION[member_id] AND friend_id=$id)";
		$result = mysql_query($sql, $db);
		if ($result){
			list($relationship) = mysql_fetch_row($result);
		}

		if (!isset($relationship)){
			return -1;
		}

		return $relationship;
	}

	/**
	 * Get user privacy perference
	 * @param	int		user id
	 */
	function getPrivacyObject($member_id){
		global $db;
		$member_id = intval($member_id);		
		
		//TODO: Check if this object exists in _SESSION, if so, don't pull it from db again

		$sql = 'SELECT preferences FROM '.TABLE_PREFIX.'privacy_preferences WHERE member_id='.$member_id;
		$result = mysql_query($sql, $db);
		list($prefs) = mysql_fetch_row($result);
		$privacy_obj = unserialize($prefs);

		//Should we checked if this is an actual object before returning it?
		return($privacy_obj);
	}

	/**
	 * Update privacy preference for a single user
	 *
	 * @param	int		user id
	 * @param	mixed	preferences object
	 * @return	true if update was successful, false otherwise
	 */
	function updatePrivacyPreference($member_id, $prefs){
		global $db;
		$member_id = intval($member_id);
		$prefs = serialize($prefs);

		$sql = 'UPDATE '.TABLE_PREFIX."privacy_preferences SET preference='$prefs' WHERE member_id=.$member_id";
		$result = mysql_query($sql, $db);
		return $result;
	}
}
?>
