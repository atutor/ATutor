<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_TEST_MARK);

if (isset($_GET['edit'])) {
	if ($_GET['id'] == '') {
		$msg->addError('NO_TEST_SELECTED');
	} else {
		header('Location: edit_test.php?tid='.$_GET['id']);
		exit;
	}
} else if (isset($_GET['preview'])) {
	if ($_GET['id'] == '') {
		$msg->addError('NO_TEST_SELECTED');
	} else {
		header('Location: preview.php?tid='.$_GET['id']);
		exit;
	}
} else if (isset($_GET['questions'])) {
	if ($_GET['id'] == '') {
		$msg->addError('NO_TEST_SELECTED');
	} else {
		header('Location: questions.php?tid='.$_GET['id']);
		exit;
	}
} else if (isset($_GET['submissions'])) {
	if ($_GET['id'] == '') {
		$msg->addError('NO_TEST_SELECTED');
	} else {
		header('Location: results.php?tid='.$_GET['id']);
		exit;
	}
} else if (isset($_GET['statistics'])) {
	if ($_GET['id'] == '') {
		$msg->addError('NO_TEST_SELECTED');
	} else {
		header('Location: results_all_quest.php?tid='.$_GET['id']);
		exit;
	}
} else if (isset($_GET['delete'])) {
	if ($_GET['id'] == '') {
		$msg->addError('NO_TEST_SELECTED');
	} else {
		header('Location: delete_test.php?tid='.$_GET['id']);
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');


/* get a list of all the tests we have, and links to create, edit, delete, preview */

$sql	= "SELECT *, UNIX_TIMESTAMP(start_date) AS us, UNIX_TIMESTAMP(end_date) AS ue FROM ".TABLE_PREFIX."tests WHERE course_id=$_SESSION[course_id] ORDER BY start_date DESC";
$result	= mysql_query($sql, $db);
$num_tests = mysql_num_rows($result);

if ($num_tests == 0) {
	echo '<p><em>'. _AT('no_tests') . '</em></p>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$cols=6;
/// if (authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_RETURN)):
?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="" style="width: 90%" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('status');         ?></th>
	<th scope="col"><?php echo _AT('title');          ?></th>
	<th scope="col"><?php echo _AT('availability');   ?></th>
	<th scope="col"><?php echo _AT('result_release'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />
		<input type="submit" name="preview" value="<?php echo _AT('preview'); ?>" />
		<input type="submit" name="questions" value="<?php echo _AT('questions'); ?>" />
	</td>
</tr>
<tr>
	<td colspan="6">
		<input type="submit" name="submissions" value="<?php echo _AT('submissions'); ?>" />
		<input type="submit" name="statistics" value="<?php echo _AT('statistics'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php while ($row = mysql_fetch_assoc($result)) : ?>
	<tr onmousedown="document.form['t<?php echo $row['test_id']; ?>'].checked = true;">
		<td><input type="radio" name="id" value="<?php echo $row['test_id']; ?>" id="t<?php echo $row['test_id']; ?>"></td>

		<td><?php
			if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
				echo '<em>'._AT('ongoing').'</em>';
			} else if ($row['ue'] < time() ) {
				echo '<em>'._AT('expired').'</em>';
			} else if ($row['us'] > time() ) {
				echo '<em>'._AT('pending').'</em>';
			} ?></td>
		<td><?php echo $row['title']; ?></td>
		<td><?php echo AT_date('%j/%n/%y %G:%i', $row['start_date'], AT_DATE_MYSQL_DATETIME). ' ' ._AT('to_2').' ';
			echo AT_date('%j/%n/%y %G:%i', $row['end_date'], AT_DATE_MYSQL_DATETIME); ?></td>

		<td><?php
				if ($row['result_release'] == AT_RELEASE_IMMEDIATE) {
					echo _AT('release_immediate');
				} else if ($row['result_release'] == AT_RELEASE_MARKED) {
					echo _AT('release_marked');
				} else if ($row['result_release'] == AT_RELEASE_NEVER) {
					echo _AT('release_never');
				}
		?></td>
	</tr>
<?php endwhile; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>