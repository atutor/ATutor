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

authenticate(AT_PRIV_TEST_MARK);

$tid = intval($_REQUEST['tid']);


if (isset($_GET['delete'], $_GET['id'])) {
	header('Location:delete_result.php?tid='.$tid.SEP.'rid='.$_GET['id']);
	exit;
} else if (isset($_GET['edit'], $_GET['id'])) {
	header('Location:view_results.php?tid='.$tid.SEP.'rid='.$_GET['id']);
	exit;
}/* else if (!empty($_GET) && !$_GET['p'] && !$_GET['asc'] && !$_GET['desc'] && !$_GET['filter'] && !$_GET['reset_filter']) {
	$msg->addError('NO_ITEM_SELECTED');
}*/

$page_string = '';
$orders = array('asc' => 'desc', 'desc' => 'asc');

require(AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['reset_filter']) {
	unset($_GET);
}

if (isset($_GET['status']) && ($_GET['status'] != '')) {
	if ($_GET['status'] == 0) {
		$status = " AND R.final_score=''";
	} else {
		$status = " AND R.final_score<>''";
	}
	$page_string .= SEP.'status='.$_GET['status'];
} else {
	$status = '';
}


//get test info
$sql	= "SELECT out_of, anonymous, title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);
if (!($row = mysql_fetch_array($result))){
	$msg->printErrors('TEST_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$out_of = $row['out_of'];
$anonymous = $row['anonymous'];

//count total
$sql	= "SELECT count(*) as cnt FROM ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."members M WHERE R.test_id=$tid AND R.member_id=M.member_id";
$result	= mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$num_sub = $row['cnt'];

//count unmarked
$sql	= "SELECT count(*) as cnt FROM ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."members M WHERE R.test_id=$tid AND R.member_id=M.member_id AND R.final_score=''";
$result	= mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$num_unmarked = $row['cnt'];

//get results based on filtre
if ($anonymous == 1) {
	$sql	= "SELECT R.*, '<em>"._AT('anonymous')."</em>' AS login FROM ".TABLE_PREFIX."tests_results R WHERE R.test_id=$tid $status";
} else {
	$sql	= "SELECT R.*, M.login FROM ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."members M WHERE R.test_id=$tid AND R.member_id=M.member_id $status";
}

$result = mysql_query($sql, $db);
if (!($row = mysql_fetch_assoc($result))) {
	echo _AT('no_results_available');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$num_results = $row['cnt'];

$msg->printAll();

echo '<p>'.$num_sub.' '._AT('submissions').', <strong>'.$num_unmarked.' '._AT('unmarked').'</strong></p>';

?>

<h3><?php echo AT_print($row['title'], 'tests.title'); ?></h3><br />

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />

	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('status'); ?><br />
			<input type="radio" name="status" value="1" id="s0" <?php if ($_GET['status'] == 1) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('marked'); ?></label> 

			<input type="radio" name="status" value="0" id="s1" <?php if ($_GET['status'] == 0) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('unmarked'); ?></label> 

		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />

<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col" width="1%">&nbsp;</th>
	<th scope="col"><?php echo _AT('username'); ?></th>
	<th scope="col"><?php echo _AT('date_taken'); ?></th>
	<th scope="col"><?php echo _AT('mark'); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<td colspan="6"><input type="submit" name="edit" value="<?php echo _AT('view_mark_test'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>

<tbody>
<?php do { ?>
	<tr>
		<td><input type="radio" name="id" value="<?php echo $row['result_id']; ?>" id="r<?php echo $row['result_id']; ?>" /></td>
		<td><label for="r<?php echo $row['result_id']; ?>"><?php echo $row['login']; ?></label></td>
		<td><?php echo AT_date('%j/%n/%y %G:%i', $row['date_taken'], AT_DATE_MYSQL_DATETIME); ?></td>
		<td align="center">
			<?php if ($out_of) {
				if ($row['final_score'] != '') { 
					echo $row['final_score'].'/'.$out_of;
				} else {
					echo _AT('unmarked');
				}
			} else {
				echo _AT('na');
			}
			?>
		</td>
	</tr>
<?php } while ($row = mysql_fetch_assoc($result)); ?>

</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>