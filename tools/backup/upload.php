<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
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

$_SESSION['done'] = 0;

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
$Backup =& new Backup($db, $_SESSION['course_id']);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['upload']) && ($Backup->getNumAvailable() < AT_COURSE_BACKUPS)) {
	$Backup->upload($_FILES, $_POST['description']);

	$_SESSION['done'] = 1;

	if($msg->containsErrors()) {
		header('Location: upload.php');
		exit;
	} else {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" >
<div class="input-form">
	<div class="row">
		<p><?php echo _AT('restore_upload'); ?></p>
	</div>

	<?php if ($Backup->getNumAvailable() >= AT_COURSE_BACKUPS): ?>
		<div class="row">
			<p><strong><?php echo _AT('max_backups_reached'); ?></strong></p>
		</div>
	<?php else: ?>
		<div class="row">
			<label for="descrip"><?php echo _AT('optional_description'); ?></label><br />
			<textarea id="descrip" cols="30" rows="2" name="description"></textarea>
		</div>
		
		<div class="row">
			<label for="file"><div class="required" title="<?php echo _AT('required_field'); ?>">*</div><?php echo _AT('file'); ?></label><br />
			<input type="file" name="file" id="file" />
		</div>

		<div class="row buttons">
		<input type="submit" name="upload" value="<?php echo _AT('upload'); ?>" onclick="openWindow('<?php echo AT_BASE_HREF; ?>tools/prog.php');" /> 
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</div>
	<?php endif; ?>
</div>
</form>

<script language="javascript" type="text/javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>