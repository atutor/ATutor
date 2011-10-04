<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'../mods/_standard/links/lib/links.inc.php');

if (!manage_links()) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/links/index.php');
	exit;
}

if ((isset($_POST['delete']) || isset($_POST['edit'])) && !isset($_POST['cat_id'])) {
		$msg->addError('NO_ITEM_SELECTED');
} else if (isset($_POST['delete'])) {
	//check if links are in the cat
	$sql	= "SELECT link_id FROM ".TABLE_PREFIX."links WHERE cat_id=$_POST[cat_id]";
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
<table summary="" class="data" rules="cols" align="center" style="width: 95%;">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('name'); ?></th>
	<th scope="col"><?php echo _AT('parent'); ?></th>
</tr>
</thead>

<?php
$categories = get_link_categories(true, true);

if (!empty($categories)) { ?>
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

<?php foreach ($categories as $id=>$row) {

		if (!empty($row['cat_name'])) {

		$parent_cat_name = '';
		if ($row['cat_parent']) {
			$sql_cat	= "SELECT name, owner_id, owner_type FROM ".TABLE_PREFIX."links_categories WHERE cat_id=".$row['cat_parent'];
			$result_cat = mysql_query($sql_cat, $db);
			$row_cat = mysql_fetch_assoc($result_cat);
			$parent_cat_name = AT_print($row_cat['name'], 'links_categories.name');

			if (empty($parent_cat_name)) {
				$parent_cat_name = get_group_name($row_cat['owner_id']);
			}
		} else {
			$parent_cat_name = '<strong>'._AT('none').'</strong>';
		}
	?>
		<tr onmousedown="document.form['m<?php echo $id; ?>'].checked = true;rowselect(this);" id="r_<?php echo $id; ?>">
			<td width="10"><input type="radio" name="cat_id" value="<?php echo $id; ?>" id="m<?php echo $id; ?>" /></td>
			<td><label for="m<?php echo $id; ?>"><?php echo AT_print($row['cat_name'], 'members.first_name'); ?></label></td>
			<td><?php echo $parent_cat_name; ?></td>
		</tr>
		</tbody>

<?php
		} 
	}?>
<?php
} else { ?>
	<tr>
		<td colspan="3"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php } ?>


</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>