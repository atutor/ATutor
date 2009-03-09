<?php
define('AT_INCLUDE_PATH', '../../include/');
require_once(AT_SOCIAL_INCLUDE.'classes/Activity.class.php');
require_once(AT_SOCIAL_INCLUDE.'classes/Member.class.php');

/** 
 * This function will return a list of the member's member Object.
 * $obj->url should retrieve the member's profile link.
 *
 * @param	int		the person who we want to get friends from
 *
 * TODO: Need optimization, too slow.
 */
function getFriends($member_id){
	global $db, $addslashes;
	$friends = array();
	
	//All member_id = member_id, and All friend_id = member_id
	$sql = 'SELECT F.member_id AS member_id, F.friend_id AS friend_id FROM '.TABLE_PREFIX.'friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE (F.member_id='.$member_id.' OR F.friend_id='.$member_id.')';
	$result = mysql_query($sql, $db);

	while ($row = mysql_fetch_assoc($result)){
		if ($row['member_id']==$member_id){
			//member_id=member_id case
			$friends[$row['friend_id']]			=	new Member($row['friend_id']);
	//		$friends[$row['member_id']]['first_name']	=	$row['first_name'];
	//		$friends[$row['member_id']]['last_name']	=	$row['last_name'];
		} else {
			//friend_id = member_id
			$friends[$row['member_id']]			=	new Member($row['member_id']);
	//		$friends[$row['friend_id']]['first_name']	=	$row['first_name'];
	//		$friends[$row['friend_id']]['last_name']	=	$row['last_name'];
		}
	}
/*
	//All friend_id = member_id
	$sql = 'SELECT F.member_id AS member_id, F.friend_id AS friend_id FROM '.TABLE_PREFIX.'friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE F.friend_id='.$member_id;
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
	$sql = 'SELECT * FROM '.TABLE_PREFIX.'friend_requests WHERE friend_id='.$_SESSION['member_id'];
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
	$sql = "INSERT INTO ".TABLE_PREFIX."friends SET member_id=$_SESSION[member_id], friend_id=$friend_id";
	$is_succeeded = mysql_query($sql, $db);	
	//remove the equivalent friend request
	if ($is_succeeded){
		$is_succeeded = removeFriendRequest($friend_id, $_SESSION['member_id']);
	}

	//add to activities log
	if ($is_succeeded){
		$act = new Activity();		
		$str1 = 'and '.printSocialName($friend_id).' are now friends.';
		$act->addActivity($_SESSION['member_id'], $str1);
		$str2 = 'and '.printSocialName($_SESSION['member_id']).' are now friends.';
		$act->addActivity($friend_id, $str2);
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
	$sql = 'DELETE FROM '.TABLE_PREFIX."friend_requests WHERE member_id=$member_id AND friend_id=$friend_id";
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

	$sql = "INSERT INTO ".TABLE_PREFIX."friend_requests SET member_id=$_SESSION[member_id], friend_id=$friend_id";
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

	$sql = 'DELETE FROM '.TABLE_PREFIX.'friends WHERE (member_id='.$_SESSION['member_id'].' AND '.'friend_id='.$friend_id.') OR (friend_id='.$_SESSION['member_id'].' AND '.'member_id='.$friend_id.')';
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

	//break the names by space, then accumulate the query
	$name = $addslashes($name);	
	$sub_names = explode(' ', $name);
	foreach($sub_names as $piece){
		$query .= "(first_name LIKE '%$piece%' OR second_name LIKE '%$piece%' OR last_name LIKE '%$piece%' OR email LIKE '$piece') AND ";
	}
	//trim back the extra "AND "
	$query = substr($query, 0, -4);
	if ($searchMyFriends == true){
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE (F.member_id='.$_SESSION['member_id'].' OR F.friend_id='.$_SESSION['member_id'].') AND ';
	} else {
		/*
		* Harris note:
		* IF the search my friend is off, then it should search all members inside that table
		* don't know what i did the search inside [friends x members]
		* end Harris note;
		*/
		//$sql = 'SELECT * FROM '.TABLE_PREFIX.'friends F LEFT JOIN '.TABLE_PREFIX.'members M ON F.friend_id=M.member_id WHERE ';
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'members M WHERE ';
	}
	$sql = $sql . $query;
	$rs = mysql_query($sql, $db);

	//Get all members out
	while($row = mysql_fetch_assoc($rs)){
		$temp =& $result[$row['member_id']];
		$temp['first_name'] = $row['first_name'];
		$temp['last_name'] = $row['last_name'];
	}
	return $result;
}


/**
 * Given an array list of friends, this function will add an attribute 'added' into the array if this person is already connected to the user.
 * @param	int		the user id
 * @param	array	the given array of friends
 */
 function markFriends($id, $connections){
	//get all friends
	$my_friends = getFriends($id);
	foreach($my_friends as $friends){
		//if it is in the connection, set the attribute
		if($connections[$friends->id] != null){
			$connections[$friends->id]['added'] = 1;
		}
	}
	return $connections;
 }


/** 
  * Print social name, with AT_print and profile link 
  * return	the name to be printed.
  */
function printSocialName($id){
	$str .= AT_print(get_display_name($id), 'members.full_name');
	return getProfileLink($id, $str);
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