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
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_LINKS);

if ((isset($_POST['delete']) || isset($_POST['edit'])) && !isset($_POST['cat_id'])) {
		$msg->addError('NO_ITEM_SELECTED');
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

require(AT_INCLUDE_PATH.'header.inc.php'); 


?>
<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 70%;">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('name'); ?></th>
	<th scope="col"><?php echo _AT('parent'); ?></th>
</tr>
</thead>

<?php
$sql	= "SELECT * FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$_SESSION[course_id] ORDER BY CatName asc";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) { ?>

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
<?php do {
		$parent_cat_name = '';
		if ($row['CatParent']) {
			$sql_cat	= "SELECT CatName FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$_SESSION[course_id] AND CatID=".$row['CatParent'];
			$result_cat = mysql_query($sql_cat, $db);
			$row_cat = mysql_fetch_assoc($result_cat);
			$parent_cat_name = AT_print($row_cat['CatName'], 'resource_categories.catname');
		} else {
			$parent_cat_name = '<em>'._AT('none').'</em>';
		}
	?>
		<tr onmousedown="document.form['m<?php echo $row['CatID']; ?>'].checked = true;rowselect(this);" id="r_<?php echo $row['CatID']; ?>">
			<td width="10"><input type="radio" name="cat_id" value="<?php echo $row['CatID']; ?>" id="m<?php echo $row['CatID']; ?>" /></td>
			<td><label for="m<?php echo $row['CatID']; ?>"><?php echo AT_print($row['CatName'], 'members.first_name'); ?></label></td>
			<td><?php echo $parent_cat_name; ?></td>
		</tr>
<?php	} while ($row = mysql_fetch_assoc($result)); ?>
	</tbody>
<?php
} else { ?>
	<tr>
		<td colspan="3"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php } ?>


</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>