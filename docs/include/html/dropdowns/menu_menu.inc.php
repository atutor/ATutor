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

	if ($_SESSION['prefs'][PREF_MENU] == 1){
?><table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">
<tr>
	<td class="cata" valign="top"><?php

		print_popup_help(AT_HELP_GLOBAL_MENU);
		if($_GET['menu_jump']){
			echo '<a name="menu_jump2"></a>';
		}
		echo '<a class="white" href="'.$_my_uri.'disable='.PREF_MENU.SEP.'menu_jump=2" accesskey="7" title="'._AT('close_global_menu').': Alt-7">'._AT('close_global_menu').'</a>';

	?></td>
</tr>
<?php  
		if ($_SESSION['is_admin'] && $_SESSION['prefs'][PREF_EDIT]) {
			echo '<tr>';
			echo '<td class="row1" align="center"><strong>';
			
			unset($editors);
			$editor[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('add_top_page'), 'url' => $_base_path.'editor/edit_content.php');
			print_editor($editor, $large = false);

			echo '</strong></td></tr>';
			echo '<tr><td class="row2" height="1"><img src="'.$_base_path.'images/clr.gif" height="1" width="1" alt="" /></td></tr>';
		}

		echo '<tr>';
		echo '<td valign="top" class="row1" nowrap="nowrap" align="left">';

		echo '<a href="'.$_base_path.'?g=9">'._AT('home').'</a><br />';

		/* @See classes/ContentManager.class.php	*/
		$contentManager->printMainMenu();

		echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_split.gif" alt="" width="16" height="16" class="menuimage8" /> ';
		echo '<img src="'.$_base_path.'images/glossary.gif" alt="" class="menuimage8" /> <a href="'.$_base_path.'glossary/">'._AT('glossary').'</a>';

		echo '<br />';

		echo '<img src="'.$_base_path.'images/'.$rtl.'tree/tree_end.gif" alt="" width="16" height="16" class="menuimage8" /> ';
		echo '<img src="'.$_base_path.'images/toc.gif" alt="" class="menuimage8" /> <a href="'.$_base_path.'tools/sitemap/">'._AT('sitemap').'</a>';

		echo '</td></tr>';
		echo '</table>';

} else {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2">';
	echo '<tr><td class="cata" valign="top">';
	print_popup_help(AT_HELP_GLOBAL_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump2"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'enable='.PREF_MENU.SEP.'menu_jump=2" accesskey="7" title="'._AT('open_global_menu').': Alt-7">';
	echo _AT('open_global_menu').'';
	echo '</a>';

	echo '</td></tr></table>';
} 

?>