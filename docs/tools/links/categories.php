<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ((isset($_POST['delete']) || isset($_POST['edit'])) && !isset($_POST['cat_id'])) {
		$msg->addError('NO_CAT_SELECTED');
} else if (isset($_POST['delete'])) {
	//check if links are in the cat
	$sql	= "SELECT LinkID FROM ".TABLE_PREFIX."resource_links WHERE CatID=$_POST[cat_id]";
	$result = mysql_query($sql, $db);
    if ($row = mysql_fetch_assoc($result)) {
		$msg->addError('LINK_CAT_NOT_EMPTY');
	
	} else {
		header('Location: categories_delete.php?cat_id='.$_POST['cat_id']);
		exit;
	}
} else if (isset($_POST['edit'])) {
	header('Location: categories_edit.php?cat_id='.$_POST['cat_id']);
	exit;
}

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'CatName';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'asc';
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$msg->printAll();

?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 70%;">

<thead>
<tr>
	<th scope="col">&nbsp;</th>

	<th scope="col"><small<?php echo $highlight_login; ?>><?php echo _AT('name'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=cat_name<?php echo SEP; ?>order=asc#list" title="<?php echo _AT('cat_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('cat_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=cat_name<?php echo SEP; ?>order=desc#list" title="<?php echo _AT('cat_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('cat_descending'); ?>" border="0" height="7" width="11" /></a></small></th>

	<th scope="col"><small<?php echo $highlight_first_name; ?>><?php echo _AT('parent'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=cat_parent<?php echo SEP; ?>order=asc#list" title="<?php echo _AT('parent_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('parent_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=cat_parent<?php echo SEP; ?>order=desc#list" title="<?php echo _AT('parent_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('parent_descending'); ?>" border="0" height="7" width="11" /></a></small></th>

</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4">
		<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
		</div>
	</td>
</tr>
</tfoot>
<tbody>
<?php

	$sql	= "SELECT * FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$_SESSION[course_id] ORDER BY $col $order";
	$result = mysql_query($sql, $db);
    if ($row = mysql_fetch_assoc($result)) {
		do {
			$parent_cat_name = '';
			if ($row['CatParent']) {
				$sql_cat	= "SELECT CatName FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$_SESSION[course_id] AND CatID=".$row['CatParent'];
				$result_cat = mysql_query($sql_cat, $db);
				$row_cat = mysql_fetch_assoc($result_cat);
				$parent_cat_name = $row_cat['CatName'];
			} 
		?>
			<tr onmousedown="document.form['m<?php echo $row['CatID']; ?>'].checked = true;">
				<td width="10"><input type="radio" name="cat_id" value="<?php echo $row['CatID']; ?>" id="m<?php echo $row['CatID']; ?>"></td>
				<td><?php echo AT_print($row['CatName'], 'members.first_name'); ?></td>
				<td><?php echo AT_print($parent_cat_name, 'members.last_name'); ?></td>
			</tr>
<?php	} 	while ($row = mysql_fetch_assoc($result));
	} else { ?>
		<tr>
			<td colspan="3"><?php echo _AT('cats_no_categories'); ?></td>
		</tr>
<?php } ?>
</tbody>
</table>

</form>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>