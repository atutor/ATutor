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

//define('AT_INCLUDE_PATH', '../../../include/');
require_once(AT_SOCIAL_INCLUDE.'classes/Activity.class.php');
require_once(AT_SOCIAL_INCLUDE.'classes/Member.class.php');

/** 
 * Get all the connections from the given member id
 * $obj->url should retrieve the member's profile link.
 *
 * @param	int		the person who we want to get friends from
 * @param	int		the max number of friends to be returned
 *
 * TODO: Need optimization, too slow.
 */
function getFriends($member_id, $limit=0){
	$friends = array();
	
	//All member_id = member_id, and All friend_id = member_id
	$sql = 'SELECT F.member_id AS member_id, F.friend_id AS friend_id FROM %ssocial_friends F LEFT JOIN %smembers M ON F.friend_id=M.member_id WHERE (F.member_id=%d OR F.friend_id=%d)';

	if ($limit > 0){
		$sql .= ' ORDER BY RAND() LIMIT '.$limit;
	}

	$rows_friends = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $member_id, $member_id));
	
	if(count($rows_friends) > 0){
		foreach($rows_friends as $row){
			if ($row['member_id']==$member_id){
				//member_id=member_id case
				$friends[$row['friend_id']]			=	$row['friend_id'];
			} else {
				//friend_id = member_id
				$friends[$row['member_id']]			=	$row['member_id'];
			}
		}
	}

	return $friends;
}


/**
 * Decide rather these two people are strictly friend of friend.  If they are already friends, return false.
 *
 * @param	int		person A's member_id
 * @param	int		person B's member_id
 * @return	true if they are friend of friend.
 */
function isFriendOfFriend($member_a, $member_b){
	$member_a = intval($member_a);
	$member_b = intval($member_b);
	$friends_of_a = getFriends($member_a);
	
	//if these two are already friends
	if(isset($friends_of_a[$member_b])){
		return false;
	}
	
	$friends_of_b = getFriends($member_b);
	if(!empty($friends_of_b) && !empty($friends_of_a)){
	    $fof = array_intersect($friends_of_a, $friends_of_b);	//friends of friends
    }
	//If it is not empty or not null, then they have friends 
	if (!empty($fof) > 0){
		return true;
	}
	return false;
}


/**
 * Get a list of people you may know
 */
function getPeopleYouMayKnow(){
	$counter = 0;
	$people_you_may_know = array();
	$pending_requests = getPendingRequests(true);

	$sql = 'SELECT MAX(member_id) as max_member FROM %smembers';
	$row_member = queryDB($sql, array(TABLE_PREFIX), TRUE);
	
	if(count($row_member) >0){
		$max_id = $row_member['max_member'];
	} else {
		return null;
	}

	//if we ran out of people, quit;
	while($counter++ < $max_id){
		//if we get enough people we might know, quit; 
		if (sizeof($people_you_may_know) >= SOCIAL_NUMBER_OF_PEOPLE_YOU_MAY_KNOW){
			break;
		}
		//get new random member
		$random_member = rand(1, $max_id);	//seed is generated automatically since php 4.2.0

		//if this person is myself, next
		if ($random_member==$_SESSION['member_id']){
			continue;
		}

		//if this person is already on pending, next.
		if (isset($pending_requests[$random_member])){
			continue;
		}
		

		//if we have added this random number before, next.
		if (in_array($random_member, $people_you_may_know)){
			continue;
		}

		if (isFriendOfFriend($_SESSION['member_id'], $random_member)){
			$people_you_may_know[] = $random_member;
		}
	}
	return $people_you_may_know;
}


/**
 * Returns a list of friend requests to me/by me depends on the flag
 * @param	flag, true for requests made by me, false for requests made to me. DEFAULT: false
 * @return	array of friend requests
 */
function getPendingRequests($request_by_me=false){
	/* NOTE: This table is not bilinear, unlike the friends table.
	 * In this table, please do not be confused, member_id is the one who requests
	 * friend_id is the member that needs to approve/reject.  Thus, when we want to retrieve 
	 * pending requests, we need to pull entries from friend_id.
	 */	
	$requests = array();

	if ($request_by_me==true) {
		$sql = 'SELECT friend_id AS id FROM %ssocial_friend_requests WHERE member_id=%d';
	} else {
		$sql = 'SELECT member_id AS id FROM %ssocial_friend_requests WHERE friend_id=%d';
	}

	$rows_friends = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));

	foreach($rows_friends as $row){
		$requests[$row['id']] =	new Member($row['id']);
	}
	return $requests;
}


/**
 * This function adds a friend to the database, remove the equivalent friend request.
 *
 * @param	int		the member being approved, not "MYSELF"
 */
function approveFriendRequest($friend_id){
	$friend_id = intval($friend_id);

	if ($friend_id < 1){
		return;
	}
	//TODO: hardcoded relationship = 1
	$sql = "INSERT INTO %ssocial_friends SET member_id=%d, friend_id=%d, relationship=1";
	$is_succeeded = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $friend_id));	
	//remove the equivalent friend request
	if ($is_succeeded > 0){
		$is_succeeded = removeFriendRequest($friend_id, $_SESSION['member_id']);
	}

	//add to activities log
	if ($is_succeeded > 0){
		$act = new Activity();		
		$str1 = _AT('now_friends1', printSocialName($friend_id)); 
		$act->addActivity($_SESSION['member_id'], $str1);
		$str2 = _AT('now_friends2', printSocialName($_SESSION['member_id'])); 
		$act->addActivity($friend_id, $str2);
		unset($act);
	}
}


/**
 * Reject friend request
 * 
 * @param	int		the member being rejected, not "MYSELF"
 */
function rejectFriendRequest($friend_id){
	return removeFriendRequest($friend_id, $_SESSION['member_id']);
}


/**
 * Remove Friend request
 * @param	member_id	the one who make this request
 * @param	friend_id	the member that decide approval/reject on this request
 */
function removeFriendRequest($member_id, $friend_id){

	$sql = "DELETE FROM %ssocial_friend_requests WHERE member_id=%d AND friend_id=%d";
	$is_succeeded = queryDB($sql, array(TABLE_PREFIX, $member_id, $friend_id));
	ContactFeedback($is_succeeded);
	return $is_succeeded;
}


/**
  * This function adds a friend request to the database
  *
  * @param	friend_id	the member_id of the friend
  */
function addFriendRequest($friend_id){
	$friend_id = intval($friend_id);

	if ($friend_id < 1){
	 return;
	}
		
	$sql = "INSERT INTO %ssocial_friend_requests SET member_id=%d, friend_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $friend_id));	
 }
 

/**
 * This function removes a friend from the the user.
 *
 * @param	int		user id
 */
function removeFriend($friend_id){
	$friend_id = intval($friend_id);

	$sql = 'DELETE FROM %ssocial_friends WHERE (member_id=%d AND  friend_id=%d) OR (friend_id=%d AND member_id=%d)';
	$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $friend_id, $_SESSION['member_id'], $friend_id));

	ContactFeedback($result);

}


/**
 * This function will return a list of people from the network with the given name.  
 * @param	string	to be searched in the members table, can have space.
 * @param	[OPTIONAL] boolean	if true, will search only within this member.
 * @param	int		the number of entries to skip over from the result set. 
 * @return	array of friends of this member; id=>[first name, last name, profile picture]
 *
 * TODO: search needs work.  Order by the most matches to the least matches
 */ 
function searchFriends($name, $searchMyFriends = false, $offset=-1){
	global $addslashes;
	$result = array(); 
	$my_friends = array();
	$exact_match = false;

	//break the names by space, then accumulate the query
	if (preg_match("/^\\\\?\"(.*)\\\\?\"$/", $name, $matches)){
		$exact_match = true;
		$name = $matches[1];
	}
	$name = $addslashes($name);	
	$sub_names = explode(' ', $name);
	foreach($sub_names as $piece){
		if ($piece == ''){
			continue;
		}
                $piece = mysql_escape_string($piece);
		//if there are 2 double quotes around a search phrase, then search it as if it's "first_name last_name".
		//else, match any contact in the search phrase.
		if ($exact_match){
			$match_piece = "= '$piece' ";
		} else {
			//$match_piece = "LIKE '%$piece%' ";
			$match_piece = "LIKE '%%$piece%%' ";
		}
		if(!isset($query )){
		    $query = '';
		}
		$query .= "(first_name $match_piece OR second_name $match_piece OR last_name $match_piece OR login $match_piece ) AND ";
	}
	//trim back the extra "AND "
	$query = substr($query, 0, -4);

	//Check if this is a search on all people
	if ($searchMyFriends == true){
		//If searchMyFriend is true, return the "my friends" array
		//If the member_id is empty, (this happens when we are doing a search without logging in) then get all members?
		//else, use "my friends" array to distinguish which of these are already in my connection
		if(!isset($_SESSION['member_id'])){
			$sql = 'SELECT member_id FROM '.TABLE_PREFIX.'members WHERE ';
		} else {
			$sql = 'SELECT F.* FROM '.TABLE_PREFIX.'social_friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE (F.member_id='.$_SESSION['member_id'].') AND ';
			$sql .= $query;
			$sql .= ' UNION ';
			$sql .= 'SELECT F.* FROM '.TABLE_PREFIX.'social_friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.member_id=M.member_id WHERE (F.friend_id='.$_SESSION['member_id'].') AND ';
		}
		$sql .= $query;

		$rows_friends = queryDB($sql, array(), '', FALSE);
		
		if(count($rows_friends) > 0){
			foreach($rows_friends as $row){
				if ($row['member_id']==$_SESSION['member_id']){
					$this_id = $row['friend_id'];
				} else {
					$this_id = $row['member_id'];
				}
				$temp =& $my_friends[$this_id];	
				$temp['obj'] = new Member($this_id);
				if ($searchMyFriends){
					$temp['added'] = 1;
				}
			}
		}
		unset($this_id);  //don't want the following statements to reuse this
		return $my_friends;
	} else {
		/*
		* Harris' note:
		* IF the 'search my friend' is off, then it should search all members inside that table
		* don't know why i did the search inside [friends x members]
		* Also this query is gonna pull out all members cept 'myself'
		* raised a small problem for public use, cause there is no member_id
		* end note;
		*/
		//$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE ';
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'members M WHERE ';
		if (isset($_SESSION['member_id'])){
			$sql .= 'member_id!='.$_SESSION['member_id'].' AND ';
		}
	}
	$sql = $sql . $query;
	if ($offset >= 0){
		$sql .= " LIMIT $offset, ". SOCIAL_FRIEND_SEARCH_MAX;
	}

	$rows_members = queryDB($sql, array());

	//Get all members out
	foreach($rows_members as $row){
		$this_id = $row['member_id'];


		//skip empty entry, don't know why there would be empty entry. 
		//TODO: Trace this. could be a bug in query
		if ($this_id == ''){
			continue;
		}

		$temp =& $result[$this_id];	
		$temp['id'] = $this_id;

		//if this person exists in "my friends" list, mark it.
		if (isset($my_friends[$this_id])){
			$temp['added'] = 1;
		}
	} 
	return $result;
}


/**
 * Given an array list of friends, this function will add an attribute 'added' into the array if this person is already connected to the user.
 * @param	int		the user id
 * @param	array	the given array of friends
 * @return	marked array
 */
 function markFriends($id, $connections){
	//get all friends
	$my_friends = getFriends($id);
	$pending_requests = getPendingRequests(true);

	foreach($my_friends as $friends){
		//if it is in the connection, set the attribute
		if($connections[$friends] != null){
			$connections[$friends] = array();
			$connections[$friends]['added'] = 1;
		} 
	}

	foreach ($pending_requests as $friends=>$garbage){
		//if it is already added, set pending =1
		if ($connections[$friends] != null){
			$connections[$friends] = array();
			$connections[$friends]['pending'] = 1;
		}
	}
	return $connections;
 }


/**
 * Invite other members
 * @param	int		The member that we are going to invite
 * @param	int		The group id in which we are inviting the person to.
 */
 function addGroupInvitation($member_id, $group_id){

	 $sql = "INSERT INTO %ssocial_groups_invitations (sender_id, group_id, member_id) VALUES (%d, %d, %d)";
	 $result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $group_id, $member_id));
	 
	 if($result > 0){
		 return true;
	 } 
	 return false;
 }


/**
 * Get invitation from "ME", which is the logged in person
 * @return	list of groups id + sender_id
 */
 function getGroupInvitations(){
	$inv = array();

	$sql = 'SELECT * FROM %ssocial_groups_invitations WHERE member_id=%d';
	$rows_invites = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
	
	if(count($rows_invites) > 0){
		foreach($rows_invites as $row){
			$inv[$row['group_id']][] = $row['sender_id'];
		}
	}

	return $inv;
 }


 /** 
  * Get group requests for "ME", which is the logged in person.
  * @return	list of groups id + sender_id
  */
 function getGroupRequests(){
	 $requests = array();
	 $sql = 'SELECT * FROM %ssocial_groups_requests WHERE member_id=%d';
	 $rows_requests = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));
	 	
	 if(count($rows_requests) > 0){   
		foreach($rows_requests as $row){
			$requests[$row['group_id']][] = $row['sender_id'];
		}
	 }

	 return $requests;
 }


 /**
  * Accept "my" group invitation
  * @param	int		group id
  * @param	int		sender's member_id
  */
function acceptGroupInvitation($group_id){
	//will only add member if the group_id is valid.
	if ($group_id <= 0){
		return;
	}

	$sg = new SocialGroup($group_id);
	$isSucceeded = $sg->addMember($_SESSION['member_id']);

	if ($isSucceeded){
		removeGroupInvitation($group_id);
	}
}


 /**
  * Reject "my" group invitation
  */
 function rejectGroupInvitation($group_id){
	 return removeGroupInvitation($group_id);
 }



 /**
  * Remove "my" group invitation
  * @param	int		group id
  */
 function removeGroupInvitation($group_id){
	$group_id = intval($group_id);

	//doesn't need sender_id cause we want to remove all anyway.
	$sql = "DELETE FROM %ssocial_groups_invitations WHERE group_id=%d AND member_id=%d";
	$rows_invites = queryDB($sql, array(TABLE_PREFIX, $group_id, $_SESSION['member_id']));
	
	if(count($rows_invites) > 0){
		return true;
	}
	return false;
 }


 /** 
  * Accept the group request
  * @param	int		group id
  * @param	int		member that made this request
  */
 function acceptGroupRequest($group_id, $sender_id){
	//will only add member if the group_id is valid.
	if ($group_id <= 0){
		return;
	}

	$sg = new SocialGroup($group_id);
	$isSucceeded = $sg->addMember($sender_id);

	if ($isSucceeded){
		removeGroupRequest($group_id, $sender_id);
	}
 }


 /**
  * Reject the group request
  * @param	int		group id
  * @param  int		member that made this request
  */
 function rejectGroupRequest($group_id, $sender_id) {
	 return removeGroupRequest($group_id, $sender_id);
 }



 /** 
  * Remove group requests
  * @param	int		group id
   * @param  int		member that made this request
  */
 function removeGroupRequest($group_id, $sender_id){
	 $group_id = intval($group_id);
	 $sender_id = intval($sender_id);

	 $sql = "DELETE FROM %ssocial_groups_requests WHERE group_id=%d AND member_id=%d AND sender_id=%d";
	 $result = queryDB($sql, array(TABLE_PREFIX, $group_id, $_SESSION['member_id'], $sender_id));
	 
	 if($result > 0){
		 return true;
	 }
	 return false;
 }


/** 
  * Print social name, with AT_print and profile link 
  * @param	int		member id
  * @param	link	will return a hyperlink when set to true
  * return	the name to be printed.
  */
function printSocialName($id, $link=true){
    if(!isset($str)){
        $str = '';
    }
	$str .= AT_print(get_display_name($id), 'members.full_name');
	if ($link) {
		return getProfileLink($id, $str);
	} 
	return $str;
}


/** 
 * Mimic vital's print_profile_img function, but with a more customized image definition
 * @param	int	the member id
 * @param	1 for thumbnail, 2 for profile
 * @param	true will return a href link to the profile page, false otherwise
 * @return	the profile image link
 */
function printSocialProfileImg($id, $type=1, $link=true) {
	global $moduleFactory;
	$str = '';
	$username = htmlspecialchars(AT_print(get_display_name($id), 'members.full_name'), ENT_QUOTES, 'UTF-8');
	$mod = $moduleFactory->getModule('_standard/profile_pictures');
	if ($mod->isEnabled() === FALSE) {
		return;
	}
	if (profile_image_exists($id)) {
		if ($type==1){
			$str = '<img src="get_profile_img.php?id='.$id.'" alt="'.$username.'" />';
		} elseif ($type==2){
			$str = '<img src="get_profile_img.php?id='.$id.SEP.'size=p" alt="'.$username.'" />';
		}
	} else {
		$str = '<img src="'.AT_SOCIAL_BASENAME.'images/nophoto.gif" alt="'.$username.'" />';
	}
	if (!$link){
		return $str;
	}
	return getProfileLink($id, $str);
}


/**
 * Generate the a href link to the designated profile
 * For now, it's to sprofile, but in the future, we want ATutor + Opensocial to have only 1 profile, which should
 * point to the default atutor profile page.
 *
 * @private
 * @param	int		member id
 * @param	string	Image tag, or any string
 * @return	the hyperlink to the profile
 */
function getProfileLink($id, $str){
	$link = '<a href="'.url_rewrite(AT_SOCIAL_BASENAME.'sprofile.php?id='.$id, AT_PRETTY_URL_IS_HEADER).'">';
	$link .= $str;
	$link .= '</a>';
	return $link;
}

/**
 * This function will return a list of the member's activities 
 * @param	member_id	The id of the member we wish to get the activities from.
 */
function getMemberActivities($member_id){
}

function ContactFeedback($result){
    global $msg;
    if($result > 0){
        $msg->addFeedback('CONTACTS_UPDATED');
    } else {
        $msg->addFeedback('CONTACTS_UNCHANGED');
    }
}

?>
