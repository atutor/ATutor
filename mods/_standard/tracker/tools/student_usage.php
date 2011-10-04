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
			FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."course_enrollment C 
			WHERE M.member_id=C.member_id AND C.course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);

$_GET['id'] = intval($_GET['id']);

$sql = "SELECT counter, content_id, SEC_TO_TIME(duration) AS total FROM ".TABLE_PREFIX."member_track WHERE member_id=$_GET[id] AND course_id=$_SESSION[course_id] ORDER BY counter DESC";
$result_list = mysql_query($sql, $db);
$savant->assign('result_list', $result_list);
$savant->assign('result', $result);
$savant->display('instructor/content/tracker/student_usage.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>