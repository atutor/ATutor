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

if ($_SESSION['prefs'][PREF_LOCAL] == 1){
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catb" valign="top">';
	print_popup_help(AT_HELP_LOCAL_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump1"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'disable='.PREF_LOCAL.SEP.'menu_jump=1">';
	echo _AT('close_local_menu');
	echo '</a>';

	echo '</td></tr>';

	if ($_SESSION['s_cid']){
		/* @see: ./include/html/breadcrumbs.inc.php (for $path) */
		if (($_GET['cid'] == '') || ($_GET['cid'] == 0) ) {
			$path = $contentManager->getContentPath($_SESSION['s_cid']);
		}
		$location =	$contentManager->getLocationPositions(0, $path[0]['content_id']);
		$temp_path = $path;
		$garbage = next($temp_path);
		$temp_menu = $contentManager->getContent();
		
		/* previous topic: */
		echo '<tr><td valign="top" class="row1" align="left">';
		if ($temp_menu[0][$location-1] != '') {
			echo '<a href="'.$_base_path.'index.php?cid='.$temp_menu[0][$location-1][content_id].SEP.'g=22"><small>'._AT('previous_topic').': ';
			
			if ($_SESSION['prefs'][PREF_NUMBERING]) {
				echo ($location);
				echo ' ';
			}

			echo $temp_menu[0][$location-1]['title'].'</small></a>';
		} else {
			echo '<small class="spacer">'._AT('previous_topic').' '._AT('none').'</small>';
		}
		echo '</td></tr>';
		echo '<tr><td class="row2" height="1"><img src="'.$_base_path.'images/clr.gif" height="1" width="1" alt="" /></td></tr>';
		
		echo '<tr>';
		echo '<td class="row1" align="left" nowrap="nowrap">';
		
		echo '<a href="'.$_base_path.'?g=26">'._AT('home').'</a><br />';
		echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="collapse" border="0" class="menuimage8" />';
		
		if($_GET['cid'] ==  $content['content_id']){
			echo '<a name="menu'.$_GET['cid'].'"></a>';
		}
		
		if ($_SESSION['prefs'][PREF_NUMBERING]) {
			echo ($location+1);
		}

		if (strlen($path[0]['title']) > 26 ) {
			$path[0]['title'] = substr($path[0]['title'], 0, 26-4).'...';
		}

		echo ' <a href="'.$_base_path.'?cid='.$path[0]['content_id'].SEP.'g=2"><b>'.$path[0]['title'].'</b></a>';
		echo '<br />';

		/* @see: ./include/lib/content_functions.inc.php */
		$contentManager->printSubMenu($path[0]['content_id'], ($location+1).'.');

		echo '</td></tr>';

		/* next topic: */
		echo '<tr><td class="row2" height="1"><img src="'.$_base_path.'images/clr.gif" alt="" height="1" width="1" /></td></tr>';
		echo '<tr><td valign="top" class="row1" align="left">';
		if ($temp_menu[0][$location+1] != '') {
			echo '<a href="'.$_base_path.'index.php?cid='.$temp_menu[0][$location+1]['content_id'].SEP.'g=22"><small>'._AT('next_topic').': ';
			
			if ($_SESSION['prefs'][PREF_NUMBERING]) {
				echo ($location+2);
				echo ' ';
			}
			echo $temp_menu[0][$location+1]['title'];
			echo '</small></a>';
		} else {
			echo '<small class="spacer">'._AT('next_topic').' '._AT('none').'</small>';
		}
		echo '</td></tr>';
	} else {
		echo '<tr>';
		echo '<td class="row1" align="left">';

		echo '<small><i>'._AT('select_topic_first').'</i></small>';
		echo '</td></tr>';
	}

	echo '</table>';
} else {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catb" valign="top">';
	print_popup_help(AT_HELP_LOCAL_MENU);
	if ($_GET['menu_jump']) {
		echo '<a name="menu_jump1"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'enable='.PREF_LOCAL.SEP.'menu_jump=1">';
	echo _AT('open_local_menu').'';
	echo '</a>';

	echo '</td></tr></table>';
}

?>