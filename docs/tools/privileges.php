<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$page = 'enrollment';
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enroll_admin.php';
$_section[2][0] = _AT('roles_privileges');


$course = $_SESSION['course_id'];
$mid = intval($_REQUEST['mid']);
$title = _AT('course_enrolment');

$num_cols = 2;

if (isset($_POST['cancel'])) {
	header('Location: enroll_admin.php?course='.$course.SEP.'f='.AT_FEEDBACK_CANCELLED);
	exit;
}

if (isset($_POST['submit'])) {

	if ($_POST['role'] == '') {
		$errors[] = AT_ERROR_TITLE_EMPTY;

	} else {
		$privilege = 0;
		foreach ($_POST['privs'] as $key => $priv) {	
			$privilege += $key;
		}	
		$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET `privileges`=$privilege, role='".$_POST['role']."' WHERE member_id=$mid AND course_id=$course";
		$result = mysql_query($sql,$db);
		if (!$result) {
			$errors[]=AT_ERROR_DB_NOT_UPDATED;
			print_errors($errors);
			exit;
		} 

		header('Location: enroll_admin.php?course='.$course.SEP.'f='.AT_FEEDBACK_PRIVS_CHANGED);
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

print_errors($errors);

$sql = "SELECT * FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND M.member_id=$mid";
$result = mysql_query($sql,$db);

if ($row = mysql_fetch_array($result)) {
	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
	}
	echo '</h2>'."\n";

	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/enrol_mng-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/enroll_admin.php?course='.$_SESSION['course_id'].'">'._AT('course_enrolment').'</a>';
	}
	echo '</h3>'."\n";
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="course" value="<?php echo $course; ?>" />
<input type="hidden" name="mid" value="<?php echo $mid; ?>" />

	<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
	<tr><th><?php echo _AT('roles_privileges');  ?></th></tr>

	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><label for="role"><strong><?php echo _AT('user_role'); ?>:</strong></label> <input type="input" name="role" id="role" class="formfield" value="<?php if ($row['role'] !='') { echo $row['role']; } else { echo _AT('student'); } ?>" size="35" />
		</td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><label for="course_list"><strong><?php echo _AT('user_privileges'); ?>:</strong></label><br />
			<table width="100%" border="0" cellspacing="5" cellpadding="0" summary="">
			<tr>
			<?php		
			$count =0;

			foreach ($_privs as $key => $priv) {		
				$count++;
				echo '<td><input type="checkbox" name="privs['.$key.']" id="'.$key.'" ';

				if (query_bit($row['privileges'], $key)) { 
					echo 'checked="checked"';
				} 

				echo ' /><label for="'.$key.'">'.$priv['name'].'</label></td>'."\n";
				if (!($count % $num_cols)) {
					echo '</tr><tr>';
				}
			}
			if ($count % $num_cols) {
				echo '<td colspan="'.($num_cols-($count % $num_cols)).'">&nbsp;</td>';
			} else {
				echo '<td colspan="'.$num_cols.'">&nbsp;</td>';
			}
			echo '</tr>';
			?>
			</tr></table><br /></td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1" align="center"><input type="submit" name="submit" value="<?php echo _AT('save_changes');  ?>" class="button" /> <input type="submit" name="cancel" value="<?php echo _AT('cancel');  ?>" class="button" /></td>
	</tr>
	</table>
</form>

<?php
} else {
	//not enrolled
	// generate some kind of error?
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
