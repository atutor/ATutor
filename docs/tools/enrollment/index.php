<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

/* make sure we own this course that we're approving for! */
if (!authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('NOT_OWNER');
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

if (isset($_POST['delete'])) {
	/* OPTION 1 DELETE/REMOVE */

	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}	
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=remove'.SEP.'curr_tab='.$_POST['curr_tab']);
		exit;
	}
} else if (isset($_POST['enroll'])) {
	/* OPTION 2 APPROVE ENROLL */
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}	
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=enroll'.SEP.'curr_tab='.$_POST['curr_tab']);
		exit;
	}
} else if (isset($_POST['unenroll'])) {
	/* OPTION 3 UNENROLL*/

	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=unenroll'.SEP.'curr_tab='.$_POST['curr_tab']);
		exit;	
	}
} else if (isset($_POST['role'])) {
	/* OPTION 4 EDIT ROLE */
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'mid'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: privileges.php?'.$text);
		exit;
	}
} else if (isset($_POST['alumni'])) {
	/* OPTION 5 MAKE ALUMNI */
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=alumni'.SEP.'curr_tab='.$_POST['curr_tab']);
		exit;
	}
} else if (isset($_POST['group_add'])) {
	/* OPTION 6 ADD TO GROUP */
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$group_id = intval($_POST['group_id']);
		if ($group_id && is_array($_POST['id'])) {
			$sql = "INSERT INTO ".TABLE_PREFIX."groups_members VALUES ";
			foreach($_POST['id'] as $student_id) {
				$student_id = intval($student_id);
				$sql .= "($group_id, $student_id),";
			}
			$sql = substr($sql, 0, -1);
			mysql_query($sql, $db);

			$msg->addFeedback('STUDENT_ADDED_GROUP');
			header('Location: index.php');
			exit;
		}
	}
} else if (isset($_POST['group_remove'])) {
	/* OPTION 7 REMOVE FROM GROUP */
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['current_tab'] = $_POST['curr_tab'];
	}
	else {
		$group_id = intval($_POST['view_select_old']);
		if (($group_id >0 ) && is_array($_POST['id'])) {
			$sql = "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=$group_id AND member_id IN ";
			$sql .= '(0,'.implode(',', $_POST['id']).')';
			mysql_query($sql, $db);

			header('Location: index.php');
			exit;
		}
	}
}

$title  = $system_courses[$_SESSION['course_id']]['title'];
$access = $system_courses[$_SESSION['course_id']]['access'];

require(AT_INCLUDE_PATH.'html/enroll_tab_functions.inc.php');
$tabs = get_tabs();	
$num_tabs = count($tabs);

for ($i=0; $i < $num_tabs; $i++) {
	if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) { 
		$current_tab = $i;
		$_POST['current_tab'] = $i;
		break;
	}
}

//get present tab if specified
if ($_GET['current_tab']) {
	$current_tab = $_GET['current_tab'];
	$_POST['current_tab'] = $_GET['current_tab'];
}

if ($_GET['col'] && $_GET['order']) {
	//get sorting order from user input
	$col = $addslashes($_GET['col']);
	$order = $addslashes($_GET['order']);
} else {
	//set default sorting order
	$col = 'login';
	$order = 'asc';
}
$title = _AT('course_enrolment');

if ($current_tab == 0) {
	$msg->addHelp('ENROLMENT');
	$msg->addHelp('ENROLMENT2');
} else if($current_tab == 1) {
	$msg->addHelp('ENROLMENT3');
} else if($current_tab == 2) {
	$msg->addHelp('ENROLMENT4');
} else if($current_tab == 3) {
	$msg->addHelp('ENROLMENT5');
} else {
	$msg->addHelp('ENROLMENT');
}

$view_select = intval($_POST['view_select']);

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">
<?php output_tabs($current_tab); ?>
<input type="hidden" name="curr_tab" value="<?php echo $current_tab; ?>" />
<input type="hidden" name="view_select_old" value="<?php echo $view_select; ?>" />

<table class="data" summary="" rules="cols">
<thead>
	<?php if (!$current_tab): ?>
		<tr>
			<td colspan="5">
				<select name="view_select">
					<option value="0" <?php if ($view_select == 0) { echo 'selected="selected"'; } ?>>- <?php echo _AT('all'); ?> -</option>
					<option value="-1" <?php if ($view_select == -1) { echo 'selected="selected"'; } ?>><?php echo _AT('assistants'); ?></option>
					<optgroup label="<?php echo _AT('groups'); ?>">
						<?php
						$sql    = "SELECT group_id, title FROM ".TABLE_PREFIX."groups WHERE course_id=$_SESSION[course_id] ORDER BY title";
						$result = mysql_query($sql, $db);
						while ($row = mysql_fetch_assoc($result)) {
							$groups_options .= '<option value="'.$row['group_id'].'"';
							 if ($view_select == $row['group_id']) { 
								 $groups_options .= ' selected="selected"'; 
							 }
							$groups_options .= '>'.$row['title'].'</option>';
						}
						echo $groups_options;
						?>
					</optgroup>
				</select>
				<input type="submit" name="view" value="<?php echo _AT('view_selected'); ?>" class="button" />
			</td>
		</tr>
	<?php endif; ?>
	<tr>
		<th scope="col" align="left">
			<input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" />
			<?php sort_columns('login', $order, $col, $_POST['current_tab']); ?></th>
		<th scope="col"><?php sort_columns('email',      $order, $col, $_POST['current_tab']); ?></th>
		<th scope="col"><?php sort_columns('first_name', $order, $col, $_POST['current_tab']); ?></th>
		<th scope="col"><?php sort_columns('last_name',  $order, $col, $_POST['current_tab']); ?></th>
		<th scope="col"><?php sort_columns('role',       $order, $col, $_POST['current_tab']); ?></th>
		<th scope="col"><?php echo _AT('confirmed'); ?></th>
	</tr>
</thead>

	<?php
		$condition = 'CE.member_id<>' . $system_courses[$_SESSION['course_id']]['member_id'];
		echo '<tfoot><tr><td colspan="6">';
		//if viewing list of unenrolled students
		if ($current_tab == 1) {
			echo '<input type="submit" name="enroll" value="'._AT('enroll').'" /> ';
			echo '<input type="submit" name="alumni" value="'._AT('mark_alumni').'" /> ';
			echo '<input type="submit" name="delete" value="'._AT('remove').'" />';
			echo '</td></tr></tfoot>';
			$condition .= " AND CE.approved='n'";
			generate_table($condition, $col, $order, 1);
		}
		//if viewing list of Alumni
		else if ($current_tab == 2) {
			echo '<input type="submit" name="enroll"   value="'._AT('enroll').'" /> ';
			echo '<input type="submit" name="unenroll" value="'._AT('unenroll').'" />';
			echo '</td></tr></tfoot>';
			$condition .= " AND CE.approved = 'a'";
			generate_table($condition, $col, $order, 0);
		} 
		//if veiwing list of enrolled students
		else {
			echo '<input type="submit" name="role" value="'._AT('roles_privileges').'" /> ';
			echo '<input type="submit" name="unenroll" value="'._AT('unenroll').'" /> ';
			echo '<input type="submit" name="alumni" value="'._AT('mark_alumni').'" />';

			if ($view_select > 0) {
				echo '<input type="submit" name="group_remove" value="'._AT('remove_from_group').'" />';
			} else {
				echo '<input type="submit" name="group_add" value="'._AT('add_to_group').'" /> ';
				echo '<select name="group_id"><optgroup label="'._AT('groups').'">'.$groups_options.'</optgroup></select>';
			}
			echo '</td></tr></tfoot>';
			$condition .= " AND CE.approved='y'";
			generate_table($condition, $col, $order, 'button_1', $view_select);
		}
	?>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>

<script language="JavaScript" type="text/javascript">
<!--
function CheckAll() {
	
	for (var i=0;i<document.selectform.elements.length;i++)	{
		var e = document.selectform.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox'))
			e.checked = document.selectform.selectall.checked;
	}
}
-->
</script>