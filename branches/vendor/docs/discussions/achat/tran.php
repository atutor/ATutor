<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$_include_path = '../../include/';
require($_include_path.'vitals.inc.php');

$_section[0][0] = 'Home';
$_section[0][1] = 'home.php';
$_section[1][0] = 'Chat';
$_section[1][1] = 'chat/';
$_section[2][0] = 'Transcript';
$_section[2][1] = 'chat/tran.php';

require($_include_path.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
}

echo '<a href="discussions/?g=11">'._AT('discussions').'</a>';
echo '</h2>';
echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/chat-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo '<a href="discussions/achat/?g=11">'._AT('chat').'</a>';
echo '</h3>';

@readfile('../../content/chat/'.$_SESSION['course_id'].'/tran/'.$_GET['t'].'.html');
echo '</table>';

require($_include_path.'footer.inc.php');
?>
