<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $db;
global $savant;
global $_stacks;
global $system_courses;
?>

<?php

//debug($this->side_menu);

$side_menu = explode("|", $system_courses[$_SESSION['course_id']]['side_menu']);

if (isset($side_menu)) {
	foreach ($side_menu as $dropdown_file) {
		require(AT_INCLUDE_PATH . 'html/dropdowns/' . $dropdown_file . '.inc.php');
	}
} // else,  defaults weren't set in the db and should've been

?>