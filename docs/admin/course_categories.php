<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');
admin_authenticate(AT_ADMIN_PRIV_CATEGORIES);

if (isset($_POST['delete'], $_POST['cat_id'])) {
	header('Location: delete_category.php?cat_id='.$_POST['cat_id']);
	exit;
} else if (isset($_POST['edit'], $_POST['cat_id'])) {
	header('Location: edit_category.php?cat_id='.$_POST['cat_id']);
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
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
<?php if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) : ?>
	<th scope="col"><?php echo _AT('theme'); ?></th>
<?php endif; ?>
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
$sql	= "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_name";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)): ?>
	<?php
	do {
		$parent_cat_name = '';
		if ($row['cat_parent']) {
			$sql_cat	= "SELECT cat_name FROM ".TABLE_PREFIX."course_cats WHERE cat_id=".$row['cat_parent'];
			$result_cat = mysql_query($sql_cat, $db);
			$row_cat = mysql_fetch_assoc($result_cat);
			$parent_cat_name = $row_cat['cat_name'];
		} 
	?>
		<tr onmousedown="document.form['m<?php echo $row['cat_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['cat_id']; ?>">
			<td width="10"><input type="radio" name="cat_id" value="<?php echo $row['cat_id']; ?>" id="m<?php echo $row['cat_id']; ?>" /></td>
			<td><label for="m<?php echo $row['cat_id']; ?>"><?php echo AT_print($row['cat_name'], 'course_cats.cat_name'); ?></label></td>
			<td><?php echo AT_print($parent_cat_name, 'course_cats.cat_name'); ?></td>
			<?php if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) : ?>
				<td><?php echo AT_print(get_theme_name($row['theme']), 'themes.title'); ?></td>
			<?php endif; ?>

		</tr>
	<?php } while ($row = mysql_fetch_assoc($result)); ?>
<?php else: ?>
	<tr>
		<td colspan="3"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>

</form>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>