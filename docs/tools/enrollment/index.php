<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'html/enroll_tab_functions.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enrollment/index.php';

/* make sure we own this course that we're approving for! */
$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db);

if (!($result) || !authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('NOT_OWNER');
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}
$row = mysql_fetch_assoc($result);
$title = $row['title'];
$access = $row['access'];


/* OPTION 1 DELETE/REMOVE */
if (isset($_POST['delete'])) {
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
}


/* OPTION 2 APPROVE ENROLL */
else if (isset($_POST['enroll'])) {
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
}

/* OPTION 3 UNENROLL*/
else if (isset($_POST['unenroll'])) {
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
}

/* OPTION 4 EDIT ROLE */
else if (isset($_POST['role'])) {
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

		header('Location: privileges.php?'.$text.'fcid='.$_SESSION['course_id']);
		exit;
	}
}

/* OPTION 5 MAKE ALUMNI */
else if (isset($_POST['alumni'])) {
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
} 

/* OPTION 6 ADD TO GROUP */
else if (isset($_POST['group_add'])) {
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
}

/* OPTION 7 REMOVE FROM GROUP */
else if (isset($_POST['group_remove'])) {
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

//get sorting order from user input
if ($_GET['col'] && $_GET['order']) {
	$col = $_GET['col'];
	$order = $_GET['order'];
}

//set default sorting order
else {
	$col = "login";
	$order = "asc";
}
$title = _AT('course_enrolment');
require(AT_INCLUDE_PATH.'header.inc.php');
		
echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
} 
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/enrol_mng-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('course_enrolment');
}
echo '</h3>';

$msg->printAll();

if($current_tab == 0){
	$msg->addHelp('ENROLMENT');
	$msg->addHelp('ENROLMENT2');
}else if($current_tab == 1){
	$msg->addHelp('ENROLMENT3');
}else if($current_tab == 2){
	$msg->addHelp('ENROLMENT4');
}else if($current_tab == 3){
	$msg->addHelp('ENROLMENT5');
}else{
	$msg->addHelp('ENROLMENT');
}

$msg->printHelps();
?>

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


<?php
unset($editors);
$editors[] = array('priv' => AT_PRIV_ENROLLMENT, 'title' => _AT('list_export_course_list'), 'url' => 'tools/enrollment/export_course_list.php');
$editors[] = array('priv' => AT_PRIV_ENROLLMENT, 'title' => _AT('list_import_course_list'), 'url' => 'tools/enrollment/import_course_list.php');
$editors[] = array('priv' => AT_PRIV_ENROLLMENT, 'title' => _AT('list_create_course_list'), 'url' => 'tools/enrollment/create_course_list.php');
$editors[] = array('priv' => AT_PRIV_ENROLLMENT, 'title' => _AT('groups'),           'url' => 'tools/enrollment/groups.php');
echo '<div align="center">';
print_editor($editors , $large = false);
echo '</div>';
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">
<input type="hidden" name="form_course_id" value="<?php echo $_SESSION['course_id']; ?>" />

<?php
output_tabs($current_tab);
$cid = $_SESSION['course_id'];

$view_select = intval($_POST['view_select']);
?>
<input type="hidden" name="curr_tab" value="<?php echo $current_tab; ?>" />
<input type="hidden" name="view_select_old" value="<?php echo $view_select; ?>" />

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="90%" summary="" align="center">
	<?php if (!$current_tab): ?>
		<tr>
			<td colspan="5" class="row1">
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
				</select> <input type="submit" name="view" value="<?php echo _AT('view_selected'); ?>" class="button" /></td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="cat" width="20%"  scope="col" align="left">
				<input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" />
				<?php sort_columns('login', $order, $col, $_POST['current_tab']); ?></th>
			<th class="cat" width="20%" scope="col"><?php sort_columns('email',      $order, $col, $_POST['current_tab']); ?></th>
			<th class="cat" width="20%" scope="col"><?php sort_columns('first_name', $order, $col, $_POST['current_tab']); ?></th>
			<th class="cat" width="20%" scope="col"><?php sort_columns('last_name',  $order, $col, $_POST['current_tab']); ?></th>
			<th class="cat" width="20%" scope="col"><?php sort_columns('role',       $order, $col, $_POST['current_tab']); ?></th>
		</tr>

	<?php
		//if viewing list of unenrolled students
		if ($current_tab == 1) {
			$condition = "CE.approved='n'";
			generate_table($condition, $col, $order, 1);
			echo '<input type="submit" class="button" name="enroll" value="'._AT('enroll').'" /> | ';
			echo '<input type="submit" class="button" name="alumni" value="'._AT('mark_alumni').'" /> | ';
			echo '<input type="submit" class="button" name="delete" value="'._AT('remove').'" />';
		}

		//if viewing list of Alumni
		else if ($current_tab == 2) {
			$condition = "CE.approved = 'a'";
			generate_table($condition, $col, $order, 0);
			echo '<input type="submit" class="button" name="enroll"   value="'._AT('enroll').'" /> | ';
			echo '<input type="submit" class="button" name="unenroll" value="'._AT('unenroll').'" />';
		}

		//if veiwing list of enrolled students
		else {
			$condition = "CE.approved='y'";
			generate_table($condition, $col, $order, 'button_1', $view_select);
			echo '<input type="submit" class="button" name="role"     value="'._AT('roles_privileges').'" /> | ';
			echo '<input type="submit" class="button" name="unenroll" value="'._AT('unenroll').'" /> | ';
			echo '<input type="submit" class="button" name="alumni"   value="'._AT('mark_alumni').'" /> | ';

			if ($view_select > 0) {
				echo '<input type="submit" class="button" name="group_remove"   value="'._AT('remove_from_group').'" />';
			} else {
				echo '<select name="group_id"><optgroup label="'._AT('groups').'">'.$groups_options.'</optgroup></select>';
				echo '<input type="submit" class="button" name="group_add" value="'._AT('add_to_group').'" />';
			}
		}
	?></td>
</tr>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>