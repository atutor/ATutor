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
global $_my_uri;
global $_base_path, $include_all, $include_one;
global $savant;

$savant->assign('tmpl_popup_help', 'SEARCH_MENU');
$savant->assign('tmpl_access_key', '');

if ($_GET['menu_jump']) {
	$savant->assign('tmpl_menu_url', '<a name="menu_jump7"></a>');	
} else {
	$savant->assign('tmpl_menu_url', '');	
}

if ($_SESSION['prefs'][PREF_SEARCH] == 1){
	ob_start(); 
	echo '<tr>';
	echo '<td class="dropdown" align="left">';

	if (!isset($include_all, $include_one)) {
		$include_one = ' checked="checked"';
	}

	echo '<form action="'.$_base_path.'users/search.php#search_results" method="get" name="searchform">';
	echo '<input type="hidden" name="search" value="1" />';
	echo '<input type="hidden" name="find_in" value="this" />';
	echo '<input type="hidden" name="display_as" value="pages" />';

	echo '<input type="text" name="words" class="formfield" size="20" id="words" value="'.stripslashes(htmlspecialchars($_GET['words'])).'" /><br />';
	echo '<small>'._AT('search_match').': <input type="radio" name="include" value="all" id="all2"'.$include_all.' /><label for="all2">'._AT('search_all_words').'</label>, <input type="radio" name="include" value="one" id="one2"'.$include_one.' /><label for="one2">'._AT('search_any_word').'</label><br /></small>';

	echo '<input type="submit" name="submit" value="  '._AT('search').'  " class="button" />';
	echo '</form>';
	echo '</td></tr>';

	$savant->assign('tmpl_dropdown_contents', ob_get_contents());
	ob_end_clean();
	$savant->assign('tmpl_close_url', $_my_uri.'disable='.PREF_SEARCH.SEP.'menu_jump=7');
	$savant->assign('tmpl_dropdown_close', _AT('close_search'));
	$savant->display('dropdown_open.tmpl.php');

} else {		
	$savant->assign('tmpl_open_url', $_my_uri.'enable='.PREF_SEARCH.SEP.'menu_jump=7');
	$savant->assign('tmpl_dropdown_open', _AT('open_search'));
	$savant->display('dropdown_closed.tmpl.php');
}

?>