<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

if ($_SESSION['prefs'][PREF_RELATED] == 1){
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catc" valign="top">';
	print_popup_help(AT_HELP_RELATED_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump3"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'disable='.PREF_RELATED.SEP.'menu_jump=3">';
	echo _AT('close_related_topics');
	echo '</a>';
	echo '</td></tr>';
	echo '<tr>';
	echo '<td class="row1" align="left">';

	$related = $contentManager->getRelatedContent($_SESSION['s_cid']);

	if (count($related) == 0) {
		echo '<small><i>'._AT('none_found').'</i></small>';
	} else {
		for ($i=0; $i < count($related); $i++) {
			echo '&#176; <a href="'.$_base_path.'?cid='.$related[$i].SEP.'g=4">';
			echo $contentManager->_menu_info[$related[$i]]['title'];
			echo '</a>';
			echo '<br />';
		}
	}
	
	echo '</td></tr></table>';

} else {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catc" valign="top">';
	print_popup_help(AT_HELP_RELATED_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump3"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'enable='.PREF_RELATED.SEP.'menu_jump3">';
	echo _AT('open_related_topics');
	echo '</a>';
	echo '</td></tr></table>';
} 

?>