<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: index.php 7981 2008-10-01 19:34:17Z cindy $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_POLLS);

if (isset($_POST['edit'], $_POST['poll'])) {
	header('Location: edit.php?poll_id=' . $_POST['poll']);
	exit;
} else if (isset($_POST['delete'], $_POST['poll'])) { 
	header('Location: delete.php?pid=' . $_POST['poll'] );
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('question' => 1, 'created_date' => 1, 'total' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'created_date';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'created_date';
} else {
	// no order set
	$order = 'desc';
	$col   = 'created_date';
}

$sql	= "SELECT poll_id, question, created_date, total FROM ".TABLE_PREFIX."polls WHERE course_id=$_SESSION[course_id] ORDER BY $col $order";
$result = mysql_query($sql, $db);


?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<table class="data" summary="" rules="cols">
<colgroup>
	<?php if ($col == 'question'): ?>
		<col />
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'created_date'): ?>
		<col span="2" />
		<col class="sort" />
		<col />
	<?php elseif($col == 'total'): ?>
		<col span="3" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><a href="mods/_standard/polls/tools/index.php?<?php echo $orders[$order]; ?>=question"><?php echo _AT('question'); ?></a></th>
	<th scope="col"><a href="mods/_standard/polls/tools/index.php?<?php echo $orders[$order]; ?>=created_date"><?php echo _AT('created'); ?></a></th>
	<th scope="col"><a href="mods/_standard/polls/tools/index.php?<?php echo $orders[$order]; ?>=total"><?php echo _AT('total_votes'); ?></a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4">
		<input type="submit" name="edit"   value="<?php echo _AT('edit'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php if ($row = mysql_fetch_assoc($result)) : ?>
	<?php do { ?>
		<tr onmousedown="document.form['p_<?php echo $row['poll_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['poll_id']; ?>">
			<td><input type="radio" id="p_<?php echo $row['poll_id']; ?>" name="poll" value="<?php echo $row['poll_id']; ?>" /></td>
			<td><label for="p_<?php echo $row['poll_id']; ?>"><?php echo AT_print(htmlentities_utf8($row['question']), 'polls.question'); ?></label></td>
			<td><?php echo AT_DATE(_AT("server_date_format"), $row['created_date']); ?></td>
			<td><?php echo $row['total']; ?></td>
		</tr>
	<?php } while($row = mysql_fetch_assoc($result)); ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php');  ?>