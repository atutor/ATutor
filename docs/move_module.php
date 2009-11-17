<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2009                                            */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

global $addslashes;

// handle ajax post request from course index page and student tools index page
if (isset($_POST['from']))
{
	$from = $_POST['from'];
	if ($_POST['moved_modules'] <> '') $final_home_links = $addslashes(str_replace('-', '/', $_POST['moved_modules']));
}

// handle ajax post request to remove module from course index page and student tools index page
if ($_POST['remove'] <> '')
{
	$remove_module = $_POST['remove'];
	
	if ($from == 'course_index')
		$sql = "SELECT home_links links FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
	else if ($from == 'student_tools')
		$sql = "SELECT links FROM ".TABLE_PREFIX."fha_student_tools WHERE course_id=$_SESSION[course_id]";

	$result = mysql_query($sql, $db);
	$row= mysql_fetch_assoc($result);

	if (substr($row['links'], 0, strlen($remove_module)) == $remove_module)
		$final_home_links = substr($row['links'], strlen($remove_module)+1);
	else
		$final_home_links = preg_replace('/\|'.preg_quote($remove_module, '/').'/', '', $row['links']);
}

// save the module display order into db
if ($from == 'course_index')
	$sql = "UPDATE ".TABLE_PREFIX."courses SET home_links='$final_home_links' WHERE course_id=$_SESSION[course_id]";
else if ($from == 'student_tools')
	$sql    = "UPDATE ".TABLE_PREFIX."fha_student_tools SET links='$final_home_links' WHERE course_id=$_SESSION[course_id]";

$result = mysql_query($sql, $db);
?>