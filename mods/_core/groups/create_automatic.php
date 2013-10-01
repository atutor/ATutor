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
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
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

		$sql = "SELECT member_id FROM %scourse_enrollment WHERE course_id=%d AND approved='y' AND `privileges`&%d=0 AND member_id<>%d";
		$rows_students = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], AT_PRIV_GROUPS, $course_owner));

		$total_students = count($rows_students);
		$students = array();
		foreach($rows_students as $row){
			$students[] = $row['member_id'];
		}
		shuffle($students);
	} else {

		$sql = "SELECT COUNT(*) AS cnt FROM %scourse_enrollment WHERE course_id=%d AND approved='y' AND `privileges`&%d=0 AND member_id<>%d";
		$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], AT_PRIV_GROUPS, $course_owner), TRUE);

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

		$sql = "INSERT INTO %sgroups_types VALUES (NULL, %d, '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_POST['type_title'] ));
		$group_type_id = at_insert_id();
		
		$start_index = 0;

		for($i=0; $i<$num_groups; $i++) {
			$group_title = $_POST['prefix'] . ' ' . ($i + 1);
			$sql = "INSERT INTO %sgroups VALUES (NULL, %d, '%s', '%s', '%s')";
			$result = queryDB($sql, array(TABLE_PREFIX, $group_type_id, $group_title, $_POST['description'], $modules));
			
			$group_id = at_insert_id();
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
					$sql = "INSERT INTO %sgroups_members VALUES (%d, %d)";
					queryDB($sql, array(TABLE_PREFIX, $group_id, $students[$j]));					
				}

				$start_index = $j;
				if ($remainder) {
					$sql = "INSERT INTO %sgroups_members VALUES (%d, %d)";
					queryDB($sql, array(TABLE_PREFIX, $group_id, $students[$start_index]));
					$start_index++;
					$remainder--;
				}
			}
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
	} else {
		$_POST['type_title']  = $stripslashes($_POST['type_title']);
		$_POST['prefix']      = $stripslashes($_POST['prefix']);
		$_POST['description'] = $stripslashes($_POST['description']);
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT COUNT(*) AS cnt FROM %scourse_enrollment WHERE course_id=%d AND approved='y' AND `privileges`&%d=0";
$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION[course_id],AT_PRIV_GROUPS),TRUE);
?>


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
<?php 
$savant->assign('row', $row);
$savant->display('instructor/groups/create_automatic.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>