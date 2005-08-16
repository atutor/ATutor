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
// $Id: index.php 5262 2005-08-10 17:13:55Z joel $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'lib/mods.inc.php');

if(isset($_GET['mod_dir'])) {
	$dir_name = $_GET['mod_dir'];

	if (isset($_GET['enable'])) {
		enable($dir_name);
		$msg->addFeedback('MOD_ENABLED');
	} else if (isset($_GET['disable'])) {
		disable($dir_name);
		$msg->addFeedback('MOD_DISABLED');
	}

}  else if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['install'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

//get current modules
$installed_mods = get_installed_mods();
?>

<h3><?php echo _AT('system_modules'); ?></h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('status'); ?></th>
	<th scope="col"><?php echo _AT('version'); ?></th>
	<th scope="col"><?php echo _AT('directory_name'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="7">
		<input type="submit" name="enable"  value="<?php echo _AT('enable'); ?>" />
		<input type="submit" name="disable" value="<?php echo _AT('disable'); ?>" />
	</td>
</tr>
</tfoot>
<?php 
foreach($installed_mods as $row) : ?>
	<tbody>
	<tr onmousedown="document.form['t_<?php echo $row['dir_name']; ?>'].checked = true;">
		<td valign="top">
			<input type="radio" id="t_<?php echo $row['dir_name']; ?>" name="mod_dir" value="<?php echo $row['dir_name']; ?>" />
			<input type="hidden" name="<?php echo $row['dir_name']; ?>_version" value="<?php echo $row['version']; ?>" />
		</td>
		<td nowrap="nowrap" valign="top"><label for="t_<?php echo $row['dir_name']; ?>"><?php echo AT_print($row['real_name'], 'themes.title'); ?></label></td>
		<td valign="top"><?php
			if ($row['status'] == AT_MOD_ENABLED) {
				echo _AT('enabled');
			} else if ($row['status'] == AT_MOD_DISABLED) {
				echo _AT('disabled'); 
			}
			?>
		</td>
		<td valign="top"><?php echo $row['version']; ?></td>
		<td valign="top"><code><?php echo $row['dir_name']; ?>/</code></td>
	</tr>
	</tbody>
<?php endforeach; ?>
</table>
</form>



<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>