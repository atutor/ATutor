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
global $db;
global $_my_uri;
global $_base_path;
global $savant;
global $contentManager;

$savant->assign('tmpl_popup_help', AT_HELP_GLOBAL_MENU);
$savant->assign('tmpl_access_key', '7');

if ($_GET['menu_jump']) {
	$savant->assign('tmpl_menu_url', '<a name="menu_jump2"></a>');	
} else {
	$savant->assign('tmpl_menu_url', '');	
}

if ($_SESSION['prefs'][PREF_MENU] == 1){
	ob_start();

?>
<?php
	if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
		echo '<tr>';
		echo '<td class="dropdown cell" align="center"><strong>';
		
		unset($editors);
		$editor[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('add_top_page'), 'url' => $_base_path.'editor/edit_content.php');
		print_editor($editor, $large = false);

		echo '</strong></td></tr>';
	}

	echo '<tr>';
	echo '<td valign="top" class="dropdown" nowrap="nowrap">';

	echo '<a href="'.$_base_path.'?g=9">'._AT('home').'</a><br />';

	/* @See classes/ContentManager.class.php	*/
	$contentManager->printMainMenu();

	echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" width="16" height="16" class="menuimage8" /> ';
	echo '<img src="'.$_base_path.'images/glossary.gif" alt="" class="menuimage8" /> <a href="'.$_base_path.'glossary/">'._AT('glossary').'</a>';

	echo '<br />';

	echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" width="16" height="16" class="menuimage8" /> ';
	echo '<img src="'.$_base_path.'images/toc.gif" alt="" class="menuimage8" /> <a href="'.$_base_path.'tools/sitemap/">'._AT('sitemap').'</a>';

	echo '</td></tr>';
	$savant->assign('tmpl_dropdown_contents', ob_get_contents());
	ob_end_clean();
	$savant->assign('tmpl_close_url', $_my_uri.'disable='.PREF_MENU.SEP.'menu_jump=2');
	$savant->assign('tmpl_dropdown_close', _AT('close_global_menu'));
	$savant->display('dropdown_open.tmpl.php');

} else {		
	$savant->assign('tmpl_open_url', $_my_uri.'enable='.PREF_MENU.SEP.'menu_jump=2');
	$savant->assign('tmpl_dropdown_open', _AT('open_global_menu'));
	$savant->display('dropdown_closed.tmpl.php');
}

?>