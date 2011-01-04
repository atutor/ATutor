<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2009                                            */
/* Inclusive Design Institute                                           */
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
	
	// when pretty url is turned on, revert the pretty urls back to regular urls
	if ($_config['pretty_url'] > 0)
	{
		$home_links = explode('|', $final_home_links);
		$final_home_links = '';
		if (is_array($home_links))
		{
			foreach ($home_links as $link)
			{
				$url_parser = new UrlParser($link);
				$pathinfo = $url_parser->getPathArray();
				$final_home_links .= $pathinfo[1]->getPath(). $pathinfo[1]->getFileName(). '|';
			}
			$final_home_links = substr($final_home_links, 0, -1);
		}
	}
}

// handle ajax post request to remove module from course index page and student tools index page
if ($_POST['remove'] <> '')
{
	$remove_module = $_POST['remove'];
	
	// when pretty url is turned on, revert the pretty url back to regular url
	if ($_config['pretty_url'] > 0)
	{
		if (substr($remove_module, 0, 6) == 'go.php') $remove_module = substr($remove_module, 6);
		
		$url_parser = new UrlParser($remove_module);
		$pathinfo = $url_parser->getPathArray();
		$remove_module = $pathinfo[1]->getPath(). $pathinfo[1]->getFileName();
	}

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