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

// this file is obsolete
if ($_SESSION['prefs'][PREF_SEQUENCE] == 1){
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="cat" valign="top">';
	
	echo '<a class="white" href="'.$_my_uri.'disable='.PREF_SEQUENCE.'">';
	echo '<img src="images/arr_down_s.gif" border="0" width="11" height="9" alt="close">';
	echo _AT('sequence');
	echo '</a>';
	echo '</td></tr>';
	echo '<tr>';
	echo '<td class="row1" align="center">';

	echo '<br />working on it';

	echo '</td></tr></table>';

} else {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="cat" valign="top">';

	echo '<a class="white" href="'.$_my_uri.'enable='.PREF_SEQUENCE.'">';
	echo _AT('sequence');
	echo '</a>';
	echo '</td></tr></table>';
} 
?>