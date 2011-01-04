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
// $Id: create_automatic.php 10142 2010-08-17 19:17:26Z hwong $

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

	$_POST['type_title']   = trim($_POST['type_title']);
	$_POST['num_students'] = abs($_POST['num_students']);
	$_POST['num_groups']   = abs($_POST['num_groups']);
	$_POST['num_g']        = intval($_POST['num_g']);

	$missing_fields = array();

	if (!$_POST['type_title']) {
		$missing_fields[] = _AT('groups_type');
	}

	if (!$_POST['prefix']) {
		$missing_fields[] = _AT('group_prefix');
	}

	$course_owner = $system_courses[$_SESSION['course_id']]['member_id'];
	if (isset($_POST['fill'])) {
		$sql = "SELECT member_id FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$_SESSION[course_id] AND approved='y' AND `privileges`&".AT_PRIV_GROUPS."=0 AND member_id<>$course_owner";
		$result = mysql_query($sql, $db);
		$total_students = mysql_num_rows($result);
		$students = array();
		while ($row = mysql_fetch_assoc($result)) {
			$students[] = $row['member_id'];
		}
		shuffle($students);
	} else {
		$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$_SESSION[course_id] AND approved='y' AND `privileges`&".AT_PRIV_GROUPS."=0 AND member_id<>$course_owner";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);

		$total_students = $row['cnt']; // 4 students in the course
	}

	if ($_POST['num_g'] == 1) { // number of students per group
		$num_students_per_group = $_POST['num_students'];

		if ($num_students_per_group == 0) {
			$missing_fields[] = _AT('number_of_students_per_group');
		} else {
			if ($total_students == 0) {
				$msg->addError('GROUP_NO_STUDENTS');
			} else {
				$num_groups = ceil($total_students / $num_students_per_group);
			}
		}
	} else { // number of groups
		$num_groups = $_POST['num_groups'];

		if ($num_groups == 0) {
			$missing_fields[] = _AT('number_of_groups');
		} else {
			if ($total_students > 0) {
				// to uniformly distribute all the groups we place the remaining students
				// into the first n groups, where n is the number of remaining students.
				$remainder = $total_students % $num_groups;
				if ($remainder) {
					$num_students_per_group = floor($total_students / $num_groups);
				} else {
					$num_students_per_group = $total_students / $num_groups;
				}
			} else {
				$num_students_per_group = 0;
			}
		}
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$_POST['type_title']  = $addslashes($_POST['type_title']);
		$_POST['prefix']      = $addslashes($_POST['prefix']);
		$_POST['description'] = $addslashes($_POST['description']);

		$sql = "INSERT INTO ".TABLE_PREFIX."groups_types VALUES (NULL, $_SESSION[course_id], '$_POST[type_title]')";
		$result = mysql_query($sql, $db);
		$group_type_id = mysql_insert_id($db);

		$start_index = 0;

		for($i=0; $i<$num_groups; $i++) {
			$group_title = $_POST['prefix'] . ' ' . ($i + 1);
			$sql = "INSERT INTO ".TABLE_PREFIX."groups VALUES (NULL, $group_type_id, '$group_title', '$_POST[description]', '$modules')";
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

			if (isset($_POST['fill'])) {
				// put students in this group
				for ($j = $start_index; $j < min(($start_index + $num_students_per_group), $total_students); $j++) {
					$sql = "INSERT INTO ".TABLE_PREFIX."groups_members VALUES ($group_id, $students[$j])";
					mysql_query($sql, $db);
				}

				$start_index = $j;
				if ($remainder) {
					$sql = "INSERT INTO ".TABLE_PREFIX."groups_members VALUES ($group_id, $students[$start_index])";
					mysql_query($sql, $db);
					$start_index++;
					$remainder--;
				}
			}
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: index.php');
		exit;
	} else {
		$_POST['type_title']  = $stripslashes($_POST['type_title']);
		$_POST['prefix']      = $stripslashes($_POST['prefix']);
		$_POST['description'] = $stripslashes($_POST['description']);
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('groups_create_automatic'); ?></legend>
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="type"><?php echo _AT('groups_type'); ?></label><br />
			<input type="text" name="type_title" id="type" value="<?php echo htmlentities_utf8($_POST['type_title']); ?>" size="30" maxlength="60" />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="prefix"><?php echo _AT('group_prefix'); ?></label><br />
			<input type="text" name="prefix" id="prefix" value="<?php echo htmlentities_utf8($_POST['prefix']); ?>" size="20" maxlength="40" />
		</div>

		<div class="row">
			<label for="description"><?php echo _AT('default_description'); ?></label><br />
			<textarea name="description" id="description" cols="10" rows="2"><?php echo htmlentities_utf8($_POST['description']); ?></textarea>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('number_of_groups'); ?><br />
			<?php
				$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$_SESSION[course_id] AND approved='y' AND `privileges`&".AT_PRIV_GROUPS."=0";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_assoc($result);
			?>
			<p><?php echo _AT('num_students_currently_enrolled', $row['cnt']-1); ?></p>

			<input type="radio" name="num_g" value="1" id="num1" checked="checked" onclick="javascript:changer('num_groups', 'num_students');" /><label for="num1"><?php echo _AT('number_of_students_per_group'); ?></label> <input type="text" name="num_students" size="3" style="text-align: right" maxlength="4" />
			<br />
			<input type="radio" name="num_g" value="2" id="num2" onclick="javascript:changer('num_students', 'num_groups');" /><label for="num2"><?php echo _AT('number_of_groups'); ?></label> <input type="text" name="num_groups" size="3" style="text-align: right" maxlength="4" value="-" />
		</div>

		<div class="row">
			<?php echo _AT('fill_groups'); ?><br />
			<input type="checkbox" name="fill" value="1" id="fill_r" checked="checked" /><label for="fill_r"><?php echo _AT('fill_groups_randomly'); ?></label>
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
<script type="text/javascript">
// <!--
document.form.num_groups.disabled = true;
function changer(name1, name2) {
	document.form[name1].value= '-';
	document.form[name1].disabled = true;
	document.form[name2].disabled = false;

	document.form[name2].value= '';
	document.form[name2].focus();
}
// -->
</script>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>