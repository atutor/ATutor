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
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN); 
require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('backup_manager');
$_section[1][1] = 'tools/backup/index.php';
$_section[2][0] = _AT('restore');

global $savant;
$msg =& new Message($savant);

$Backup =& new Backup($db, $_SESSION['course_id']);

if (!isset($_REQUEST['backup_id'])) {
	header('Location: index.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	if (!$_POST['material']) {
		$msg->addError('RESTORE_MATERIAL');
	} else {
		$Backup->restore($_POST['material'], $_POST['action'], $_POST['backup_id']);

		$msg->addFeedback('IMPORT_SUCCESS');
		header('Location: index.php');
		exit;
	}
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

$msg->printAll();

$row = $Backup->getRow($_REQUEST['backup_id']);

if (!isset($row['contents']['content'])) {
	$row['contents']['content'] = '?';
}
if (!isset($row['contents']['news'])) {
	$row['contents']['news'] = '?';
}
if (!isset($row['contents']['resource_categories'])) {
	$row['contents']['resource_categories'] = '?';
}
if (!isset($row['contents']['resource_links'])) {
	$row['contents']['resource_links'] = '?';
}
if (!isset($row['contents']['forums'])) {
	$row['contents']['forums'] = '?';
}
if (!isset($row['contents']['tests'])) {
	$row['contents']['tests'] = '?';
}
if (!isset($row['contents']['tests_questions'])) {
	$row['contents']['tests_questions'] = '?';
}
if (!isset($row['contents']['polls'])) {
	$row['contents']['polls'] = '?';
}
if (!isset($row['contents']['glossary'])) {
	$row['contents']['glossary'] = '?';
}
if (!isset($row['contents']['file_manager'])) {
	$row['contents']['file_manager'] = '?';
}
if (!isset($row['contents']['course_stats'])) {
	$row['contents']['course_stats'] = '?';
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="backup_id" value="<?php echo $_REQUEST['backup_id']; ?>" />
<table cellspacing="1" cellpadding="0" border="0" width="95%" summary="" align="center" class="bodyline">
	<tr>
		<th class="cyan" colspan="2"><?php echo _AT('restore_backup', $row['file_name']); ?></th>
	</tr>
	<tr>
		<td class="row1" colspan="2"><?php echo _AT('restore_backup_about'); ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1"><strong><?php echo _AT('material'); ?>:</strong></td>
		<td class="row1">
				<input type="checkbox" value="1" name="all"  id="all" onclick="javascript:selectAll();" /><label for="all"><?php echo _AT('material_select_all'); ?></label><br /><br />

				<label><input type="checkbox" value="1" name="material[content]" id="content_pages" /><?php echo _AT('material_content_pages', $row['contents']['content']); ?></label><br />
				
				<label><input type="checkbox" value="1" name="material[news]" id="news" /><?php echo _AT('material_announcements', $row['contents']['news']); ?></label><br />

				<label><input type="checkbox" value="1" name="material[links]" id="links" /><?php echo _AT('material_links', $row['contents']['resource_categories'], $row['contents']['resource_links']); ?></label><br />

				<label><input type="checkbox" value="1" name="material[forums]" id="forums" /><?php echo _AT('material_forums', $row['contents']['forums']); ?></label><br />
				
				<label><input type="checkbox" value="1" name="material[tests]" id="tests" /><?php echo _AT('material_tests', $row['contents']['tests'], $row['contents']['tests_questions']); ?></label><br />
				
				<label><input type="checkbox" value="1" name="material[polls]" id="polls" /><?php echo _AT('material_polls', $row['contents']['polls']); ?></label><br />
				
				<label><input type="checkbox" value="1" name="material[glossary]" id="glossary" /><?php echo _AT('material_glossary', $row['contents']['glossary']); ?></label><br />
				
				<label><input type="checkbox" value="1" name="material[files]" id="files" /><?php echo _AT('material_files', get_human_size($row['contents']['file_manager'])); ?></label><br />

				<label><input type="checkbox" value="1" name="material[stats]" id="stats" /><?php echo _AT('material_stats', $row['contents']['course_stats']); ?></label><br />
			</td>
	</tr>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" width="20%"><strong><?php echo _AT('action'); ?>:</strong></td>
		<td class="row1"><input type="radio" checked="checked" name="action" value="append" id="append" /><label for="append"><?php echo _AT('append_content'); ?></label><br />
		
		<input type="radio" name="action" value="overwrite" id="overwrite" /><label for="overwrite"><?php echo _AT('overwite_content'); ?></label><br />
		<br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="2"><input type="submit" name="submit" value="<?php echo _AT('restore'); ?>" class="button" /> - 
													<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
</table>
</form>

<script language="javascript">
	
	function selectAll() {
		if (document.form.all.checked == true) {
			document.form.content_pages.checked = true;
			document.form.news.checked = true;
			document.form.links.checked = true;
			document.form.forums.checked = true;
			document.form.tests.checked = true;
			document.form.polls.checked = true;
			document.form.glossary.checked = true;
			document.form.files.checked = true;
			document.form.stats.checked = true;
		} else {
			document.form.content_pages.checked = false;
			document.form.news.checked = false;
			document.form.links.checked = false;
			document.form.forums.checked = false;
			document.form.tests.checked = false;
			document.form.polls.checked = false;
			document.form.glossary.checked = false;
			document.form.files.checked = false;
			document.form.stats.checked = false;
		}
	}
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>