<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002 by Greg Gay & Joel Kronenberg             */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

if ($_SESSION['prefs'][PREF_HISTORY] == 1){
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="cate" valign="top">';
	
	echo '<a href="'.$_my_uri.'disable='.PREF_HISTORY.'">';
	echo '<img src="'.$_base_path.'images/arr_down_s.gif" border="0" width="11" height="9" alt="close">';
	echo _AT('history');
	echo '</a>';
	echo '</td></tr>';
	echo '<tr>';
	echo '<td class="row1" align="center">';

	echo '<br />working on it';

	echo '</td></tr></table>';

} else {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="cate" valign="top">';

	echo '<a href="'.$_my_uri.'enable='.PREF_HISTORY.'">';
	echo '<img src="'.$_base_path.'images/arr_up_s.gif" border="0" width="9" height="11" alt="open">';
	echo _AT('history');
	echo '</a>';
	echo '</td></tr></table>';
} 
?>