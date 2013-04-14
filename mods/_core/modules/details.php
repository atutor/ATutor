<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_MODULES);

require(AT_INCLUDE_PATH.'../mods/_core/modules/classes/ModuleParser.class.php');


if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/modules/add_new.php');
	exit;
} else if (isset($_POST['mod']) && isset($_POST['submit_yes'])) {
	$module = $moduleFactory->getModule($_POST['mod']);
	$module->load();
	$module->install();

	if ($msg->containsErrors()) {
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/details.php?mod='.$addslashes($_POST['mod']).SEP.'new=1');
	} else {
		$msg->addFeedback('MOD_INSTALLED');
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/index.php');
	}
	exit;
} else if (isset($_GET['submit'])) {
	$args = '';

	if (isset($_GET['enabled'])  && $_GET['enabled'])  {  $args .= 'enabled=1';      }
	if (isset($_GET['disabled']) && $_GET['disabled']) {  $args .= SEP.'disabled=1'; }
	if (isset($_GET['missing'])  && $_GET['missing'])  {  $args .= SEP.'missing=1';  }
	if (isset($_GET['core'])     && $_GET['core'])     {  $args .= SEP.'core=1';     }
	if (isset($_GET['standard']) && $_GET['standard']) {  $args .= SEP.'standard=1'; }
	if (isset($_GET['extra'])    && $_GET['extra'])    {  $args .= SEP.'extra=1';    }

	header('Location: index.php?'. $args);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$moduleParser = new ModuleParser();

$_REQUEST['mod'] = str_replace(array('.','..'), '', $_REQUEST['mod']);

$module = $moduleFactory->getModule($_GET['mod']);

$main_module_dir = $module->getModulePath();

if (!file_exists($main_module_dir.$_GET['mod'].'/module.xml')) {
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="mod" value="<?php echo $_GET['mod']; ?>" />
<input type="hidden" name="new" value="<?php echo $_GET['new']; ?>" />
<div class="input-form">
	<div class="row">
		<h3><?php echo $_GET['mod']; ?></h3>
	</div>

	<div class="row">
		<?php echo _AT('missing_info'); ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('back'); ?>" />
		<?php if (isset($_GET['new']) && $_GET['new']): ?>
			<input type="submit" name="install" value="<?php echo _AT('install'); ?>" />
		<?php endif; ?>
	</div>

</div>
</form>
<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$moduleParser->parse(file_get_contents($main_module_dir.$_GET['mod'].'/module.xml'));

$module = $moduleFactory->getModule($_GET['mod']);

$properties = $module->getProperties(array('maintainers', 'url', 'date', 'license', 'state', 'notes', 'version'));
?>

<?php if (isset($_REQUEST['new'])): ?>
	<?php
		$hidden_vars['mod'] = $_REQUEST['mod'];
		$hidden_vars['new'] = '1';
		$msg->addConfirm(array('ADD_MODULE', $_REQUEST['mod']), $hidden_vars);
		$msg->printConfirm();
	?>
<?php endif; ?>

<?php 

$savant->assign('module', $module);
$savant->assign('properties', $properties);
$savant->display('admin/modules/details.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>