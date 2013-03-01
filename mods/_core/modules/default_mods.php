<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH . '../mods/_core/modules/classes/ModuleUtility.class.php');
admin_authenticate(AT_ADMIN_PRIV_MODULES);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ../courses.php');
	exit;
}

ModuleUtility::set_default_tools($_POST['up'], $_POST['down'], $_POST['main'], $_POST['home'], $_POST['submit']);
$main_defaults[] = ModuleUtility::get_main_defaults();
$home_defaults[] = ModuleUtility::get_home_defaults();

if(isset($_POST['submit'])) {
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$main_defaults = explode('|', $_config['main_defaults']);
$home_defaults = explode('|', $_config['home_defaults']);

$main_defaults = array_filter($main_defaults); // remove empties
$home_defaults = array_filter($home_defaults); // remove empties
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data static" rules="rows" summary="">
<thead>
<tr>
	<th scope="cols"><?php echo _AT('section'); ?></th>
	<th><?php echo _AT('location'); ?></th>
	<th><?php echo _AT('order'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3" style="text-align:right;">		
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s"  />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />	
	</td>
</tr>
</tfoot>
<tbody>
<?php 
$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED);
$keys = array_keys($module_list);

foreach ($keys as $dir_name) {
	$module =& $module_list[$dir_name]; 

	if ($module->getStudentTools()) {
		$student_tools[] = $module->getStudentTools();
	}
}

$count = 0;

//main mods
$_current_modules = $main_defaults;
$num_main    = count($_current_modules);
//main and home merged
$_current_modules = array_merge($_current_modules, array_diff($home_defaults, $main_defaults));
$num_modules = count($_current_modules);
//all other mods
$_current_modules = array_merge($_current_modules, array_diff($student_tools, $_current_modules));


foreach ($_current_modules as $tool) :
	$count++; 
?>
	<tr>
		<td><?php 
		if (isset($_pages[$tool]['title'])) {
			echo $_pages[$tool]['title'];
		} else {
			echo _AT($_pages[$tool]['title_var']);
		} ?></td>
		<td align="center">
			<?php if (in_array($tool, $main_defaults)): ?>
				<input type="checkbox" name="main[]" value="<?php echo $tool; ?>" id="m<?php echo $tool; ?>" checked="checked" /><label for="m<?php echo $tool; ?>"><?php echo _AT('main_navigation'); ?></label>
			<?php else: ?>
				<input type="checkbox" name="main[]" value="<?php echo $tool; ?>" id="m<?php echo $tool; ?>" /><label for="m<?php echo $tool; ?>"><?php echo _AT('main_navigation'); ?></label>
			<?php endif; ?>

			<?php if (in_array($tool, $home_defaults)): ?>
				<input type="checkbox" name="home[]" value="<?php echo $tool; ?>" id="h<?php echo $tool; ?>" checked="checked" /><label for="h<?php echo $tool; ?>"><?php echo _AT('home'); ?></label>
			<?php else: ?>
				<input type="checkbox" name="home[]" value="<?php echo $tool; ?>" id="h<?php echo $tool; ?>" /><label for="h<?php echo $tool; ?>"><?php echo _AT('home'); ?></label>
			<?php endif; ?>
		</td>
		<td align="right">
			<?php if (!in_array($tool, $home_defaults) && !in_array($tool, $main_defaults)): ?>
				&nbsp;
			<?php else: ?>
				<?php if (($count != $num_main+1) && ($count > 1)): ?>
					<input type="submit" name="up[<?php echo $tool; ?>]" value="<?php echo _AT('move_up'); ?>" title="<?php echo _AT('move_up'); ?>" style="background-color: white; border: 1px solid; padding: 0px;" />
				<?php else: ?>
					<img src="images/clr.gif" alt="" width="12" />
				<?php endif; ?>
				<?php if (($count != $num_main) && ($count < $num_modules)): ?>
					<input type="submit" name="down[<?php echo $tool; ?>]" value="<?php echo _AT('move_down'); ?>" title="<?php echo _AT('move_down'); ?>" style="background-color: white; border: 1px solid; padding: 0px;"/>
				<?php else: ?>
					<img src="images/clr.gif" alt="" width="12" />
				<?php endif; ?>
			<?php endif; ?>
		</td>
	</tr>
<?php 
endforeach; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>