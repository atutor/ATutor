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
global $contentManager; 

if (is_array($_SESSION['prefs'][PREF_STACK])) {
	foreach ($_SESSION['prefs'][PREF_STACK] as $stack_id) {
		echo '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" alt="" />';
		
		/*if ($_SESSION['prefs'][PREF_LOCAL] == 1){

		} else {
		}*/
//debug("YO".$stack_id);
		require(AT_INCLUDE_PATH.'html/dropdowns/'.$_stacks[$stack_id].'.inc.php');

	}
}


//$savant->assign('tmpl_lang', $available_languages[$_SESSION['lang']][2]);

//$savant->display('include/html/dropdowns/local_menu.tmpl.php');


?>