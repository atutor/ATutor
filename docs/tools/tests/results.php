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


$sql	= "SELECT out_of, anonymous, title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);
if (!($row = mysql_fetch_array($result))){
	$msg->printErrors('TEST_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$out_of = $row['out_of'];
$anonymous = $row['anonymous'];

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h3>'.AT_print($row['title'], 'tests.title').'</h3><br />';

/*echo '<p>';
if ($_GET['m']) {
	echo '<a href="'.$_SERVER['PHP_SELF'].'?tid='.$tid.'">'._AT('show_marked_unmarked').'</a>';		
} else {
	echo _AT('show_marked_unmarked');
}

echo ' | ';
if ($_GET['m'] != 1) {
	echo '<a href="'.$_SERVER['PHP_SELF'].'?tid='.$tid.SEP.'m=1">'._AT('show_unmarked').'</a>';
} else {
	echo _AT('show_unmarked');
}
echo ' | ';
if ($_GET['m'] != 2){
	echo '<a href="'.$_SERVER['PHP_SELF'].'?tid='.$tid.SEP.'m=2">'._AT('show_marked').'</a>';
} else {
	echo _AT('show_marked');
}

echo '</p>';*/


if ($_GET['m'] == 1) {
	$show = ' AND R.final_score=\'\'';
} else if ($_GET['m'] == 2) {
	$show = ' AND R.final_score<>\'\'';
} else {
	$show = '';
}

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

$msg->printAll();

if ($anonymous == 1) {
	$sql	= "SELECT R.*, '<em>"._AT('anonymous')."</em>' AS login FROM ".TABLE_PREFIX."tests_results R WHERE R.test_id=$tid $show";
} else {
	$sql	= "SELECT R.*, M.login FROM ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."members M WHERE R.test_id=$tid AND R.member_id=M.member_id $show";
}

$result	= mysql_query($sql, $db);
$num_results = mysql_num_rows($result);

echo '<p>'.$num_sub.' '._AT('submissions').', <strong>'.$num_unmarked.' '._AT('unmarked').'</strong></p>';

if (!($row = mysql_fetch_assoc($result))) {
	echo _AT('no_results_available');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

?>

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