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
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyObject.class.php');
require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyController.class.php');
$_custom_css = $_base_path . 'mods/social/module.css'; // use a custom stylesheet

/*
if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
*/

// default display my friends
$friends = getFriends($_SESSION['member_id']);
$rand_key = $addslashes($_POST['rand_key']);	//should we excape?

//paginator settings
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * SOCIAL_FRIEND_SEARCH_MAX) + 1;
$offset = ($page-1) * SOCIAL_FRIEND_SEARCH_MAX;


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
		$sql_notify = "SELECT first_name, last_name, email FROM ".TABLE_PREFIX."members WHERE member_id=$id";
		$result_notify = mysql_query($sql_notify, $db);
		$row_notify = mysql_fetch_assoc($result_notify);

		if ($row_notify['email'] != '') {
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			$body = _AT('notification_new_contact', get_display_name($_SESSION['member_id']), $_base_href.'mods/social/index_mystart.php');
			$sender = get_display_name($_SESSION['member_id']);
			$mail = new ATutorMailer;
			$mail->AddAddress($row_notify['email'], $sender);
			$mail->FromName = $_config['site_name'];
			$mail->From     = $_config['contact_email'];
			$mail->Subject  = _AT('contact_request');
			$mail->Body     = $body;

			if(!$mail->Send()) {
				$msg->addError('SENDING_ERROR');
			}
			unset($mail);
		}

		header('Location: '.url_rewrite('mods/social/connections.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}
}

//handle search friends request
if(($rand_key!='' && isset($_POST['search_friends_'.$rand_key])) || isset($_GET['search_friends'])){
	if (empty($_POST['search_friends_'.$rand_key]) && !isset($_GET['search_friends'])){
		$msg->addError('CANNOT_BE_EMPTY');
		header('Location: '.url_rewrite('mods/social/connections.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}
	//to adapt paginator GET queries
	if($_GET['search_friends']){
		$search_field = $addslashes($_GET['search_friends']);
	} else {
		$search_field = $addslashes($_POST['search_friends_'.$rand_key]);	
	}
	if (isset($_POST['myFriendsOnly'])){
		//retrieve a list of my friends
		$friends = searchFriends($search_field, true);
	} else {
		//retrieve a list of friends by the search
		$friends = searchFriends($search_field);	//to calculate the total number. TODO: need a better way, wasting of runtime.
		$num_pages = max(ceil(sizeof($friends) / SOCIAL_FRIEND_SEARCH_MAX), 1);
		$friends = searchFriends($search_field, false, $offset);
	}
} 	

//mark those that are already added
$friends = markFriends($_SESSION['member_id'], $friends);
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('pubmenu.tmpl.php');
print_paginator($page, $num_pages, 'search_friends='.$search_field, 1); 
$savant->assign('friends', $friends);
$savant->assign('rand_key', $rand_key);
$savant->display('connections.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>