<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
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
if ($_SESSION['course_id'] > -1) { exit; }

$page = 'backups';
$_user_location = 'admin';

$course = $_POST['course'];
require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$Backup =& new Backup($db);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {

	$Backup->setCourseID($_POST['course']);
	$error = $Backup->create($_POST['description']);
	if ($error !== FALSE) {
		$msg->addFeedback('BACKUP_CREATED');
		header('Location: index.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h3>'._AT('backups').'</h3><br />';

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table cellspacing="1" cellpadding="0" border="0" width="95%" summary="" align="center" class="bodyline">
	<tr>
		<th class="cyan" colspan="2"><?php echo _AT('create_backup'); ?></th>
	</tr>
	<tr>
		<td class="row1" colspan="2"><?php echo _AT('create_backup_about', AT_COURSE_BACKUPS); ?></td>
	</tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<?php if (isset($_POST['submit']) && ($Backup->getNumAvailable() >= AT_COURSE_BACKUPS)): ?>
		<tr>
			<td class="row1" colspan="2"><p><strong><?php echo _AT('max_backups_reached'); ?></strong></p></td>
		</tr>
	<?php else: ?>
	<tr>
		<td class="row1" align="right"><label for="desc"><strong><?php echo _AT('course'); ?>:</strong></label></td>
		<td class="row1"><select name="course"><?php
			foreach ($system_courses as $id => $course) {
				echo '<option value="'.$id.'">'.$course['title'].'</option>';
			}
			?>
		</select></td>
	</tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" align="right"><label for="desc"><strong><?php echo _AT('optional_description'); ?>:</strong></label></td>
		<td class="row1"><textarea cols="35" rows="2" class="formfield" id="desc" name="description" scroll="no"></textarea><br /><br /></td>
	</tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><input type="submit" name="submit" value="<?php echo _AT('create'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
	<?php endif; ?>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>