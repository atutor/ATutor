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
$page = 'enroll_admin';
$_user_location = '';

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'html/enroll_tab_functions.inc.php');


$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enroll_admin.php';


/* make sure we own this course that we're approving for! */
$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db);

if (!($result) || !authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$errors[] = AT_ERROR_NOT_OWNER;
	print_errors($errors);
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}
$row = mysql_fetch_assoc($result);
$title = $row['title'];
$access = $row['access'];


$tabs = get_tabs();	
$num_tabs = count($tabs);

for ($i=0; $i < $num_tabs; $i++) {
	if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) { 
		$current_tab = $i;
		$_POST[current_tab] = $i;
		break;
	}
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

/* OPTION 1 DELETE/REMOVE */
if (isset($_POST['delete'])) {
	if (!$_POST['id']) 	{
		$errors[] = AT_ERROR_NO_STUDENT_SELECTED;
	}	
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=remove');
		exit;
	}
}


/* OPTION 2 APPROVE ENROLL */
else if (isset($_POST['enroll'])) {
	if (!$_POST['id']) 	{
		$errors[] = AT_ERROR_NO_STUDENT_SELECTED;
	}	
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=enroll');
		exit;
	}
}

/* OPTION 3 UNENROLL*/
else if (isset($_POST['unenroll'])) {
	if (!$_POST['id']) 	{
		$errors[] = AT_ERROR_NO_STUDENT_SELECTED;
	}
	else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=unenroll');
		exit;	
	}
}

/* OPTION 4 EDIT ROLE */
else if (isset($_POST['role'])) {
	if (!$_POST['id']) 	{
		$errors[] = AT_ERROR_NO_STUDENT_SELECTED;
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

$title = _AT('course_enrolment');
require(AT_INCLUDE_PATH.'header.inc.php');

print_feedback ($feedback);
//print_errors($errors);

/* we own this course! */
$help[]=AT_HELP_ENROLMENT;
$help[]=AT_HELP_ENROLMENT2;

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

require(AT_INCLUDE_PATH.'html/feedback.inc.php');


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


<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">
<input type="hidden" name="form_course_id" value="<?php echo $_SESSION['course_id']; ?>" />
	<p align ="center"><strong> 
		<a href="tools/export_course_list.php"> <?php echo _AT('list_export_course_list');  ?></a> | 
		<a href="tools/import_course_list.php"> <?php echo _AT('list_import_course_list');  ?></a> | 
		<a href="tools/create_course_list.php"> <?php echo _AT('list_create_course_list');  ?></a>
	</strong></p>
	
<?php
output_tabs($current_tab);
$cid = $_SESSION['course_id'];
?>

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="90%" summary="" align="center">
		<tr>
			<th class="cat" width="20%"  scope="col" align="left">
				<input type="checkbox" value="SelectAll" id="all" title="select/unselect all" name="selectall" onclick="CheckAll();" />
				<?php
					sort_columns('login', $order, $col);
				?>
			</th>
			<th class="cat" width="20%" scope="col"><?php
					sort_columns('email', $order, $col);
				?>
			</th>
			<th class="cat" width="20%" scope="col"><?php
					sort_columns('first_name', $order, $col);
				?>
			</th>
			<th class="cat" width="20%" scope="col"><?php
					sort_columns('last_name', $order, $col);
				?>
			</th>
			<th class="cat" width="20%" scope="col"><?php
					sort_columns('role', $order, $col);
				?>
			</th>
		</tr>

	<?php
		//if viewing list of unenrollded students
		if (isset($_POST['button_1']) && ($_POST['button_1'] != -1)) {
			$condition = "cm.approved = 'n'";
			generate_table($condition, $col, $order, $cid, 1);
			echo '<input type="submit" class="button" title="Cannot edit Roles od unenrolled students" name="role" disabled="disabled" value="'._AT('roles_privileges').'" /> | ';
			echo '<input type="submit" class="button" name="enroll" value="'._AT('enroll').'" /> | ';
			echo '<input type="submit" class="button" name="delete"   value="'._AT('remove').'" />';
		}

		//if viewing list of Assistants
		else if (isset($_POST['button_2']) && ($_POST['button_2'] != -1)) { 
			$condition = "cm.privileges <> 0";
			generate_table($condition, $col, $order, $cid, 0);
			echo '<input type="submit" class="button" name="role"   value="'._AT('roles_privileges').'" />';
		}

		//if veiwing list of enrolled students
		else {
			$condition = "cm.approved = 'y'";
			generate_table($condition, $col, $order, $cid, 0);
			echo '<input type="submit" class="button" name="role"     value="'._AT('roles_privileges').'" /> | ';
			echo '<input type="submit" class="button" name="unenroll" value="'._AT('unenroll').'" /> | ';
			echo '<input type="submit" class="button" name="delete"   value="'._AT('remove').'" />';

		}
		echo '</td></tr>';
		echo '</table>';

	?>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>