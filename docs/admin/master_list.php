<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (!defined('AT_MASTER_LIST') || !AT_MASTER_LIST) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	echo '... master list is disabled. enable it using the <a href="">config editor thing..</a>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


if (isset($_POST['submit'])) {
	if ($_FILES['file']['error'] == 1) { 
		$errors = array('FILE_MAX_SIZE', ini_get('upload_max_filesize'));
		$msg->addError($errors);
		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}

	if (!$_FILES['file']['name'] || (!is_uploaded_file($_FILES['file']['tmp_name']))) {
		$msg->addError('FILE_NOT_SELECTED');
		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}

	$fp = fopen($_FILES['file']['tmp_name'], 'r');
	if ($fp) {
		$existing_accounts = array();

		if ($_POST['override'] > 0) {
			/* Delete all the un-created accounts. (There is no member to delete or disable). */
			$sql = "DELETE FROM ".TABLE_PREFIX."master_list WHERE member_id=0";
			$result = mysql_query($sql, $db);

			/* Get all the created accounts. (They will be disabled or deleted if not in the new list. */
			$sql = "SELECT public_field, member_id FROM ".TABLE_PREFIX."master_list";
			$result = mysql_query($sql, $db);
			while ($row = mysql_fetch_assoc($result)) {
				$existing_accounts[$row['public_field']] = $row['member_id'];
			}
		}
		$sql = '';
		while (($row = fgetcsv($fp, 1000, ',')) !== FALSE) {
			if (count($row) != 2) {
				continue;
			}
			$row[0] = addslashes($row[0]);
			$row[1] = addslashes($row[1]); // this may be hashed

			$sql = "INSERT INTO ".TABLE_PREFIX."master_list VALUES ('$row[0]', '$row[1]', 0)";
			mysql_query($sql, $db);
			unset($existing_accounts[$row[0]]);
		}
		fclose($fp);

		if (($_POST['override'] == 1) && $existing_accounts) {
			// disable missing accounts
			$existing_accounts = implode(',', $existing_accounts);

			$sql    = "UPDATE ".TABLE_PREFIX."members SET status=".AT_STATUS_DISABLED." WHERE member_id IN ($existing_accounts)";
			$result = mysql_query($sql, $db);
			
			// un-enrol disabled accounts
			$sql    = "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE member_id IN ($existing_accounts)";
			$result = mysql_query($sql, $db);
			
		} else if ($_POST['override'] == 2) {
			// delete missing accounts
		}
	}

	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
what kind of options do we want?
view list? filter by created accounts, delete list, override list upon upload..

<form name="importForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<div class="input-form">
	<div class="row">
		format.. encoding options
	</div>

	<div class="row">
		<label for="file"><?php echo _AT('file'); ?></label><br />
		<input type="file" name="file" size="40" id="file" />
	</div>
	
	<div class="row">
		<?php echo _AT('what to do with those not in the new list'); ?><br />
		<input type="radio" name="override" id="o0" value="0" /><label for="o0"><?php echo _AT('leave_as_is'); ?></label>
		<input type="radio" name="override" id="o1" value="1" /><label for="o1"><?php echo _AT('disable');     ?></label>
		<input type="radio" name="override" id="o2" value="2" /><label for="o2" style="text-decoration: line-through;"><?php echo _AT('delete');      ?></label>
	</div>

	<div class="row buttons">
		<input type= "submit" name="submit" value="<?php echo _AT('upload'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>