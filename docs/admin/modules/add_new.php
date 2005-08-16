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

if (isset($_GET['mod'])) {
	$dir_name = str_replace(array('.','..','/'), '', $_GET['mod']);  ;

	if (isset($_GET['install'])) {
		header('Location: '.$_base_href.'admin/modules/confirm.php?mod='.$dir_name);
		exit;
	} elseif ($_GET['details']) {
		header('Location: '.$_base_href.'admin/modules/details.php?mod='.$dir_name.';new=1');
		exit;
	}

} else if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['install'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

//get current modules
$installed_mods = get_installed_mods();

//look for uninstalled modules
$new_mods = find_mods($installed_mods);

if (!empty($new_mods)):

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
	foreach($new_mods as $row) : ?>
		<tr onmousedown="document.installform['m_<?php echo $row['dir_name']; ?>'].checked = true;">
			<td valign="top">
				<input type="radio" id="m_<?php echo $row['dir_name']; ?>" name="mod" value="<?php echo $row['dir_name']; ?>" />
			</td>

			<td valign="top"><label for="m_<?php echo $row['dir_name']; ?>"><code><?php echo $row['dir_name']; ?>/</code></label></td>
		</tr>
	<?php endforeach; ?>
</tbody>

</table>
</form>
<br />
<?php else: ?>		
	<p><?php echo _AT('none_found'); ?></p>
<?php endif; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>