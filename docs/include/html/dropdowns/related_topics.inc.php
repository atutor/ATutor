<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $contentManager;
global $_my_uri;
global $_base_path, $path;
global $savant;

$savant->assign('tmpl_popup_help', 'RELATED_MENU');
$savant->assign('tmpl_access_key', '');

if ($_GET['menu_jump']) {
	$savant->assign('tmpl_menu_url', '<a name="menu_jump3"></a>');	
} else {
	$savant->assign('tmpl_menu_url', '');	
}
	 
if ($_SESSION['prefs'][PREF_RELATED] == 1){
	ob_start();

	$related = $contentManager->getRelatedContent($_SESSION['s_cid']);

	if (count($related) == 0) {
		echo '<small><i>'._AT('none_found').'</i></small>';
	} else {
		for ($i=0; $i < count($related); $i++) {
			echo '&#176; <a href="'.$_base_path.'content.php?cid='.$related[$i].SEP.'g=4">';
			echo $contentManager->_menu_info[$related[$i]]['title'];
			echo '</a>';
			echo '<br />';
		}
	}
	

	$savant->assign('tmpl_dropdown_contents', ob_get_contents());
	ob_end_clean();
	$savant->assign('tmpl_close_url', $_my_uri.'disable='.PREF_RELATED.SEP.'menu_jump=3');
	$savant->assign('tmpl_dropdown_close', _AT('close_related_topics'));
	$savant->display('dropdown_open.tmpl.php');

} else {		
	$savant->assign('tmpl_open_url', $_my_uri.'enable='.PREF_RELATED.SEP.'menu_jump=3');
	$savant->assign('tmpl_dropdown_open', _AT('open_related_topics'));
	$savant->display('dropdown_closed.tmpl.php');
}
?>