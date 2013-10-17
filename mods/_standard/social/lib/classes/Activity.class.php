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
		$id = intval($id);
		$app_id = intval($app_id);
		$app_string = '';

		if ($app_id > 0){
			$app_string = ", application_id=$app_id";
			//overwrite title with an automated message
			$title = $this->generateApplicationTitle($app_id);
		}

		if ($id > 0 && $title!=''){

			$sql = "INSERT INTO %ssocial_activities SET member_id=%d, title='%s'".$app_string;
			queryDB($sql, array(TABLE_PREFIX, $id, $title));
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
		//global $db;
		$activities = array();
		$id = intval($id);
		if ($id > 0){
		   $sql = "SELECT * FROM %ssocial_activities WHERE member_id=%d ORDER BY created_date DESC";
	
			if (!$displayAll){
				$sql .= ' LIMIT '.SOCIAL_FRIEND_ACTIVITIES_MAX;
			}
			$rows_activities = queryDB($sql, array(TABLE_PREFIX, $id));
			 if(count($rows_activities) > 0){
		        foreach($rows_activities as $row){
					$activities[$row['id']]['member_id'] = $row['member_id'];
					$activities[$row['id']]['title'] = $row['title'];
					$activities[$row['id']]['created_date'] = $row['created_date'];
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
		$id = intval($id);
		$sql = 'DELETE FROM %ssocial_activities WHERE member_id=%d AND id=%d';
		$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $id));
		if($result > 0){
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
		$activities = array();
		$friends = getFriends($id);	
		if($friends > 0){
            $friends_ids = implode(', ', array_keys($friends));
            if($friends_ids != ''){
                $sql = 'SELECT * FROM %ssocial_activities WHERE member_id IN (%s) ORDER BY created_date DESC';
                if (!$displayAll){
                    $sql .= ' LIMIT '.SOCIAL_FRIEND_ACTIVITIES_MAX;
                }
                $rows_activities = queryDB($sql, array(TABLE_PREFIX, $friends_ids));
                if (count($rows_activities) > 0){
                    foreach($rows_activities as $row){
                        $activities[$row['id']]['member_id'] = $row['member_id'];
                        $activities[$row['id']]['title'] = $row['title'];
                        $activities[$row['id']]['created_date'] = $row['created_date'];
                    }
                }
                return $activities;
            }else{
                return false;
            }
        }
	 }


	 /** 
	  * Generate the title string for application.
	  *
	  * @param	int		application id
	  * @return	the title string that has a hyperlink to the application itself.
	  */
	 function generateApplicationTitle($app_id){
		$app_id = intval($app_id);
		
		//This here, it is actually better to use $url instead of app_id.
		//$url is the primary key.  $id is also a key, but it is not guranteed that it will be unique
		$sql = "SELECT title FROM %ssocial_applications WHERE id=%d";
		$row = queryDB($sql, array(TABLE_PREFIX, $app_id), TRUE);
		
		$msg = _AT("has_added_app", url_rewrite(AT_SOCIAL_BASENAME.'applications.php?app_id='.$app_id, AT_PRETTY_URL_IS_HEADER),
			htmlentities_utf8($row['title']));
		return $msg;
	 }
}
?>
