<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: index.php 1715 2004-09-30 14:18:46Z heidi $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN); 
require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('backup_manager');
$_section[1][1] = 'tools/backup/index.php';
$_section[2][0] = _AT('restore');



if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;
} else if (isset($_POST['restore'])) {
	
} 

require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2" class="menuimageh2" width="42" height="40" alt="" />';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
	}
	echo '</h2>';


	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/backups-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/backup/index.php" class="hide">'._AT('backup_manager').'</a>';
	}
	echo '</h3>';

require(AT_INCLUDE_PATH.'html/feedback.inc.php');
?>

<h4>Restore - NAME OF BACKUP</h4>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table cellspacing="1" cellpadding="0" border="0" width="95%" summary="" align="center" class="bodyline">
	<tr>
		<td class="row1" colspan="2">
		bla bla.
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1"><strong>Select Content:</strong></td>
		<td class="row1">
		<input type="checkbox" value="" name="content" id="content" /><label for="content">Content Pages (12)</label><br />
		<input type="checkbox" value="" name="links" id="links" /><label for="links">Links (5)</label><br />
		<input type="checkbox" value="" name="forums" id="forums" /><label for="forums">Forums (5)</label><br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" value="" name="threads" id="threads" /><label for="threads">Threads (54)</label><br />
		<input type="checkbox" value="" name="tests" id="tests" /><label for="tests">Tests (3)</label><br />
		<input type="checkbox" value="" name="glossary" id="glossary" /><label for="glossary">Glossary (25 terms)</label><br />
		<input type="checkbox" value="" name="enroll" id="enroll" /><label for="enroll">Enrollment (43 students)</label><br />
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" width="20%"><strong><?php echo _AT('select_action'); ?>:</strong></td>
		<td class="row1"><input type="radio" checked="checked" name="restore_action" value="append" id="append" /><label for="append"><?php echo _AT('append_content'); ?></label><br />
		
		<input type="radio" name="restore_action" value="overwrite" id="overwrite" /><label for="overwrite"><?php echo _AT('overwite_content'); ?></label><br />

		<input type="radio" name="restore_action" value="new" id="new" /><label for="new">Make into a new course</label><br />
		<br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="2"><input type="submit" name="submit" value="<?php echo _AT('restore_backup'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
</table>
</form>
<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>