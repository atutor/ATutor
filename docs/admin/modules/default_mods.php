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
// $Id: $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

$module_list =& $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED);
$keys = array_keys($module_list);

if (isset($_POST['submit'])) {
	foreach ($keys as $dir_name) {
		$new_defaults = '';
		$count++;

		$module =& $module_list[$dir_name]; 
		$mod_defaults = $module->getDisplayDefaults();

		if (in_array($module->_student_tools, $_POST['main'])) {
			$main_set = TRUE;
		} else {
			$main_set = FALSE;
		}

		if (in_array($module->_student_tools, $_POST['home'])) {
			$home_set = TRUE;
		} else {
			$home_set = FALSE;
		}

		if ($home_set && $main_set) {
			$new_defaults = AT_MODULE_HOME + AT_MODULE_MAIN;
		} else if ($home_set && !$main_set) {
			$new_defaults = AT_MODULE_HOME;
		} else if (!$home_set && $main_set) {
			$new_defaults = AT_MODULE_MAIN;
		} else {
			$new_defaults = 0;
		}

		if ($new_defaults != $mod_defaults['total']) {
			$sql    = "UPDATE ".TABLE_PREFIX."modules SET display_defaults=$new_defaults WHERE dir_name='".$module->_directoryName."'";
			$result = mysql_query($sql, $db);
		}
	}

	$msg->addFeedback('SECTIONS_SAVED');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$count = 0;
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data static" rules="rows" summary="" style="width:60%;">
<thead>
<tr>
	<th scope="cols"><?php echo _AT('section'); ?></th>
	<th><?php echo _AT('location'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3"><input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /></td>
</tr>
</tfoot>
<tbody>
<?php 
foreach ($keys as $dir_name) :
	$count++;

	$module =& $module_list[$dir_name]; 
	$mod_defaults = $module->getDisplayDefaults();
	
	if (!empty($mod_defaults)):
	?>
	<?php// if ((!AC_PATH) && ($_pages[$module]['title_var'] == 'acollab') || !isset($_pages[$module])): ?>
	<?php //else: ?>

	<tr>
		<td><?php 
			if (isset($_pages[$mod_defaults['student_tool']]['title'])) {
				echo $_pages[$mod_defaults['student_tool']]['title'];
			} else {
				echo _AT($_pages[$mod_defaults['student_tool']]['title_var']);
			} ?></td>
		<td>
			<input type="checkbox" name="main[]" value="<?php echo $mod_defaults['student_tool']; ?>" id="m<?php echo $count; ?>" <?php if ($mod_defaults['main']) { echo 'checked="checked"'; } ?> /><label for="m<?php echo $count; ?>"><?php echo _AT('main_navigation'); ?></label>

			<input type="checkbox" name="home[]" value="<?php echo $mod_defaults['student_tool']; ?>" id="h<?php echo $count; ?>" <?php if ($mod_defaults['home']) { echo 'checked="checked"'; } ?> /><label for="h<?php echo $count; ?>"><?php echo _AT('home'); ?></label>

		</td>
	</tr>
	<?php //endif; ?>
<?php endif; ?>
<?php endforeach; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>