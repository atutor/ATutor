<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }
global $available_languages;
global $_rtl_languages;
global $page;
global $savant;
global $onload;
global $_stacks;
 
if (is_array($_SESSION['prefs'][PREF_STACK])) {
	foreach ($_SESSION['prefs'][PREF_STACK] as $stack_id) {
		//echo '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" alt="" />';
		
		$dropdown_name = $_stacks[$stack_id]['name'];
		$dropdown_file = $_stacks[$stack_id]['file'];

		/*if ($_SESSION['prefs'][$dropdown_name] == 1){
		} else {
		} */
		require(AT_INCLUDE_PATH.'html/dropdowns/'.$dropdown_file.'.inc.php');
	}
}
?>