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
global $page;
global $savant;
global $onload;
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
if (is_array($_SESSION['prefs'][PREF_STACK])) { 	
	foreach ($_SESSION['prefs'][PREF_STACK] as $stack_id) {
		$dropdown_file = $_stacks[$stack_id];
		require(AT_INCLUDE_PATH . 'html/dropdowns/' . $dropdown_file . '.inc.php');
	}
}
?>