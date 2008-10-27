<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

$dir_name = str_replace(array('.','..'), '', $_GET['mod_dir']);

$args = '';

if (isset($_GET['enabled'])  && $_GET['enabled'])  {  $args .= 'enabled=1';      }
if (isset($_GET['disabled']) && $_GET['disabled']) {  $args .= SEP.'disabled=1'; }
if (isset($_GET['missing'])  && $_GET['missing'])  {  $args .= SEP.'missing=1';  }
if (isset($_GET['partially_uninstalled'])  && $_GET['partially_uninstalled'])  {  $args .= SEP.'partially_uninstalled=1';  }
if (isset($_GET['core'])     && $_GET['core'])     {  $args .= SEP.'core=1';     }
if (isset($_GET['standard']) && $_GET['standard']) {  $args .= SEP.'standard=1'; }
if (isset($_GET['extra'])    && $_GET['extra'])    {  $args .= SEP.'extra=1';    }

if (isset($_GET['reset_filter'])) {
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

if (isset($_GET['mod_dir'], $_GET['enable'])) {
	$module =& $moduleFactory->getModule($_GET['mod_dir']);
	if (!$module->isEnabled() && !$module->isCore() && !$module->isMissing() && !$module->isPartiallyUninstalled()) {
		$module->enable();
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: '.$_SERVER['PHP_SELF'] . '?' . $args);
	exit;
} else if (isset($_GET['mod_dir'], $_GET['disable'])) {
	$module =& $moduleFactory->getModule($_GET['mod_dir']);
	if ($module->isCore()) {
		// core modules cannot be disabled!
		$msg->addError('DISABLE_CORE_MODULE');
	} else if ($module->isMissing()) {
		$msg->addError('DISABLE_MISSING_MODULE');
	} else if ($module->isPartiallyUninstalled()) {
		$msg->addError('DISABLE_PARTIALLY_UNINSTALLED_MODULE');
	} else if ($module->isEnabled()) {
		$module->disable();
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
	header('Location: '.$_SERVER['PHP_SELF'] . '?' . $args);
	exit;
} else if (isset($_GET['mod_dir'], $_GET['details'])) {
	header('Location: details.php?mod='.$_GET['mod_dir'] . SEP . $args);
	exit;

} else if (isset($_GET['mod_dir'], $_GET['uninstall'])) {
	$module =& $moduleFactory->getModule($_GET['mod_dir']);

	$module_folder = '../../mods/'.$_GET['mod_dir'];
	// check if the module has been un-installed
	if (!file_exists($module_folder))
	{
		$msg->addError('ALREADY_UNINSTALLED');
	}

	// only extra modules can be uninstalled
	if (!$module->isExtra()) {
		$msg->addError('ONLY_UNINSTALL_EXTRA_MODULE');
	} 
	// check if the module is installed via "Available Extra Modules"
	// which are the modules can be un-installed 
	else if (!file_exists($module_folder.'/module_uninstall.php') || !is_writable($module_folder))
	{
		$msg->addError('CANNOT_UNINSTALL_MANUAL_MODULE');
	}
	
  if (!$msg->containsErrors())
	{
		header('Location: module_uninstall_step_1.php?mod=' . urlencode($_GET['mod_dir']) . SEP.'args='.urlencode($args));
		exit;
	}

} else if (isset($_GET['mod_dir'], $_GET['export'])) {
	$module =& $moduleFactory->getModule($_GET['mod_dir']);

	$module_folder = '../../mods/'.$_GET['mod_dir'];
	// check if the module has been un-installed
	if (!file_exists($module_folder))
	{
		$msg->addError('ITEM_NOT_FOUND');
	}

	// only extra modules can be uninstalled
	if (!$module->isExtra()) {
		$msg->addError('ONLY_EXPORT_EXTRA_MODULE');
	} 
	
  if (!$msg->containsErrors())
	{
		require(AT_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
		
		$zipfile = new zipfile();
		$zipfile->add_dir('../../mods/'.$_GET['mod_dir'].'/', $_GET['mod_dir'].'/');
		$zipfile->close();
		$zipfile->send_file($_GET['mod_dir']);
		exit;
	}

} else if (isset($_GET['disable']) || isset($_GET['enable']) || isset($_GET['details']) || isset($_GET['uninstall']) || isset($_GET['export'])) {
	$msg->addError('NO_ITEM_SELECTED');
	header('Location: '.$_SERVER['PHP_SELF'] . '?' . $args);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$module_status_bits = $module_type_bits = 0;

if ($_GET['enabled'])  { $module_status_bits += AT_MODULE_STATUS_ENABLED;  }
if ($_GET['disabled']) {	$module_status_bits += AT_MODULE_STATUS_DISABLED; }
if ($_GET['missing'])  {	$module_status_bits += AT_MODULE_STATUS_MISSING;  }
if ($_GET['partially_uninstalled'])  {	$module_status_bits += AT_MODULE_STATUS_PARTIALLY_UNINSTALLED;  }

if ($_GET['core'])     {  $module_type_bits += AT_MODULE_TYPE_CORE;     }
if ($_GET['standard']) {  $module_type_bits += AT_MODULE_TYPE_STANDARD; }
if ($_GET['extra'])    {  $module_type_bits += AT_MODULE_TYPE_EXTRA;    }

if ($module_status_bits == 0) {
	$module_status_bits = AT_MODULE_STATUS_DISABLED | AT_MODULE_STATUS_ENABLED | AT_MODULE_STATUS_MISSING | AT_MODULE_STATUS_PARTIALLY_UNINSTALLED;
	$_GET['enabled'] = $_GET['disabled'] = $_GET['missing'] = $_GET['partially_uninstalled'] = 1;
}

if ($module_type_bits == 0) {
	$module_type_bits = AT_MODULE_TYPE_STANDARD + AT_MODULE_TYPE_EXTRA;
	$_GET['standard'] = $_GET['extra'] = 1;
}


$module_list = $moduleFactory->getModules($module_status_bits, $module_type_bits, $sort = TRUE);
$keys = array_keys($module_list);
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', count($keys)); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('type'); ?><br />
			<input type="checkbox" name="core" value="1" id="t0" <?php if ($_GET['core']) { echo 'checked="checked"'; } ?> /><label for="t0"><?php echo _AT('core'); ?></label>

			<input type="checkbox" name="standard" value="1" id="t1" <?php if ($_GET['standard']) { echo 'checked="checked"'; } ?> /><label for="t1"><?php echo _AT('standard'); ?></label> 

			<input type="checkbox" name="extra" value="1" id="t2" <?php if ($_GET['extra']) { echo 'checked="checked"'; } ?> /><label for="t2"><?php echo _AT('extra'); ?></label> 
		</div>


		<div class="row">
			<?php echo _AT('status'); ?><br />
			<input type="checkbox" name="enabled" value="1" id="s0" <?php if ($_GET['enabled']) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('enabled'); ?></label> 

			<input type="checkbox" name="disabled" value="1" id="s1" <?php if ($_GET['disabled']) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('disabled'); ?></label> 

			<input type="checkbox" name="missing" value="1" id="s2" <?php if ($_GET['missing']) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('missing'); ?></label> 

			<input type="checkbox" name="partially_uninstalled" value="1" id="s3" <?php if ($_GET['partially_uninstalled']) { echo 'checked="checked"'; } ?> /><label for="s3"><?php echo _AT('partially_uninstalled'); ?></label> 
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">

<input type="hidden" name="enabled" value="<?php echo (int) isset($_GET['enabled']); ?>" />
<input type="hidden" name="disabled" value="<?php echo (int) isset($_GET['disabled']); ?>" />
<input type="hidden" name="core" value="<?php echo (int) isset($_GET['core']); ?>" />
<input type="hidden" name="standard" value="<?php echo (int) isset($_GET['standard']); ?>" />
<input type="hidden" name="extra" value="<?php echo (int) isset($_GET['extra']); ?>" />
<input type="hidden" name="missing" value="<?php echo (int) isset($_GET['missing']); ?>" />
<input type="hidden" name="partially_uninstalled" value="<?php echo (int) isset($_GET['partially_uninstalled']); ?>" />

<table class="data" summary="" rules="cols">
<colgroup>
		<col />
		<col class="sort" />
		<col span="4" />
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('module_name'); ?></th>
	<th scope="col"><?php echo _AT('type'); ?></th>
	<th scope="col"><?php echo _AT('status'); ?></th>
	<th scope="col"><?php echo _AT('cron'); ?></th>
	<th scope="col"><?php echo _AT('directory_name'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
		<input type="submit" name="details" value="<?php echo _AT('details'); ?>" />
		<input type="submit" name="enable"  value="<?php echo _AT('enable'); ?>" />
		<input type="submit" name="disable" value="<?php echo _AT('disable'); ?>" />
		<input type="submit" name="uninstall" value="<?php echo _AT('uninstall'); ?>" />
		<input type="submit" name="export" value="<?php echo _AT('export'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php foreach($keys as $dir_name) : $module =& $module_list[$dir_name]; $i++?>

	<tr onmousedown="document.form['t_<?php echo $i; ?>'].checked = true; rowselect(this);" id="r_<?php echo $i; ?>">
		<td valign="top"><input type="radio" id="t_<?php echo $i; ?>" name="mod_dir" value="<?php echo $dir_name; ?>" /></td>
		<td nowrap="nowrap" valign="top"><label for="t_<?php echo $i; ?>"><?php echo $module->getName(); ?></label></td>
		<td valign="top"><?php
			if ($module->isCore()) {
				echo '<strong>'._AT('core').'</strong>';
			} else if ($module->isStandard()) {
				echo _AT('standard');
			} else {
				echo _AT('extra');
			}
			?></td>
		<td valign="top"><?php
			if ($module->isEnabled()) {
				echo _AT('enabled');
			} else if ($module->isMissing()) {
				echo '<strong>'._AT('missing').'</strong>';
			} else if ($module->isPartiallyUninstalled()) {
				echo _AT('partially_uninstalled');
			} else {
				echo '<em>'._AT('disabled').'</em>';
			}
			?></td>
		<td valign="top" align="center">
			<?php if ($module->getCronInterval()): ?>
				<?php echo _AT('minutes', $module->getCronInterval()); ?>
			<?php else: ?>
				-
			<?php endif; ?>
		</td>
		<td valign="top"><code><?php echo $dir_name; ?>/</code></td>
	</tr>
<?php endforeach; ?>
<?php if (!$keys): ?>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>