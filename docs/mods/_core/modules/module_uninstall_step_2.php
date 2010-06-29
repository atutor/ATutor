<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : "";
$args = isset($_REQUEST['args']) ? $_REQUEST['args'] : "";
$permission_granted = isset($_REQUEST['permission_granted']) ? $_REQUEST['permission_granted'] : "";

if (isset($_POST['submit_no']))
{
	// if write permission on the mods folder has been granted, re-direct to the page of removing permission,
	// otherwise, back to start page.
	if ($_POST['permission_granted']==1)
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/module_uninstall_step_3.php?mod='.$_POST['mod'].SEP.'cancelled=1'.SEP.'args='.urlencode($_POST['args']));
	else
	{
		$msg->addFeedback('CANCELLED');
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/index.php?'.urlencode($_POST["args"]));
	}
	
	exit;
} 
else if (isset($_POST['submit_yes'], $_POST['mod'])) 
{
	$module = $moduleFactory->getModule($_POST['mod']);
	$module->uninstall($_POST['del_data']);

	if ($_POST['permission_granted']==1)
	{
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/module_uninstall_step_3.php?uninstalled=1'.SEP.'args='.urlencode($_POST["args"]));
	}
	else
	{
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/index.php?'.$_POST['args']);
	}

	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="mod" value="<?php echo $mod; ?>" />
<input type="hidden" name="args" value="<?php echo $args; ?>" />
<input type="hidden" name="permission_granted" value="<?php echo $permission_granted; ?>" />

<div class="input-form">
	<div class="row">
		<?php echo _AT('uninstall_module_info', $mod); ?><br />
		<input type="checkbox" name="del_data" value="1" id="del_data" checked="checked" /><label for="del_data"><?php echo _AT('delete_module_data'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit_yes" value="<?php echo _AT('submit_yes'); ?>" /> 
		<input type="submit" name="submit_no" value="<?php echo _AT('submit_no'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>