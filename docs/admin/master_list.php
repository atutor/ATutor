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
	// 1. make sure uploaded file was actually uploaded (not empty)
	// 2. try to read the uploaded file
	// 3. fgetcsv to read the file
	// 4. create a temporary table like the master_list
	// 5. insert into the temp master_list
	// 6. update the temp master_list with the member IDs from the real master_list
	// 7. dump the master_list using TRUNCATE, otherwise DELETE
	// 8. insert the temp master_list back into master_list

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
	
	debug($_POST);
	debug($_FILES);

	$fp = fopen($_FILES['file']['tmp_name'], 'r');
	if ($fp) {
		$sql = '';
		while (($row = fgetcsv($fp, 1000, ',')) !== FALSE) {
			if (count($row) == 2) {
				debug($row);
			}
			$row[0] = addslashes($row[0]);
			$row[1] = addslashes($row[1]); // this may be hashed

			$sql .= "('$row[0]', '$row[1]', 0),";
		}
		if ($sql) {
			$sql = "INSERT INTO ".TABLE_PREFIX."master_list VALUES ".substr($sql, 0, -1);
		}
		debug($sql);
		fclose($fp);
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
		<?php echo _AT('replace current list'); ?><br />
		<input type="radio" name="override" id="oy" value="1" /><label for="oy"><?php echo _AT('yes'); ?></label>
		<input type="radio" name="override" id="on" value="0" /><label for="on"><?php echo _AT('no'); ?></label>
	</div>

	<div class="row buttons">
		<input type= "submit" name="submit" value="<?php echo _AT('upload'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>