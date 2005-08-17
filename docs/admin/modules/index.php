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

require(AT_INCLUDE_PATH.'classes/Module/ModuleParser.class.php');
require(AT_INCLUDE_PATH.'lib/mods.inc.php');

$dir_name = str_replace(array('.','..','/'), '', $_GET['mod_dir']);

if (isset($_GET['mod_dir'], $_GET['enable'])) {
	enable($dir_name);
	$msg->addFeedback('MOD_ENABLED');
} else if (isset($_GET['mod_dir'], $_GET['disable'])) {
	disable($dir_name);
	$msg->addFeedback('MOD_DISABLED');

} else if (isset($_GET['mod_dir'], $_GET['details'])) {
	header('Location: details.php?mod='.$_GET['mod_dir']);
	exit;

} else if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['details'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

//get current modules
$installed_mods = get_installed_mods();
$moduleParser   =& new ModuleParser();
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('module_name'); ?></th>
	<th scope="col"><?php echo _AT('status'); ?></th>
	<th scope="col"><?php echo _AT('version'); ?></th>
	<th scope="col"><?php echo _AT('directory_name'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<input type="submit" name="details" value="<?php echo _AT('details'); ?>" />
		<input type="submit" name="enable"  value="<?php echo _AT('enable'); ?>" />
		<input type="submit" name="disable" value="<?php echo _AT('disable'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
<?php foreach($installed_mods as $row) : ?>
	<?php if (!file_exists('../../mods/'.$row['dir_name'].'/module.xml')): ?>
		<?php $rows = array('name' => '<em>'._AT('missing_info').'</em>', 'version' => '<em>'._AT('missing_info').'</em>'); ?>
	<?php else: ?>
		<?php $moduleParser->parse(file_get_contents('../../mods/'.$row['dir_name'].'/module.xml')); ?>
		<?php $rows = $moduleParser->rows[0]; ?>
	<?php endif; ?>
	<?php $modules_exist = TRUE; ?>

	<tr onmousedown="document.form['t_<?php echo $row['dir_name']; ?>'].checked = true;">
		<td valign="top"><input type="radio" id="t_<?php echo $row['dir_name']; ?>" name="mod_dir" value="<?php echo $row['dir_name']; ?>" /></td>
		<td nowrap="nowrap" valign="top"><label for="t_<?php echo $row['dir_name']; ?>"><?php echo $rows['name']; ?></label></td>
		<td valign="top"><?php
			if ($row['status'] == AT_MOD_ENABLED) {
				echo _AT('enabled');
			} else if ($row['status'] == AT_MOD_DISABLED) {
				echo _AT('disabled'); 
			}
			?></td>
		<td valign="top"><?php echo $rows['version']; ?></td>
		<td valign="top"><code><?php echo $row['dir_name']; ?></code></td>
	</tr>
<?php endforeach; ?>
<?php if (!isset($modules_exist)): ?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>