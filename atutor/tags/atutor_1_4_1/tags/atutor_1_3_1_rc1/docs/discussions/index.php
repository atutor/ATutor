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



define('AT_INCLUDE_PATH', '../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');
$section = 'discussions';
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

	if ($_SESSION['is_admin'] && $_SESSION['prefs'][PREF_EDIT]) {
		$help[] = AT_HELP_CREATE_FORUMS;
		print_help($help);

	}
	
	if ($_SESSION['is_admin'] && !$_SESSION['prefs'][PREF_EDIT]) {
		$help[] = array(AT_HELP_ENABLE_EDITOR, $_my_uri);
		print_help($help);

	}
?>

<table border="0" cellspacing="0" cellpadding="3" summary="">
<tr>
	<?php
		if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
			echo '<td rowspan="2" valign="top"><img src="images/icons/default/forum-small.gif" width="28" height="25" class="menuimage" border="0" alt="" /></td>';
		}
		echo '<td>';
		echo '<b>'._AT('forums').'</b>';

		print_editor( _AT('new_forum'), 'editor/add_forum.php');

		/*if ($_SESSION['is_admin'] && $_SESSION['prefs'][PREF_EDIT] && false) {
			echo '<span class="bigspacer">';
			echo '( <img src="images/pen2.gif" border="0" class="menuimage12" alt="'._AT('editor_on').'" title="'._AT('editor_on').'" height="14" width="16" valign="middle" /> ';
			echo '<strong><em><a href="editor/add_forum.php">'._AT('new_forum').'</a></em></strong>';
			echo ' )</span>';
		}*/

		echo '</td></tr><tr><td>';

	?>
		<ul>
		<?php
			$sql	= "SELECT * FROM ".TABLE_PREFIX."forums WHERE course_id=$_SESSION[course_id] ORDER BY title";
			$result = mysql_query($sql, $db);

			if ($row = mysql_fetch_array($result)) {
				do {
					echo '<li><a href="forum/?fid='.$row['forum_id'].'">'.$row['title'].'</a>';
					
					print_editor( _AT('edit'), 'editor/edit_forum.php?fid='.$row['forum_id'], _AT('delete'), 'editor/delete_forum.php?fid='.$row['forum_id']);

					/*if ($_SESSION['is_admin'] && $_SESSION['prefs'][PREF_EDIT]) {
						echo ' <span class="bigspacer">( <img src="images/pen2.gif" border="0" class="menuimage12" alt="'._AT('editor_on').'" title="'._AT('editor_on').'" height="14" width="16" valign="middle" />';
						echo '<a href="editor/edit_forum.php?fid='.$row['forum_id'].'">'._AT('edit').'</a>';
						echo ' | ';
						echo '<a href="editor/delete_forum.php?fid='.$row['forum_id'].'">'._AT('delete').'</a>';
						echo ' )</span>';
					}*/
					echo '<p>'.$row['description'].'</p>';
					echo '</li>';
				} while ($row = mysql_fetch_array($result));
			} else {
				echo '<li><i>'._AT('no_forums').'</i></li>';
			}
		?>
		</ul>
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
		//echo '<small class="spacer">'._AT('chat_window').'</small>';
		
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
				echo ' <a href="inbox.php?g=21"><b>'._AT('inbox').'</b></a>';
			}
			echo '</td></tr><tr><td>';
			echo _AT('inbox_text');
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