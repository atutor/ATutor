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
<form name="importForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<div class="input-form">
	<div class="row">
		<label for="file"><?php echo _AT('file'); ?></label><br />
		<input type="file" name="file" size="40" id="file" />
	</div>
	
	<div class="row">
		<?php echo _AT('what to do with those not in the new list'); ?><br />
		<input type="radio" name="override" id="o0" value="0" /><label for="o0"><?php echo _AT('leave_as_is'); ?></label>
		<input type="radio" name="override" id="o1" value="1" /><label for="o1"><?php echo _AT('disable');     ?></label>
	</div>

	<div class="row buttons">
		<input type= "submit" name="submit" value="<?php echo _AT('upload'); ?>" />
	</div>
</div>
</form>


<?php
$sql	= "SELECT * FROM ".TABLE_PREFIX."master_list ORDER BY public_field";
$result = mysql_query($sql, $db);
$num_results = mysql_num_rows($result);

$results_per_page = 100;
$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}
?>

<div class="paging">
	<ul>
	<?php for ($i=1; $i<=$num_pages; $i++): ?>
		<li>
			<?php if ($i == $page) : ?>
				<a class="current" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string; ?>"><em><?php echo $i; ?></em></a>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
		</li>
	<?php endfor; ?>
	</ul>
</div>


<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="status" value="<?php echo $_GET['status']; ?>" />

<table summary="" class="data" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('student_id'); ?></th>
	<th scope="col"><?php echo _AT('username'); ?></th>
</tr>
</thead>
<?php if ($num_results > 0): ?>
<tfoot>
<tr>
	<td colspan="3"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
	<?php while($row = mysql_fetch_assoc($result)): ?>
		<tr onmousedown="document.form['m<?php echo $row['public_field']; ?>'].checked = true;">
			<td><input type="radio" name="id" value="<?php echo $row['public_field']; ?>" id="m<?php echo $row['public_field']; ?>" /></td>
			<td><?php echo $row['public_field']; ?></td>
			<td><?php 
				if ($row['member_id']) {
					echo get_login($row['member_id']);
				} else {
					echo _AT('na');
				}
				?></td>
		</tr>
	<?php endwhile; ?>
</tbody>
<?php else: ?>
	<tr>
		<td colspan="3"><?php echo _AT('no_users_found'); ?></td>
	</tr>
<?php endif; ?>
</table>
</form>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>