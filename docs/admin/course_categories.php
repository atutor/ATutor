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

$page = 'categories';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');

if ($_SESSION['course_id'] > -1) { exit; }
//require(AT_INCLUDE_PATH.'lib/admin_categories.inc.php');

if ((isset($_POST['delete']) || isset($_POST['edit'])) && !isset($_POST['cat_id'])) {
		$msg->addError('NO_CAT_SELECTED');
} else if (isset($_POST['delete'])) {
	header('Location: delete_category.php?cat_id='.$_POST['cat_id']);
	exit;
} else if (isset($_POST['edit'])) {
	header('Location: edit_category.php?cat_id='.$_POST['cat_id']);
	exit;
}

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'cat_name';
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

<?php if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) : ?>
	<th scope="col"><small<?php echo $highlight_last_name; ?>><?php echo _AT('theme'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=theme<?php echo SEP; ?>order=asc#list" title="<?php echo _AT('theme_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('theme_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=theme<?php echo SEP; ?>order=desc#list" title="<?php echo _AT('theme_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('theme_descending'); ?>" border="0" height="7" width="11" /></a></small></th>
<?php endif; ?>

	<?php //num courses?>

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

	$sql	= "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY $col $order";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) : 
		$parent_cat_name = '';
		if ($row['cat_parent']) {
			$sql_cat	= "SELECT cat_name FROM ".TABLE_PREFIX."course_cats WHERE cat_id=".$row['cat_parent'];
			$result_cat = mysql_query($sql_cat, $db);
			$row_cat = mysql_fetch_assoc($result_cat);
			$parent_cat_name = $row_cat['cat_name'];
		} 
	?>
		<tr onmousedown="document.form['m<?php echo $row['cat_id']; ?>'].checked = true;">
			<td width="10"><input type="radio" name="cat_id" value="<?php echo $row['cat_id']; ?>" id="m<?php echo $row['cat_id']; ?>"></td>
			<td><?php echo AT_print($row['cat_name'], 'course_cats.cat_name'); ?></td>
			<td><?php echo AT_print($parent_cat_name, 'course_cats.cat_name'); ?></td>
			<?php if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) : ?>
				<td><?php echo AT_print(get_theme_name($row['theme']), 'themes.title'); ?></td>
			<?php endif; ?>

		</tr>
<?php endwhile; ?>
</tbody>
</table>

</form>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>