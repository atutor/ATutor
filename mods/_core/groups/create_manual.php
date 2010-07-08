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
// $Id: create_manual.php 7482 2008-05-06 17:44:49Z greg $

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

	$_POST['type']   = abs($_POST['type']);
	$_POST['prefix'] = trim($_POST['prefix']);
	$_POST['new_type'] = trim($_POST['new_type']);

	$missing_fields = array();

	if (!$_POST['type'] && !$_POST['new_type']) {
		$missing_fields[] = _AT('groups_type');
	}
	if (!$_POST['prefix']) {
		$missing_fields[] = _AT('title');
	}
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$_POST['new_type'] = $addslashes($_POST['new_type']);
		$_POST['prefix']      = $addslashes($_POST['prefix']);
		$_POST['description'] = $addslashes($_POST['description']);

		if ($_POST['new_type']) {
			$sql = "INSERT INTO ".TABLE_PREFIX."groups_types VALUES (NULL, $_SESSION[course_id], '$_POST[new_type]')";
			$result = mysql_query($sql, $db);
			$type_id = mysql_insert_id($db);
		} else {
			$sql = "SELECT type_id FROM ".TABLE_PREFIX."groups_types WHERE course_id=$_SESSION[course_id] AND type_id=$_POST[type]";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				$type_id = $row['type_id'];
			} else {
				$type_id = FALSE;
			}
		}
		if ($type_id) {
			$sql = "INSERT INTO ".TABLE_PREFIX."groups VALUES (NULL, $type_id, '$_POST[prefix]', '$_POST[description]', '$modules')";
			$result = mysql_query($sql, $db);

			$group_id = mysql_insert_id($db);

			$_SESSION['groups'][$group_id] = $group_id;
			// call module init scripts:
			if (isset($_POST['modules'])) {
				foreach ($_POST['modules'] as $mod) {
					$module =& $moduleFactory->getModule($mod);
					$module->createGroup($group_id);
				}
			}
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: index.php');
		exit;
	} else {
		$_POST['new_type']    = $stripslashes($_POST['new_type']);
		$_POST['prefix']      = $stripslashes($_POST['prefix']);
		$_POST['description'] = $stripslashes($_POST['description']);
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$types = array();
$sql = "SELECT type_id, title FROM ".TABLE_PREFIX."groups_types WHERE course_id=$_SESSION[course_id] ORDER BY title";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$types[$row['type_id']] = htmlentities_utf8($row['title']);
}

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('groups_create_manual'); ?></legend>
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="type"><?php echo _AT('groups_type'); ?></label><br />
			<?php if ($types): ?>
				<?php echo _AT('existing_type'); ?>
				<select name="type" id="type">
				<?php foreach ($types as $type_id => $type_title): ?>
					<option value="<?php echo $type_id; ?>"><?php echo $type_title; ?></option>
				<?php endforeach; ?>
				</select>
				<em><?php echo _AT('or'); ?></em>
			<?php endif; ?>
			<label for="new"><?php echo _AT('new_type'); ?></label> <input type="text" name="new_type" value="<?php echo htmlentities_utf8($_POST['new_type']); ?>" id="new" size="30" maxlength="40" />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="prefix"><?php echo _AT('title'); ?></label><br />
			<input type="text" name="prefix" id="prefix" value="<?php echo htmlentities_utf8($_POST['prefix']); ?>" size="20" maxlength="40" />
		</div>

		<div class="row">
			<label for="description"><?php echo _AT('description'); ?></label><br />
			<textarea name="description" id="description" cols="10" rows="2"><?php echo htmlentities_utf8($_POST['description']); ?></textarea>
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
						<input type="checkbox" value="<?php echo $module_name; ?>" name="modules[]" id="m<?php echo ++$i; ?>" /><label for="m<?php echo $i; ?>"><?php echo $module->getName(); ?></label><br />
					<?php endif; ?>
				<?php endforeach; ?>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('create'); ?>" accesskey="s" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</div>
		</fieldset>
	</div>
</form>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>