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

?>

<style type="text/css">
div.dropdown {
	width: 200px;
	padding: 2px;
	background-color: white;
	color: black;
	border-left: 1px solid #EAF2FE;
	border-right: 1px solid #EAF2FE;
	border-bottom: 1px solid #EAF2FE;
	font-weight: normal;
}

div.dropdown-heading {
	background-color: #D4E5FD;
	color: #006699;
	border-left: 1px solid #EAF2FE;
	border-right: 1px solid #EAF2FE;
	border-top: 1px solid #EAF2FE;
	font-weight: bold;
	padding: 2px;}

</style>

<?php
$side_menu = array();

$sql = "SELECT side_menu FROM ".TABLE_PREFIX."courses WHERE course_id=".$_SESSION['course_id'];
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$side_menu = explode("|", $row['side_menu']);
}

if (isset($side_menu)) {
	foreach ($side_menu as $stack_id) {
		if($stack_id != -1) {
			$dropdown_file = $_stacks[$stack_id];
			require(AT_INCLUDE_PATH . 'html/dropdowns/' . $dropdown_file . '.inc.php');
		}
	}
} else {
	//defaults weren't set and should've been
}
?>