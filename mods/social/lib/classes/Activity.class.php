<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

/**
 * This class is designed to handle the transactions for all the news, updates and
 * activities that are issued by the user.
 *
 */
class Activity{
	//constructor
	function Activity(){}

	/** 
	 * Adds a new activity into the database.
	 * @param	int		user id
	 * @param	string	the message of that describe this activity
	 * @param	int		the application that's linked with this activity.  Purpose is to display it with a link.
	 * TODO: What happens if title is empty? Don't add it?
	 */
	function addActivity($id, $title, $app_id=0){
		global $db, $addslashes;
		$id = intval($id);
		$app_id = intval($app_id);
		$app_string = '';

		if ($app_id > 0){
			$app_string = ", application_id=$app_id";
			//overwrite title with an automated message
			$title = $this->generateApplicationTitle($app_id);
		}

		if ($id > 0 && $title!=''){
			$sql = 'INSERT INTO '.TABLE_PREFIX."social_activities SET member_id=$id, title='$title'".$app_string;
			mysql_query($sql, $db);
		}
	}


	/** 
	 * Retrieve this user's activity
	 * 
	 * @param	int		user id.
	 * @param	boolean	set TRUE to display all entry		
	 * @return	The array of description of all the activities from the given user.
	 */
	function getActivities($id, $displayAll=false){
		global $db;
		$activities = array();
		$id = intval($id);
		if ($id > 0){
			$sql = 'SELECT * FROM '.TABLE_PREFIX."social_activities WHERE member_id=$id ORDER BY created_date DESC";
			if (!$displayAll){
				$sql .= ' LIMIT '.SOCIAL_FRIEND_ACTIVITIES_MAX;
			}
			$result = mysql_query($sql, $db);
			if ($result){
				while($row = mysql_fetch_assoc($result)){
					$activities[$row['id']] = $row['title'];
				}
			}
			return $activities;
		}
		return;
	}


	/**
	 * Remove an activity
	 *
	 * @param	int		user id
	 * @return	true if activity is deleted.
	 */
	function deleteActivity($id){
		global $db;

		$id = intval($id);
		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_activities WHERE member_id='.$_SESSION['member_id'].' AND id='.$id;
		mysql_query($sql, $db);
		if (mysql_affected_rows() > 0){
			return true;
		} else  {
			return false;
		}
	}

	
	/**
	 * Retrieve friends' recent activities
	 *
	 * @param	int		user id
	 * @param	boolean	set TRUE to display all entry		
	 * @return	The array of description of all the activities of the given user's friends.
	 */
	 function getFriendsActivities($id, $displayAll=false){
		global $db;
		$activities = array();

		$friends = getFriends($id);	
		$friends_ids = implode(', ', array_keys($friends));
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_activities WHERE member_id IN ('.$friends_ids.') ORDER BY created_date DESC';
		if (!$displayAll){
			$sql .= ' LIMIT '.SOCIAL_FRIEND_ACTIVITIES_MAX;
		}
		$result = mysql_query($sql, $db);

		if ($result){
			while($row = mysql_fetch_assoc($result)){
				$activities[$row['id']]['member_id'] = $row['member_id'];
				$activities[$row['id']]['title'] = $row['title'];
				$activities[$row['id']]['created_date'] = $row['created_date'];
			}
		}
		return $activities;
	 }


	 /** 
	  * Generate the title string for application.
	  *
	  * @param	int		application id
	  * @return	the title string that has a hyperlink to the application itself.
	  */
	 function generateApplicationTitle($app_id){
		global $db;
		$app_id = intval($app_id);
		
		//This here, it is actually better to use $url instead of app_id.
		//$url is the primary key.  $id is also a key, but it is not guranteed that it will be unique
		$sql = 'SELECT title FROM '.TABLE_PREFIX."social_applications WHERE id=$app_id";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		
		$msg = _AT("has_added_app", url_rewrite(AT_SOCIAL_BASENAME.'applications.php?app_id='.$app_id),
			htmlentities_utf8($row['title']));
		return $msg;
	 }
}
?>
