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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = 'Home';
$_section[0][1] = 'home.php';
$_section[1][0] = 'Chat';
$_section[1][1] = 'chat/';
$_section[2][0] = 'Transcript';
$_section[2][1] = 'chat/tran.php';

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
}

echo '<a href="discussions/index.php?g=11">'._AT('discussions').'</a>';
echo '</h2>';
echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/chat-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo '<a href="discussions/achat/index.php?g=11">'._AT('chat').'</a>';
echo '</h3>';

@readfile(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$_GET['t'].'.html');
echo '</table>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>