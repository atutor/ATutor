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

$page = 'discussions';
define('AT_INCLUDE_PATH', '../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('discussions');
require (AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimageh2" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('discussions');
	}
	echo '</h2><br />';
?>

<table border="0" cellspacing="0" cellpadding="3" summary="">
<tr>
	<?php
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<td rowspan="2" valign="top"><img src="images/icons/default/forum-small.gif" width="28" height="25" class="menuimage" border="0" alt="" /></td>';
		}
		echo '<td>';
		echo '<a href="forum/list.php"><b>'._AT('forums').'</b></a>';
		echo '</td></tr><tr><td>';
		echo _AT('forums_text');
		?>
	</td>
</tr>
<tr>
	<?php
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<td rowspan="2" valign="top"><img src="images/icons/default/chat-small.gif"  class="menuimage" width="28" height="25" border="0" alt="*" /></td>';
		}
		echo '<td>';
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
			echo ' <a href="discussions/achat/"><b>'._AT('chat').'</b></a>';
		}

		echo '</td></tr><tr><td>';
		echo _AT('chat_text');

		?>
	</td>
</tr>
<tr>
	<?php
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
				echo '<td rowspan="2" valign="top"><img src="images/icons/default/inbox-small.gif"  class="menuimage" width="28" height="25" border="0" alt="*" /></td>';
			}
			echo '<td>';
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
				echo ' <a href="users/inbox.php?g=21"><b>'._AT('inbox').'</b></a>';
			}
			echo '</td></tr><tr><td>';
			echo _AT('inbox_text');
		?>
	</td>
</tr>
<tr>
	<?php 
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
					echo '<td rowspan="2" valign="top"><img src="images/icons/default/polls-small.gif" border="0" class="menuimage" width="28" height="25" alt="*" /></td>';
				}
				echo '<td>';
				if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
					echo ' <a href="discussions/polls.php"><b>'._AT('polls').'</b></a>';
				}
				echo '</td></tr><tr><td>';
				echo _AT('polls_text');
			?>
	</td>
</tr>
<tr>
	<?php
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
				echo '<td rowspan="2" valign="top"><img src="images/icons/default/users-online-small.gif" class="menuimage" width="28" height="25" border="0" alt="*" /></td>';
			}
			echo '<td>';
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {				
				echo '<b>'._AT('users_online').'</b>';
			}
			echo '</td></tr><tr><td>';
			echo _AT('users_online_text');
		?>
	</td>
</tr>
</table>
<?php
	echo '<img src="images/clr.gif" alt="" width="34" height="1" align="left" /><table border="0" style="width:16em;" width="40%" summary=""><tr><td>';
	require(AT_INCLUDE_PATH.'html/dropdowns/users_online.inc.php');
	echo '</td></tr></table>';

	require (AT_INCLUDE_PATH.'footer.inc.php');
?>