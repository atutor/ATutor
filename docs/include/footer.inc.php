<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

	/* next and previous link:	*/
	if ($_SESSION['prefs'][PREF_SEQ] != TOP) {
		echo "\n".'<div align="right" id="seqbottom">';
		echo $next_prev_links;
		echo '</div>'."\n";
	}
?>
<div align="right" id="top"><small><br />
<?php
	if (is_array($help)) {
		echo '<a href="'.$_base_path.'help/about_help.php"><em>'._AT('help_available').'</em>.</a> ';
	}
	if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 2) {
		echo '<a href="'.$_my_uri.'g=6#content" title="'._AT('back_to_top').' ALT-c"><img src="'.$_base_path.'images/top.gif" alt="'._AT('back_to_top').'" border="0" class="menuimage4" height="25" width="28"  /></a><br />';
	}
	if ($_SESSION['prefs'][PREF_SEQ_ICONS] != 1) {
		echo '<a href="' . $_my_uri . 'g=6#content" title="' ._AT('back_to_top') . ' ALT-c">' . _AT('top') . '</a>';
	}
?>&nbsp;&nbsp;</small></div>
</td>
	<?php
	if (($_SESSION['prefs'][PREF_MAIN_MENU] == 1) && ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] != MENU_LEFT)) {
		/* the menu is open: */
		echo '<td width="25%" valign="top" rowspan="2" style="padding-top: 1px;" id="menuR">';

		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="" id="contentR">';
		echo '<tr><td class="cata" valign="top">';
		print_popup_help(AT_HELP_MAIN_MENU);
		echo '<a name="menu"></a><a href="'.$_my_uri.'disable='.PREF_MAIN_MENU.'" accesskey="6" class="white" title="'._AT('close_menus').' ALT-6">' . _AT('close_menus') . '</a>';
		echo '</td></tr></table>';

		if (isset($_SESSION['prefs'][PREF_STACK])) {
			foreach ($_SESSION['prefs'][PREF_STACK] as $stack_id) {
				echo '<img src="'.$_base_path.'images/clr.gif" height="1" width="1" alt="" />';
				require(AT_INCLUDE_PATH.'html/dropdowns/'.$_stacks[$stack_id].'.inc.php');
			}
		}
		echo '</td>';
	}
	?>
</tr>
</table>
<?php

$sql_foot="select footer from ".TABLE_PREFIX."courses where course_id='$_SESSION[course_id]'";
if($result = mysql_query($sql_foot, $db)) {
	while($row=mysql_fetch_row($result)) {
		if(strlen($row[0])>0) {
			$custom_foot= $row[0];
			if (BACKWARDS_COMPATIBILITY) {
				$custom_foot = str_replace('CONTENT_DIR', $_base_path . 'content/'.$_SESSION['course_id'], $custom_foot);
			} else {
				$custom_foot = str_replace('CONTENT_DIR/', '', $custom_foot);
			}
		}
	}
}

if(strlen($custom_foot) > 0){
	echo $custom_foot;
}

require(AT_INCLUDE_PATH.'html/languages.inc.php');

require(AT_INCLUDE_PATH.'html/copyright.inc.php');

$microtime = microtime();
$microsecs = substr($microtime, 2, 8);
$secs = substr($microtime, 11);
$endTime = "$secs.$microsecs";
$t .= 'Timer: This page was generated in ';
$t .= sprintf("%.4f",($endTime - $startTime));
$t .= ' seconds.';

debug($t);
debug($_SESSION);

?>
</body>
</html>
