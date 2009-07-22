<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['back'])) {
	header('Location: error_logging.php');
	exit;
}

$files = array();
if (isset($_POST['view'])) { // check if a bug was selected
	foreach($_POST as $elem => $val) {
		$str_ = substr($elem, 0, 4);
		if ($str_  == 'file') {
			$files[] = $elem;
		}
	}
	if (empty($files)) {
		$msg->addError('NO_LOG_SELECTED');
		header('Location: error_logging.php');
		exit;
	}
}

$back_ref = $_POST['profile_id'] . ':' . $_POST['profile_date'];

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<form name="form" method="post" action="<?php echo 'admin/error_logging_details.php'; ?>">
<input type="hidden" name="data" value="<?php echo $back_ref; ?>" />
<input type="hidden" name="view" value="<?php echo ''; ?>" />

<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('viewing_errors'); ?></h3>
	</div><?php

	foreach ($files as $file) {
		if (isset($_POST[$file])) {
			$dump = @file_get_contents(AT_CONTENT_DIR . 'logs/' . $_POST[$file]);	
			if ($dump !== false) { ?>

				<div class="row">
					<?php echo $dump; ?>
				</div><?php

			} else {
				$msg->printErrors(array('CANNOT_READ_FILE', AT_CONTENT_DIR . 'logs/' . $_POST[$file]));
			}
		}	
	} ?>

	<div class="row buttons">
		<input type="submit" name="back" value="<?php echo _AT('back_to_profile'); ?>" />  
	</div>
</div>
</form>

<?php	require(AT_INCLUDE_PATH.'footer.inc.php'); ?>