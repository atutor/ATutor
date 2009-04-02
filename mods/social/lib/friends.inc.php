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

define('AT_INCLUDE_PATH', '../../include/');
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
	global $db, $addslashes;
	$friends = array();
	
	//All member_id = member_id, and All friend_id = member_id
	$sql = 'SELECT F.member_id AS member_id, F.friend_id AS friend_id FROM '.TABLE_PREFIX.'social_friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE (F.member_id='.$member_id.' OR F.friend_id='.$member_id.')';
	if ($limit > 0){
		$sql .= ' LIMIT '.$limit;
	}
	$result = mysql_query($sql, $db);

	while ($row = mysql_fetch_assoc($result)){
		if ($row['member_id']==$member_id){
			//member_id=member_id case
			$friends[$row['friend_id']]			=	$row['friend_id'];
//			$friends[$row['member_id']]['first_name']	=	$row['first_name'];
//			$friends[$row['member_id']]['last_name']	=	$row['last_name'];
		} else {
			//friend_id = member_id
			$friends[$row['member_id']]			=	$row['member_id'];
//			$friends[$row['friend_id']]['first_name']	=	$row['first_name'];
//			$friends[$row['friend_id']]['last_name']	=	$row['last_name'];
		}
	}
/*
	//All friend_id = member_id
	$sql = 'SELECT F.member_id AS member_id, F.friend_id AS friend_id FROM '.TABLE_PREFIX.'social_friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE F.friend_id='.$member_id;
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)){
		$friends[$row['member_id']]			=	new Member($row['member_id']);
//		$friends[$row['friend_id']]['first_name']	=	$row['first_name'];
//		$friends[$row['friend_id']]['last_name']	=	$row['last_name'];
	}
*/
	return $friends;
}


/**
 * Returns a list of "my" friend requests.
 * @return	array of friend requests
 */
function getPendingRequests(){
	global $db;
	/* NOTE: This table is not bilinear, unlike the friends table.
	 * In this table, please do not be confused, member_id is the one who requests
	 * friend_id is the member that needs to approve/reject.  Thus, when we want to retrieve 
	 * pending requests, we need to pull entries from friend_id.
	 */
	$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_friend_requests WHERE friend_id='.$_SESSION['member_id'];
	$rs = mysql_query($sql, $db);

	//myself=> pending objs
	while($row = mysql_fetch_assoc($rs)){
		$requests[$row['member_id']] =	new Member($row['member_id']);
	}
	return $requests;
}


/**
 * This function adds a friend to the database, remove the equivalent friend request.
 *
 * @param	int		the member being approved, not "MYSELF"
 */
function approveFriendRequest($friend_id){
	global $db;
	$friend_id = intval($friend_id);

	if ($friend_id < 1){
		return;
	}
	//TODO: hardcoded relationship = 1
	$sql = "INSERT INTO ".TABLE_PREFIX."social_friends SET member_id=$_SESSION[member_id], friend_id=$friend_id, relationship=1";
	$is_succeeded = mysql_query($sql, $db);	
	//remove the equivalent friend request
	if ($is_succeeded){
		$is_succeeded = removeFriendRequest($friend_id, $_SESSION['member_id']);
	}

	//add to activities log
	if ($is_succeeded){
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
	global $db;
	return removeFriendRequest($friend_id, $_SESSION['member_id']);
}


/**
 * Remove Friend request
 * @param	member_id	the one who make this request
 * @param	friend_id	the member that decide approval/reject on this request
 */
function removeFriendRequest($member_id, $friend_id){
	global $db;
	$sql = 'DELETE FROM '.TABLE_PREFIX."social_friend_requests WHERE member_id=$member_id AND friend_id=$friend_id";
	$is_succeeded = mysql_query($sql, $db);
	return $is_succeeded;
}


/**
  * This function adds a friend request to the database
  *
  * @param	friend_id	the member_id of the friend
  */
function addFriendRequest($friend_id){
	global $db;
	$friend_id = intval($friend_id);

	if ($friend_id < 1){
	 return;
	}

	$sql = "INSERT INTO ".TABLE_PREFIX."social_friend_requests SET member_id=$_SESSION[member_id], friend_id=$friend_id";
	mysql_query($sql, $db);		
 }
 

/**
 * This function removes a friend from the the user.
 *
 * @param	int		user id
 */
function removeFriend($friend_id){
	global $db;
	$friend_id = intval($friend_id);

	$sql = 'DELETE FROM '.TABLE_PREFIX.'social_friends WHERE (member_id='.$_SESSION['member_id'].' AND '.'friend_id='.$friend_id.') OR (friend_id='.$_SESSION['member_id'].' AND '.'member_id='.$friend_id.')';
	mysql_query($sql, $db);
}


/**
 * This function will return a list of people from the network with the given name.  
 * @param	string	to be searched in the members table, can have space.
 * @param	[OPTIONAL] boolean	if true, will search only within this member.
 * @return	array of friends of this member; id=>[first name, last name, profile picture]
 *
 * TODO: search needs work.  Order by the most matches to the least matches
 */ 
function searchFriends($name, $searchMyFriends = false){
	global $db, $addslashes;
	$result = array(); 
	$my_friends = array();

	//break the names by space, then accumulate the query
	$name = $addslashes($name);	
	$sub_names = explode(' ', $name);
	foreach($sub_names as $piece){
		$query .= "(first_name LIKE '%$piece%' OR second_name LIKE '%$piece%' OR last_name LIKE '%$piece%' OR email LIKE '$piece') AND ";
	}
	//trim back the extra "AND "
	$query = substr($query, 0, -4);

	//If searchMyFriend is true, return the "my friends" array
	//else, use "my friends" array to distinguish which of these are already in my connection
	$sql = 'SELECT F.* FROM '.TABLE_PREFIX.'social_friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE (F.member_id='.$_SESSION['member_id'].') AND ';
	$sql .= $query;
	$sql .= ' UNION ';
	$sql .= 'SELECT F.* FROM '.TABLE_PREFIX.'social_friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.member_id=M.member_id WHERE (F.friend_id='.$_SESSION['member_id'].') AND ';
	$sql .= $query;

	$rs = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($rs)){
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
	unset($this_id);  //don't want the following statements to reuse this

	//Check if this is a search on all people
	if ($searchMyFriends == true){
		return $my_friends;
	} else {
		/*
		* Harris' note:
		* IF the 'search my friend' is off, then it should search all members inside that table
		* don't know why i did the search inside [friends x members]
		* end note;
		*/
		//$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE ';
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'members M WHERE ';
	}
	$sql = $sql . $query;
	$rs = mysql_query($sql, $db);

	//Get all members out
	while($row = mysql_fetch_assoc($rs)){
		if ($row['member_id']==$_SESSION['member_id']){
			$this_id = $row['friend_id'];
		} else {
			$this_id = $row['member_id'];
		}
		$temp =& $result[$this_id];		
//		$temp['first_name'] = $row['first_name'];
//		$temp['last_name'] = $row['last_name'];

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

	foreach($my_friends as $friends){
		//if it is in the connection, set the attribute
		if($connections[$friends] != null){
			$connections[$friends]['added'] = 1;
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
	 global $db;

	 $sql = 'INSERT INTO '.TABLE_PREFIX."social_groups_invitations (sender_id, group_id, member_id) VALUES ($_SESSION[member_id], $group_id, $member_id)";
//	 echo $sql;
	 $result = mysql_query($sql, $db);
	 if($result){
		 return true;
	 } 
	 return false;
 }


/**
 * Get invitation from "ME", which is the logged in person
 * @return	list of groups id + sender_id
 */
 function getGroupInvitations(){
	global $db;
	$inv = array();

	$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_groups_invitations WHERE member_id='.$_SESSION['member_id'];

	$result = mysql_query($sql, $db);
	if ($result){
		while ($row = mysql_fetch_assoc($result)){
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
	 global $db;
	 $requests = array();

	 $sql = 'SELECT * FROM '.TABLE_PREFIX.'social_groups_requests WHERE member_id='.$_SESSION['member_id'];

	 $result = mysql_query($sql, $db);
	 if ($result){
		while ($row = mysql_fetch_assoc($result)){
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
	global $db;
	
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
	global $db;
	$group_id = intval($group_id);

	//delete invitation based on 3 primary keys
//	$sql = 'DELETE FROM '.TABLE_PREFIX."social_groups_invitations WHERE group_id=$group_id AND sender_id=$sender_id AND member_id=$member_id";
	//doesn't need sender_id cause we want to remove all anyway.
	$sql = 'DELETE FROM '.TABLE_PREFIX."social_groups_invitations WHERE group_id=$group_id AND member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	if ($result){
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
	global $db;
	
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
	 global $db;
	 $group_id = intval($group_id);
	 $sender_id = intval($sender_id);

	 $sql = 'DELETE FROM '.TABLE_PREFIX."social_groups_requests WHERE group_id=$group_id AND member_id=$_SESSION[member_id] AND sender_id=$sender_id";

	 $result = mysql_query($sql, $db);
	 if ($result){
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
	$str .= AT_print(get_display_name($id), 'members.full_name');
	if ($link) {
		return getProfileLink($id, $str);
	} 
	return $str;
}


/** 
 * Mimic vital's print_profile_img function, but with a more customized image definition
 * @param	int	the member id
 * @return	the profile image link
 */
function printSocialProfileImg($id) {
	global $moduleFactory;
	$str = '';
	$mod = $moduleFactory->getModule('_standard/profile_pictures');
	if ($mod->isEnabled() === FALSE) {
		return;
	}
	if (profile_image_exists($id)) {
		$str = '<img src="get_profile_img.php?id='.$id.'" alt="" />';
	} else {
		$str = '<img src="mods/social/images/nophoto.gif" alt="" />';
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
	$link = '<a href="mods/social/sprofile.php?id='.$id.'">';
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

?>