<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http:/atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_CONTENT);

/* Getting content id from page that reffered */
$content_id = intval($_GET['content_id']);

$_pages['mods/_standard/tracker/tools/page_student_stats.php']['title'] = $contentManager->_menu_info[$content_id]['title'];
$_pages['mods/_standard/tracker/tools/page_student_stats.php']['parent'] = 'mods/_standard/tracker/tools/index.php';

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT counter, content_id, member_id, SEC_TO_TIME(duration) AS total, SEC_TO_TIME(duration/counter) AS average FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id] AND content_id=$content_id ORDER BY total DESC";
$result = mysql_query($sql, $db);

$savant->assign('result', $result); 
$savant->display('instructor/content/page_student_stats.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>