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

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'lib/atutor_mail.inc.php');

$course = intval($_REQUEST['course']);
$mid = intval($_REQUEST['mid']);
$title = _AT('course_enrolment');

$num_cols = 2;

$privs[AT_PRIV_CONTENT]			= _AT('priv_manage_content');
$privs[AT_PRIV_GLOSSARY]		= _AT('priv_manage_glossary');
$privs[AT_PRIV_TEST_CREATE]		= _AT('priv_create_tests');
$privs[AT_PRIV_TEST_MARK]		= _AT('priv_mark_tests');
$privs[AT_PRIV_FILES]			= _AT('priv_files');
$privs[AT_PRIV_LINKS]			= _AT('priv_links');
$privs[AT_PRIV_FORUMS]			= _AT('priv_forums');
$privs[AT_PRIV_STYLES]			= _AT('priv_styles');
$privs[AT_PRIV_ENROLLMENT]		= _AT('priv_enrollment');
$privs[AT_PRIV_COURSE_EMAIL]	= _AT('priv_course_email');
$privs[AT_PRIV_ANNOUNCEMENTS]	= _AT('priv_announcements');

asort($privs);
reset($privs);

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

require(AT_INCLUDE_PATH.'cc_html/header.inc.php');

print_errors($errors);

$sql = "SELECT * FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND M.member_id=$mid";
$result = mysql_query($sql,$db);

if ($row = mysql_fetch_array($result)) {
?>
<a name="content"></a>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="course" value="<?php echo $course; ?>" />
<input type="hidden" name="mid" value="<?php echo $mid; ?>" />

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
	<tr><th class="cyan"><?php echo _AT('roles_privileges');  ?></th></tr>

	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><label for="role"><strong><?php echo _AT('user_role'); ?>:</strong></label> <input type="input" name="role" id="role" class="formfield" value="<?php echo $row['role']; ?>" size="35" />
		</td>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr>
		<td class="row1"><label for="course_list"><strong><?php echo _AT('user_privileges'); ?>:</strong></label><br />
			<table width="100%" border="0" cellspacing="5" cellpadding="0" summary="">
			<tr>
			<?php		
			$count =0;
			foreach ($privs as $key => $priv) {				
				$count++;
				echo '<td><input type="checkbox" name="privs['.$key.']" id="'.$key.'" ';

				if (query_bit($row['privileges'], $key)) { 
					echo 'checked="checked"';
				} 

				echo ' /><label for="'.$key.'">'.$priv.'</label></td>';
				if (!($count % $num_cols)) {
					echo '</tr><tr>';
				}
			}
			if ($count % $num_cols) {
				echo '<td colspan="'.($num_cols-($count % $num_cols)).'">&nbsp;</td>';
			} else {
				echo '<td colspan="'.$num_cols.'">&nbsp;</td>';
			}
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

require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
?>