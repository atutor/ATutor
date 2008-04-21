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
// $Id: myown_patches.php 7208 2008-02-20 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PATCHER);

if (isset($_POST['remove'], $_POST['myown_patch_id'])) 
{
	header('Location: patch_delete.php?myown_patch_id='.$_POST['myown_patch_id']);
	exit;
} 
else if (isset($_POST['edit'], $_POST['myown_patch_id'])) 
{
	header('Location: patch_edit.php?myown_patch_id='.$_POST['myown_patch_id']);
	exit;
} 
else if (!empty($_POST) && !isset($_POST['myown_patch_id'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>
<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 70%;">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('atutor_patch_id'); ?></th>
	<th scope="col"><?php echo _AT('atutor_version_to_apply'); ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
	<th scope="col"><?php echo _AT('last_modified'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="remove" value="<?php echo _AT('remove'); ?>" /> 
		</div>
	</td>
</tr>
<tr>
	<td colspan="5"></td>
</tr>
</tfoot>
<tbody>
<?php
$include_javascript = true;

$sql = "SELECT * from ".TABLE_PREFIX."myown_patches m order by last_modified desc";

$result = mysql_query($sql, $db) or die(mysql_error());

if (mysql_num_rows($result) == 0)
{
?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php 
}
else
{
	while ($row = mysql_fetch_assoc($result))
	{
	?>
		<tr onmousedown="document.form['m<?php echo $row['myown_patch_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['myown_patch_id']; ?>">
			<td width="10"><input type="radio" name="myown_patch_id" value="<?php echo $row['myown_patch_id']; ?>" id="m<?php echo $row['myown_patch_id']; ?>" <?php if ($row['myown_patch_id']==$_POST['myown_patch_id']) echo 'checked'; ?> /></td>
			<td><label for="m<?php echo $row['myown_patch_id']; ?>"><?php echo $row['atutor_patch_id']; ?></label></td>
			<td><?php echo $row['applied_version']; ?></td>
			<td><?php echo $row['description']; ?></td>
			<td><?php echo $row['last_modified']; ?></td>
		</tr>
<?php 
	}
}
?>

</tbody>
</table>

</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
