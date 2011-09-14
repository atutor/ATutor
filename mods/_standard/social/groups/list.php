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
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
//require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyObject.class.php');
//require(AT_SOCIAL_INCLUDE.'classes/PrivacyControl/PrivacyController.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet

$id = intval($_REQUEST['id']);
if ($id < 1){
	exit;
}

// default display all group members
$grp_obj = new SocialGroup($id);
$grp_members = $grp_obj->getGroupMembers();

$rand_key = $addslashes($_POST['rand_key']);	//should we excape?

//if $_GET['q'] is set, handle Ajax.
if (isset($_GET['q'])){
	$query = $addslashes($_GET['q']);
	$search_result = $grp_obj->searchMembers($query);

	if (!empty($search_result)){
		echo '<div class="suggestions">'._AT('suggestions').':<br/>';
		$counter = 0;
		foreach($search_result as $member_id=>$member_obj){
			//display 10 suggestions
			if ($counter > 10){
				break;
			}
			echo '<a href="javascript:void(0);" onclick="document.getElementById(\'search_friends\').value=\''.printSocialName($member_obj->getID(), false).'\'; document.getElementById(\'search_friends_form\').submit();">'.printSocialName($member_obj->getID(), false).'</a><br/>';
			$counter++;
		}
		echo '</div>';
	}
	exit;
}

//handle search friends request
if($rand_key!='' && isset($_POST['search_friends_'.$rand_key])){
	if (empty($_POST['search_friends_'.$rand_key])){
		$msg->addError('CANNOT_BE_EMPTY');
		header('Location: '.url_rewrite(AT_SOCIAL_BASENAME.'groups/list.php?id='.$id, AT_PRETTY_URL_IS_HEADER));
		exit;
	}
	$search_field = $addslashes($_POST['search_friends_'.$rand_key]);
	$grp_members = $grp_obj->searchMembers($search_field);
} 


//handle delete friends request
if (isset($_GET['remove']) && isset($_GET['member_id'])){
	//saveguard
	$member_id = $_GET['member_id'];

	//validate if this is the creator of group
	if($_SESSION['member_id']==$grp_obj->getUser()){
		$grp_obj->removeMember($member_id);
		$msg->addFeedback('GRUOP_MEMBER_REMOVED');
		header('Location: '.url_rewrite(AT_SOCIAL_BASENAME.'groups/list.php?id='.$id, AT_PRETTY_URL_IS_HEADER));
		exit;
	}
}

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('social/pubmenu.tmpl.php');
$savant->assign('grp_obj', $grp_obj);
$savant->assign('grp_members', $grp_members);
$savant->assign('rand_key', $rand_key);
$savant->display('social/sgroup_list.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>