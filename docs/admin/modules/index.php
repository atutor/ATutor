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
require(AT_INCLUDE_PATH.'lib/modules.inc.php');

$dir_name = str_replace(array('.','..','/'), '', $_GET['mod_dir']);

if (isset($_GET['mod_dir'], $_GET['enable'])) {
	$module =& $moduleFactory->getModule($_GET['mod_dir']);
	//debug($module);
	// $module->enable();
	enable($dir_name);
	$msg->addFeedback('MOD_ENABLED');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_GET['mod_dir'], $_GET['disable'])) {
	$module =& $moduleFactory->getModule($_GET['mod_dir']);
	if ($module->isCore()) {
		// core modules cannot be disabled!
		$msg->addError('DISABLE_CORE_MODULE');
	} else if ($module->isEnabled()) {
		//$module->disable();
		disable($dir_name);
		$msg->addFeedback('MOD_DISABLED');
	}
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_GET['mod_dir'], $_GET['details'])) {
	header('Location: details.php?mod='.$_GET['mod_dir']);
	exit;

} else if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['details'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$module_list = $moduleFactory->getInstalledModules();
$keys = array_keys($module_list);

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
<?php foreach($keys as $dir_name) : $module =& $module_list[$dir_name]; ?>

	<tr onmousedown="document.form['t_<?php echo $dir_name; ?>'].checked = true;">
		<td valign="top"><input type="radio" id="t_<?php echo $dir_name; ?>" name="mod_dir" value="<?php echo $dir_name; ?>" /></td>
		<td nowrap="nowrap" valign="top"><label for="t_<?php echo $dir_name; ?>"><?php echo $module->getName($_SESSION['lang']); ?></label></td>
		<td valign="top"><?php
			if ($module->isCore()) {
				echo _AT('core');
			} else if ($module->isEnabled()) {
				echo _AT('enabled');
			} else {
				echo _AT('disabled'); 
			}
			?></td>
		<td valign="top"><?php echo $module->getVersion(); ?></td>
		<td valign="top"><code><?php echo $dir_name; ?>/</code></td>
	</tr>
<?php endforeach; ?>
<?php if (!$keys): ?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>