<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
		$msg->addError('NO_TITLE');
	}

	if (!$msg->containsErrors()) {
		$_POST['title']       = $addslashes($_POST['title']);
		$_POST['description'] = $addslashes($_POST['description']);

		$id = intval($_POST['id']);
		$type_id = intval($_POST['type_id']);

		$sql = "SELECT type_id FROM ".TABLE_PREFIX."groups_types WHERE type_id=$type_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)) {
			$sql = "UPDATE ".TABLE_PREFIX."groups SET title='$_POST[title]', description='$_POST[description]', modules='$modules' WHERE group_id=$id AND type_id=$type_id";
			$result = mysql_query($sql, $db);

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

	$sql = "SELECT * FROM ".TABLE_PREFIX."groups WHERE group_id=$_GET[id]";
	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_assoc($result))) {
		$msg->printErrors('GROUP_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$sql = "SELECT title FROM ".TABLE_PREFIX."groups_types WHERE type_id=$row[type_id] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql,$db);
	if (!($type_row = mysql_fetch_assoc($result))) {
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
	<div class="row">
		<h3><?php echo $type_row['title']; ?></h3>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" value="<?php echo $row['title']; ?>" size="20" maxlength="40" />
	</div>

	<div class="row">
		<label for="description"><?php echo _AT('description'); ?>:</label><br />
		<textarea name="description" cols="10" rows="2"><?php echo $row['description']; ?></textarea>
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
				<?php if ($module->getGroupTool()): ?>
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
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>