<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GROUPS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$modules = '';
	if (isset($_POST['modules'])) {
		$modules = implode('|', $_POST['modules']);
	}

	$_POST['title']   = trim($_POST['title']);

	if (!$_POST['title']) {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}

	if (!$msg->containsErrors()) {
		$_POST['title']       = htmlspecialchars($_POST['title'], ENT_QUOTES);
		$_POST['description'] = htmlspecialchars($_POST['description']);

		$id = intval($_POST['id']);
		$type_id = intval($_POST['type_id']);

		$sql = "SELECT type_id FROM %sgroups_types WHERE type_id=%d AND course_id=%d";
		$rows_group_types = queryDB($sql, array(TABLE_PREFIX, $type_id, $_SESSION['course_id']));
		
		if(count($rows_group_types) > 0){

			$sql = "UPDATE %sgroups SET title='%s', description='%s', modules='%s' WHERE group_id=%d AND type_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $_POST['title'], $_POST['description'], $modules, $id, $type_id));
			
			// delete the modules that were un-checked
			$old_modules = explode('|', $_POST['old_modules']);
			$modules = explode('|', $modules);

			foreach ($old_modules as $mod) {
				if (!in_array($mod, $modules)) {
					$module =& $moduleFactory->getModule($mod);
					$module->deleteGroup($id);
				}
			}
			foreach ($modules as $mod) {
				if (!in_array($mod, $old_modules)) {
					$module =& $moduleFactory->getModule($mod);
					$module->createGroup($id);
				}
			}
		}

		$msg->addFeedback('GROUP_EDITED_SUCCESSFULLY');

		header('Location: index.php');
		exit;
	}
	$_GET['id'] = abs($_POST['id']);
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['id'] = intval($_GET['id']);

	$sql = "SELECT * FROM %sgroups WHERE group_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $_GET['id']), TRUE);	
	
	if(count($row) == 0){
		$msg->printErrors('GROUP_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$sql = "SELECT title FROM %sgroups_types WHERE type_id=%d AND course_id=%d";
	$type_row = queryDB($sql, array(TABLE_PREFIX, $row['type_id'], $_SESSION['course_id']), TRUE);

	if(count($type_row) == 0){
		$msg->printErrors('GROUP_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
$old_modules = $row['modules'];
$row['modules'] = explode('|', $row['modules']);
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="id" value="<?php echo $row['group_id']; ?>" />
<input type="hidden" name="type_id" value="<?php echo $row['type_id']; ?>" />
<input type="hidden" name="old_modules" value="<?php echo $old_modules; ?>" />
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit'); ?></legend>
	<div class="row">
		<h3><?php echo AT_print($type_row['title'], 'groups.title'); ?></h3>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" value="<?php echo AT_print($row['title'], 'groups.title'); ?>" size="20" maxlength="40" />
	</div>

	<div class="row">
		<label for="description"><?php echo _AT('description'); ?>:</label><br />
		<textarea name="description" id="description" cols="10" rows="2"><?php echo AT_print($row['description'], 'groups.description'); ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('tools'); ?><br />
			<?php
			$modules = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
			$keys = array_keys($modules);
			$i=0;
			?>
			<?php foreach($keys as $module_name): ?>
				<?php $module =& $modules[$module_name]; ?>
				<?php if ($module->getGroupTool() && (in_array($module->getGroupTool(),$_pages[AT_NAV_HOME]) || in_array($module->getGroupTool(),$_pages[AT_NAV_COURSE])) ): ?>
					<input type="checkbox" value="<?php echo $module_name; ?>" name="modules[]" id="m<?php echo ++$i; ?>" <?php 
						if (in_array($module_name, $row['modules'])) { echo 'checked="checked"'; } 
					?> /><label for="m<?php echo $i; ?>"><?php echo $module->getName(); ?></label><br />
				<?php endif; ?>
			<?php endforeach; ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>