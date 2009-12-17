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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_SOCIAL_INCLUDE.'constants.inc.php');
require(AT_SOCIAL_INCLUDE.'friends.inc.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroup.class.php');
require(AT_SOCIAL_INCLUDE.'classes/SocialGroups/SocialGroups.class.php');
$_custom_css = $_base_path . AT_SOCIAL_BASENAME . 'module.css'; // use a custom stylesheet
if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

//social groups init
$social_groups = new SocialGroups();
$rand_key = $addslashes($_REQUEST['rand_key']);	//should we excape?

//if $_GET['q'] is set, handle Ajax.
if (isset($_GET['q'])){
	$query = $addslashes($_GET['q']);
	$search_result = $social_groups->search($query);
	if (!empty($search_result)){
		echo '<div style="border:1px solid #a50707; margin-left:50px; width:45%;">Suggestion:<br/>';
		$counter = 0;
		foreach($search_result as $group_id=>$group_array){
			//display 10 suggestions
			if ($counter > 10){
				break;
			}

			$group_obj = $group_array['obj'];
			/* A bit of a hack here
			 * Escape XSS for the ajax search. Problem: the ' and " is changed to its entities.
			 *
			 * @Apr 2, 2009 - Harris
			 */
			echo '<a href="javascript:void(0);" onclick="document.getElementById(\'search_groups\').value=\''.htmlentities_utf8($group_obj->getName()).'\'; document.getElementById(\'search_group_form\').submit();">'.$group_obj->getName().'</a><br/>';
			$counter++;
		}
		echo '</div>';
	}
	exit;
}

//paginator settings
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * SOCIAL_GROUP_MAX) + 1;
$offset = ($page-1) * SOCIAL_GROUP_MAX;


// handle post request
if ($rand_key!='' && isset($_REQUEST['search_groups_'.$rand_key])){
	$query = $addslashes($_REQUEST['search_groups_'.$rand_key]);
	$search_result = $social_groups->search($query);
	$num_pages = sizeof($search_result)/SOCIAL_GROUP_MAX;	
	$search_result = $social_groups->search($query, $offset);
}
/*elseif(empty($_POST['search_groups_'.$rand_key])) {
	$msg->addError('CANNOT_BE_EMPTY');
} */

//Generate a random number for the search input name fields, so that the browser will not remember any previous entries.
$rand = md5(rand(0, time())); 
if ($rand_key != ''){
	$last_search = $_REQUEST['search_groups_'.$rand_key];
} else {
	$last_search = $_REQUEST['search_groups_'.$rand];	
}
//take out double quotes until there is a way to escape XSS from the ajax script.
$last_search = preg_replace('/\"/', '', $last_search);

//Display
include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('pubmenu.tmpl.php');
print_paginator($page, $num_pages, 'search_groups_'.$rand_key.'='.$query.SEP.'rand_key='.$rand_key, 1); 
$savant->assign('rand_key', $rand);
$savant->assign('last_search', $last_search);
$savant->assign('search_result', $search_result);
$savant->display('sgroup_search.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>
