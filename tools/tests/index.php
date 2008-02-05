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

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_TESTS);

if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: edit_test.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['preview'], $_GET['id'])) {
	header('Location: preview.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['questions'], $_GET['id'])) {
	header('Location: questions.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['submissions'], $_GET['id'])) {
	header('Location: results.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['statistics'], $_GET['id'])) {
	header('Location: results_all_quest.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: delete_test.php?tid='.$_GET['id']);
	exit;
} else if (isset($_GET['edit']) 
		|| isset($_GET['preview']) 
		|| isset($_GET['questions']) 
		|| isset($_GET['submissions']) 
		|| isset($_GET['statistics']) 
		|| isset($_GET['delete'])) {

	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');


/* get a list of all the tests we have, and links to create, edit, delete, preview */

$sql	= "SELECT *, UNIX_TIMESTAMP(start_date) AS us, UNIX_TIMESTAMP(end_date) AS ue FROM ".TABLE_PREFIX."tests WHERE course_id=$_SESSION[course_id] ORDER BY start_date DESC";
$result	= mysql_query($sql, $db);
$num_tests = mysql_num_rows($result);

$cols=6;
?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="" style="width: 90%" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title');          ?></th>
	<th scope="col"><?php echo _AT('status');         ?></th>
	<th scope="col"><?php echo _AT('availability');   ?></th>
	<th scope="col"><?php echo _AT('result_release'); ?></th>
	<th scope="col"><?php echo _AT('submissions'); ?></th>
	<th scope="col"><?php echo _AT('assigned_to'); ?></th>
</tr>
</thead>

<?php if ($num_tests): ?>
	<tfoot>
	<tr>
		<td colspan="7">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />
			<input type="submit" name="preview" value="<?php echo _AT('preview'); ?>" />
			<input type="submit" name="questions" value="<?php echo _AT('questions'); ?>" />
		</td>
	</tr>
	<tr>	
		<td colspan="7" style="padding-left:38px;">
			<input type="submit" name="submissions" value="<?php echo _AT('submissions'); ?>" />
			<input type="submit" name="statistics" value="<?php echo _AT('statistics'); ?>" />
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		</td>
	</tr>
	</tfoot>
	<tbody>

	<?php while ($row = mysql_fetch_assoc($result)) : ?>
		<tr onmousedown="document.form['t<?php echo $row['test_id']; ?>'].checked = true;rowselect(this);" id="r_<?php echo $row['test_id']; ?>">
			<td><input type="radio" name="id" value="<?php echo $row['test_id']; ?>" id="t<?php echo $row['test_id']; ?>" /></td>
			<td><label for="t<?php echo $row['test_id']; ?>"><?php echo $row['title']; ?></label></td>
			<td><?php
				if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
					echo '<em>'._AT('ongoing').'</em>';
				} else if ($row['ue'] < time() ) {
					echo '<em>'._AT('expired').'</em>';
				} else if ($row['us'] > time() ) {
					echo '<em>'._AT('pending').'</em>';
				} ?></td>
			<td><?php $startend_date_format=_AT('startend_date_format'); echo AT_date( $startend_date_format, $row['start_date'], AT_DATE_MYSQL_DATETIME). ' ' ._AT('to_2').' ';
				echo AT_date($startend_date_format, $row['end_date'], AT_DATE_MYSQL_DATETIME); ?></td>

			<td><?php
				if ($row['result_release'] == AT_RELEASE_IMMEDIATE) {
					echo _AT('release_immediate');
				} else if ($row['result_release'] == AT_RELEASE_MARKED) {
					echo _AT('release_marked');
				} else if ($row['result_release'] == AT_RELEASE_NEVER) {
					echo _AT('release_never');
				}
			?></td>
			<td><?php
				//get # marked submissions
				$sql_sub = "SELECT COUNT(*) AS sub_cnt FROM ".TABLE_PREFIX."tests_results WHERE status=1 AND test_id=".$row['test_id'];
				$result_sub	= mysql_query($sql_sub, $db);
				$row_sub = mysql_fetch_assoc($result_sub);
				echo $row_sub['sub_cnt'].' '._AT('submissions').', ';

				//get # submissions
				$sql_sub = "SELECT COUNT(*) AS marked_cnt FROM ".TABLE_PREFIX."tests_results WHERE status=1 AND test_id=".$row['test_id']." AND final_score=''";
				$result_sub	= mysql_query($sql_sub, $db);
				$row_sub = mysql_fetch_assoc($result_sub);
				echo $row_sub['marked_cnt'].' '._AT('unmarked');
				?>
			</td>
			<td><?php
				//get assigned groups
				$sql_sub = "SELECT G.title FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."tests_groups T USING (group_id) WHERE T.test_id=".$row['test_id'];
				$result_sub	= mysql_query($sql_sub, $db);
				if (mysql_num_rows($result_sub) == 0) {
					echo _AT('everyone');
				} else {
					$row_sub = mysql_fetch_assoc($result_sub);
					echo $row_sub['title'];
					do {
						echo ', '.$row_sub['title'];
					} while ($row_sub = mysql_fetch_assoc($result_sub));
				}				
				?>
			</td>
		</tr>
	<?php endwhile; ?>
<?php else: ?>
	<tbody>
	<tr>
		<td colspan="7"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>