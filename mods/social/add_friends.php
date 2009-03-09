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

//safe guard
$friends = array();
if (isset($_GET['id'])){
	$id = intval($_GET['id']);
	if($id > 0){
		addFriendRequest($id);
		header('Location: '.url_rewrite('mods/social/add_friends.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}
}

//handle search friends request
if(isset($_POST['search'])){
	if (empty($_POST['searchFriends'])){
		$msg->addError('CANNOT_EMPTY');
		header('Location: '.url_rewrite('mods/social/add_friends.php'));
		exit;
	}
	$_POST['searchFriends'] = $addslashes($_POST['searchFriends']);	
	if (isset($_POST['myFriendsOnly'])){
		//retrieve a list of my friends
		$friends = searchFriends($_POST['searchFriends'], true);
	} else {
		//retrieve a list of friends by the search
		$friends = searchFriends($_POST['searchFriends']);
	}

	//mark those that are already added
	$friends = markFriends($_SESSION['member_id'], $friends);
} 

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('friends', $friends);
$savant->display('add_friends.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>