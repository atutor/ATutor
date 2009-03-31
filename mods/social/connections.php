<?php
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyObject.class.php');
require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyController.class.php');
$_custom_css = $_base_path . 'mods/social/module.css'; // use a custom stylesheet

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// default display my friends
$friends = getFriends($_SESSION['member_id']);
$rand_key = $addslashes($_POST['rand_key']);	//should we excape?


//if $_GET['q'] is set, handle Ajax.
if (isset($_GET['q'])){
	$query = $addslashes($_GET['q']);

	if (isset($_POST['myFriendsOnly'])){
		//retrieve a list of my friends
		$search_result = searchFriends($query, true);
	} else {
		//retrieve a list of friends by the search
		$search_result = searchFriends($query);
	}

	if (!empty($search_result)){
		echo '<div class="suggestions">'._AT('suggestions').':<br/>';
		$counter = 0;
		foreach($search_result as $member_id=>$member_array){
			//display 10 suggestions
			if ($counter > 10){
				break;
			}

			echo '<a href="javascript:void(0);" onclick="document.getElementById(\'search_friends\').value=\''.printSocialName($member_id, false).'\'; document.getElementById(\'search_friends_form\').submit();">'.printSocialName($member_id, false).'</a><br/>';
			$counter++;
		}
		echo '</div>';
	}
	exit;
}

//safe guard
if (isset($_GET['id'])){
	$id = intval($_GET['id']);
	if($id > 0){
		addFriendRequest($id);
		$msg->addFeedback('REQUEST_FRIEND_ADDED');
		header('Location: '.url_rewrite('mods/social/connections.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}
}

//handle search friends request
if($rand_key!='' && isset($_POST['search_friends_'.$rand_key])){
	if (empty($_POST['search_friends_'.$rand_key])){
		$msg->addError('CANNOT_BE_EMPTY');
		header('Location: '.url_rewrite('mods/social/connections.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}
	$search_field = $addslashes($_POST['search_friends_'.$rand_key]);	
	if (isset($_POST['myFriendsOnly'])){
		//retrieve a list of my friends
		$friends = searchFriends($search_field, true);
	} else {
		//retrieve a list of friends by the search
		$friends = searchFriends($search_field);
	}
} 

//mark those that are already added
$friends = markFriends($_SESSION['member_id'], $friends);

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('friends', $friends);
$savant->assign('rand_key', $rand_key);
$savant->display('connections.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>