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

$page = 'export_course_list';
$_user_location = '';

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enroll_admin.php';
$_section[2][0] = _AT('list_export_course_list');
$_section[2][1] = 'tools/export_course_list.php';

$title = _AT('course_enrolment');
$completed = 0;

/*EXPORT LIST OF STUDENTS*/
if(isset($_POST['export'])) {
	//if not list was selected
	if (!$_POST['id']) {
		$errors[] = AT_ERROR_NO_STUDENT_SELECTED;
	}
	//retrieve info from database based on selection (make sure that instructor is not exported!
	else {
		if ($_POST['id'][0] == 'unenrolled') {
			$sql	= "SELECT m.first_name, m.last_name, m.email 
						FROM ".TABLE_PREFIX."course_enrollment cm JOIN ".TABLE_PREFIX."members m ON cm.member_id = m.member_id JOIN ".TABLE_PREFIX."courses c ON (cm.course_id = c.course_id AND cm.member_id <> c.member_id)	WHERE cm.course_id = $_SESSION[course_id] AND approved ='n'	
						ORDER BY m.last_name";
					
		}
		else if ($_POST['id'][0] == 'enrolled' && $_POST['id'][1] == 'unenrolled') {
			$sql	= "SELECT m.first_name, m.last_name, m.email 
						FROM ".TABLE_PREFIX."course_enrollment cm JOIN ".TABLE_PREFIX."members m ON cm.member_id = m.member_id JOIN ".TABLE_PREFIX."courses c ON (cm.course_id = c.course_id AND cm.member_id <> c.member_id)	WHERE cm.course_id = $_SESSION[course_id] ORDER BY m.last_name";
		}
		else {
			$sql	= "SELECT m.first_name, m.last_name, m.email 
						FROM ".TABLE_PREFIX."course_enrollment cm JOIN ".TABLE_PREFIX."members m ON cm.member_id = m.member_id JOIN ".TABLE_PREFIX."courses c ON (cm.course_id = c.course_id AND cm.member_id <> c.member_id)	WHERE cm.course_id = $_SESSION[course_id] AND approved ='y'	
						ORDER BY m.last_name";
		}
		$result =  mysql_query($sql,$db);
		while ($row = mysql_fetch_assoc($result)){
			$this_row .= quote_csv($row['first_name']).",";
			$this_row .= quote_csv($row['last_name']).",";
			$this_row .= quote_csv($row['email'])."\n";
		}

		
		header('Content-Type: text/csv');
		header('Content-transfer-encoding: binary');
		header('Content-Disposition: attachment; filename="course_list_'.$_SESSION['course_id'].'.csv"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		echo $this_row;
		exit;
	}
}
if(isset($_POST['cancel'])) {
	header('Location: enroll_admin.php?f=' . AT_FEEDBACK_CANCELLED);	
	exit;
}

if(isset($_POST['done'])) {
	header('Location: enroll_admin.php?f=' . AT_FEEDBACK_COMPLETED);	
	exit;
}
require(AT_INCLUDE_PATH.'header.inc.php');

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
echo '</h3><br />'."\n";

require(AT_INCLUDE_PATH.'html/feedback.inc.php');

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">
	<div align="left" />
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="70%" summary="" align="center">
		<tr><th class="cyan" scope="col"><?php echo _AT('list_export_course_list'); ?></th></tr>

		<tr>
			<td class="row1" align="left">
				<label>
					<input type="checkbox" name="id[]" value="enrolled" id="enrolled" />
					<?php echo _AT('enrolled_list_includes_assistants'); ?>
				</label>
			</td>
		</tr>
		
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		
		<tr>
			<td class="row1" align="left">
				<label>				
					<input type="checkbox" name="id[]" value="unenrolled" id="unenrolled" />
					<?php echo _AT('unenrolled_list'); ?>
				</label>
			</td>
		</tr>
		
		<tr><td height="1" class="row2" colspan="2"></td></tr>

		<tr><td align="center" scope="col" class="row1">
			<input type="submit" class="button" name="export" value="<?php echo _AT('export'); ?>" /> |
			<input type="submit" class="button" name="cancel" value="<?php echo _AT('cancel'); ?>" /> |
			<input type="submit" class="button" name="done" value="<?php echo _AT('done'); ?>" />
		</tr>
		</table>
		</div>

</form>

<?php 

/**
* Creates csv file to be exported
* @access  private
* @param   string $line		The line ot be converted to csv
* @return  string			The line after conversion to csv
* @author  Shozub Qureshi
*/
function quote_csv($line) {
	$line = str_replace('"', '""', $line);

	$line = str_replace("\n", '\n', $line);
	$line = str_replace("\r", '\r', $line);
	$line = str_replace("\x00", '\0', $line);

	return '"'.$line.'"';
}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>