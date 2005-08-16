<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'lib/mods.inc.php');

if(isset($_GET['mod_dir'])) {
	$dir_name = $_GET['mod_dir'];

	if (isset($_GET['install'])) {
		header('Location: '.$_base_href.'admin/modules/confirm.php?mod='.$dir_name);
		exit;
	} elseif ($_GET['details']) {
		header('Location: '.$_base_href.'admin/modules/details.php?mod='.$dir_name.';new=1');
		exit;
	}

}  else if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['install'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

//get current modules
$installed_mods = get_installed_mods();

//look for uninstalled modules
$new_mods = find_mods($installed_mods);

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="installform">
<table class="data" summary="" rules="cols" style="width:30%;">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('directory_name'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="7">
		<input type="submit" name="details"  value="<?php echo _AT('details'); ?>" />
		<input type="submit" name="install"  value="<?php echo _AT('install'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
<?php 
if (!empty($new_mods)):
	foreach($new_mods as $row) : ?>
		<tr onmousedown="document.installform['m_<?php echo $row['dir_name']; ?>'].checked = true;">
			<td valign="top">
				<input type="radio" id="m_<?php echo $row['dir_name']; ?>" name="mod_dir" value="<?php echo $row['dir_name']; ?>" />
			</td>

			<td valign="top"><label for="m_<?php echo $row['dir_name']; ?>"><code><?php echo $row['dir_name']; ?>/</code></label></td>
		</tr>
	<?php endforeach; 
else: ?>
		<tr>
			<td valign="top" colspan="2">
				<?php echo AT_NONE; ?>
			</td>
		</tr>
<?php endif; ?>
</tbody>

</table>
</form>
<br />


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>