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
// $Id: index.php,v 1.4 2004/02/19 17:35:21 joel Exp $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('backup_course');
$_section[1][1] = 'tools/';

$_SESSION['done'] = 0;
session_write_close();

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
		echo _AT('backup_course');
	}
	echo '</h3>';

	if (!$_SESSION['is_admin']) {
		require (AT_INCLUDE_PATH.'header.inc.php'); 
		$errors[] = AT_ERROR_NOT_OWNER;
		print_errors($errors);
		require (AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}

$help[] = AT_HELP_IMPORT_EXPORT;
$help[] = AT_HELP_IMPORT_EXPORT1;

?>
<?php print_help($help);  ?>

<h2><?php echo _AT('create_and_download_course_backup'); ?></h2>
<form method="post" action="tools/backup/backup_export.php">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
	<tr>
		<td class="row1"><p><?php echo _AT('export_backup_about'); ?></p></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1" align="center"><input type="submit" name="submit" value="<?php echo _AT('export_backup'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
	</table>
</form>

<br /><br />

<h2><?php echo _AT('upload_and_restore_course_backup'); ?></h2>

<form name="form1" method="post" action="tools/backup/backup_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo $_base_href; ?>tools/prog.php');">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
	<tr>
		<td class="row1" colspan="2"><?php echo _AT('restore_backup_about'); ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" colspan="2"><strong><?php echo _AT('restore_course'); ?>:</strong> <input type="file" name="file" class="formfield" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" width="20%"><strong><?php echo _AT('select_action'); ?>:</strong></td>
		<td class="row1"><input type="radio" checked="checked" name="overwrite" value="0" id="a" /><label for="a"><?php echo _AT('append_content'); ?></label><br /><input type="radio" name="overwrite" value="1" id="o" /><label for="o"><?php echo _AT('overwite_content'); ?></label><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="2"><input type="submit" name="submit" value="<?php echo _AT('restore_backup'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
	</table>
</form>

<script language="javascript" type="text/javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>

<?php
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>