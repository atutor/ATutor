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
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CONTENT);

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT M.member_id, M.login, CONCAT(M.first_name, ' ', M.second_name, ' ', M.last_name) AS full_name
			FROM %smembers M, %scourse_enrollment C 
			WHERE M.member_id=C.member_id AND C.course_id=%d";
$rows_members = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id']));

$_GET['id'] = intval($_GET['id']);

$sql = "SELECT counter, content_id, SEC_TO_TIME(duration) AS total FROM %smember_track WHERE member_id=%d AND course_id=%d ORDER BY counter DESC";
$rows_list = queryDB($sql, array(TABLE_PREFIX, $_GET['id'], $_SESSION['course_id']));

$savant->assign('rows_list', $rows_list);
$savant->assign('rows_members', $rows_members);
$savant->display('instructor/content/tracker/student_usage.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>