<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: edit.php 3111 2005-01-18 19:32:00Z joel $

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$_GET['id'] = intval($_GET['id']);
$_SESSION['last_visited_page'] = $_base_href.'profile.php?id='.$_GET['id'];

$sql	= 'SELECT member_id, login, website, first_name, second_name, last_name, email, private_email, phone FROM %smembers WHERE member_id=%d';
$profile_row = queryDB($sql,array(TABLE_PREFIX, $_GET['id']), TRUE);

if (count($profile_row) > 0) {	
	//get privs
	$sql	= 'SELECT `privileges`, approved FROM %scourse_enrollment WHERE member_id=%d';
	$row_en = queryDB($sql,array(TABLE_PREFIX, $_GET['id']));

	if ($system_courses[$_SESSION['course_id']]['member_id'] == $_GET['id']) {
		$status = _AT('instructor');
	} else if ( ($row_en['approved'] == 'y') && $row_en['privileges'] ) {
		$status = _AT('assistant');
	} else if ($row_en['approved'] == 'y') {
		$status = _AT('enrolled');
	}

	$_pages['profile.php']['title'] = _AT($display_name_formats[$_config['display_name_format']], $profile_row['login'], $profile_row['first_name'], $profile_row['second_name'], $profile_row['last_name']);

    $sql = 'SELECT id FROM %spa_albums WHERE member_id=%d AND type_id='.AT_PA_TYPE_PERSONAL;
    $aid = queryDB($sql, array(TABLE_PREFIX, $_GET['id']), TRUE);

	require(AT_INCLUDE_PATH.'header.inc.php');
    $savant->assign('aid', $aid['id']);
	$savant->assign('row', $profile_row);
	$savant->assign('status', $status);
	$savant->display('profile.tmpl.php');
} else {
	$msg->printErrors('NO_SUCH_USER');
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>