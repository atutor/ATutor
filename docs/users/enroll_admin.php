<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'lib/atutor_mail.inc.php');

$course = intval($_GET['course']);

if (!$course) {
	$course = intval($_POST['form_course_id']);
}


/* make sure we own this course that we're approving for! */
$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$course AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db);
if (mysql_num_rows($result) != 1) {
	require(AT_INCLUDE_PATH.'cc_html/header.inc.php');
	$errors[] = AT_ERROR_NOT_OWNER;
	print_errors($errors);
	require (AT_INCLUDE_PATH.'cc_html/footer.inc.php'); 
	exit;
}

if($_GET['export_enrollment'] && !$no_students){
	$row = mysql_fetch_assoc($result);
	$title = $row['title'];

	$sql5 = "SELECT member_id from ".TABLE_PREFIX."course_enrollment where course_id = $course";
	$result5 =  mysql_query($sql5,$db);
	$enrolled = array();
	while($row = mysql_fetch_array($result5)){
		$sql1 = "SELECT * from ".TABLE_PREFIX."members where member_id = $row[member_id]";
		//echo $sql1;
		$result1 = mysql_query($sql1,$db);
		while($row2 = mysql_fetch_array($result1)){
			if($row2['member_id'] != $_SESSION['member_id']){
				$this_row .= quote_csv($row2['first_name']).",";
				$this_row .= quote_csv($row2['last_name']).",";
				$this_row .= quote_csv($row2['email'])."\n";
			}
		}
	}

	if (!@opendir('../content/export/')){
		mkdir('../content/export/', 0777);
	}

	$fp = @fopen('../content/export/course_list_'.$course.'.csv', 'w');
	if (!$fp) {
		require(AT_INCLUDE_PATH.'cc_html/header.inc.php');
		$errors[] = array(AT_ERROR_CSV_FAILED, $title);
		print_errors($errors);
		require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
		exit;
	}
	@fputs($fp, $this_row);
	@fclose($fp);

	header('Content-Type: text/csv');
	header('Content-transfer-encoding: binary');
	header('Content-Disposition: attachment; filename="course_list_'.$course.'.csv"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	@readfile('../content/export/course_list_'.$course.'.csv');

	@unlink('../content/export/course_list_'.$course.'.csv');
	exit;

}
if ($_POST['submit'])
{
	$_POST['form_course_id'] = intval($_POST['form_course_id']);

	if (is_array($_POST['id'])) {
		$members = '(member_id='.$_POST['id'][0].')';
		for ($i=1; $i < count($_POST['id']); $i++) {
			$members .= ' OR (member_id='.$_POST['id'][$i].')';
		}
		$sql	= "UPDATE ".TABLE_PREFIX."course_enrollment SET approved='y' WHERE course_id=$_POST[form_course_id] AND ($members)";
		$result = mysql_query($sql, $db);

		// notify the users that they have been approved:
		$sql	= "SELECT email, first_name, last_name, login, password FROM ".TABLE_PREFIX."members WHERE $members";
		$result = mysql_query($sql, $db);

		while ($row = mysql_fetch_array($result)) {
			/* assumes that there is a first and last name for this user, but not required during registration */
			$to_email = $row['email'];

			$message  = $row['first_name'].' '.$row['last_name'].'( '._AT('login').':'.$row['login'].' - '._AT('password').':'. $row['password'].")\n";
			//this isn't right 
			//$message .= _AT('enrol_message1').' "'.$system_courses[$_POST['form_course_id']]['title'].'" '._AT('enrol_message2').' '.$_base_href.' '._AT('enrol_message3');
			$message .= _AT('enrol_message_approved', $system_courses[$_POST['form_course_id']]['title'], $_base_href);
			// name, your enrolment has been approved for course: course_name. Go to $_base_href to login.

			if ($to_email != '') {
				atutor_mail($to_email, _AT('enrol_message_subject'), $message, ADMIN_EMAIL);
			}
		}
	}

	if (is_array($_POST['nid'])) {
		$members = '(member_id='.$_POST['nid'][0].')';
		for ($i=1; $i < count($_POST['nid']); $i++)
		{
			$members .= ' OR (member_id='.$_POST['nid'][$i].')';
		}
		$sql	= "UPDATE ".TABLE_PREFIX."course_enrollment SET approved='n' WHERE course_id=$_POST[form_course_id] AND ($members)";
		$result = mysql_query($sql, $db);
	}

	if (is_array($_POST['rid'])) {
		$members = '(member_id='.$_POST['rid'][0].')';
		for ($i=1; $i < count($_POST['rid']); $i++)
		{
			$members .= ' OR (member_id='.$_POST['rid'][$i].')';
		}
		$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$_POST[form_course_id] AND ($members)";
		$result = mysql_query($sql, $db);
	}

	Header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_ENROLMENT_UPDATED));
	exit;
}

$title = _AT('course_enrolment');
require(AT_INCLUDE_PATH.'cc_html/header.inc.php');

/* we own this course! */
$help[]=AT_HELP_ENROLMENT;
$help[]=AT_HELP_ENROLMENT2;

?>	<script language="JavaScript" type="text/javascript">
	<!--
	function CheckAll()
	{
		for (var i=0;i<document.selectform.elements.length;i++)
		{
			var e = document.selectform.elements[i];
			if ((e.name == 'id[]') && (e.type=='checkbox'))
			e.checked = document.selectform.selectall.checked;
		}
	}
	function CheckCheckAll()
	{
		var TotalBoxes = 0;
		var TotalOn = 0;
		for (var i=0;i<document.selectform.elements.length;i++)
		{
			var e = document.selectform.elements[i];
			if ((e.name != 'selectall') && (e.type=='checkbox'))
			{
				TotalBoxes++;
			if (e.checked)
			{
				TotalOn++;
			}
			}
		}
		if (TotalBoxes==TotalOn)
		{document.selectform.selectall.checked=true;}
		else
		{document.selectform.selectall.checked=false;}
	}
	-->
	</script>
<form method="post" action="<?php echo $PHP_SELF; ?>" name="selectform">
<input type="hidden" name="form_course_id" value="<?php echo $course; ?>" />
<p><a href="users/import_course_list.php?course=<?php echo $course; ?>"> <?php echo _AT(list_import_course_list)  ?></a> | <a href="<?php echo $PHP_SELF; ?>?export_enrollment=1<?php echo SEP; ?>course=<?php echo $_GET['course']; ?>"><?php echo _AT(list_export_course_list)  ?></a> </p>
<?php
	// note: doesn't list the owner of the course.
	$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND M.member_id<>$_SESSION[member_id] ORDER BY C.approved, M.login";
	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_array($result))) {
		$infos[]=AT_INFOS_NO_ENROLLMENTS;
		print_infos($infos);
		$no_students =1;
		//echo 'No users found.';
	} else {
		print_help($help);
		echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%" align="center">';
		echo '<tr><th class="cyan" colspan="5">'._AT('students').'</th></tr>';

		echo '<tr><th class="cat" scope="col">'._AT('login_id').'</th><th class="cat" scope="col">'._AT('enrolment').'</th><th class="cat" scope="col"><input type="checkbox" value="SelectAll" id="all" title="select/unselect all" name="selectall" onclick="CheckAll();"/>'._AT('approve').'</th><th class="cat" scope="col">'._AT('disapprove').'</th><th class="cat" scope="col">'._AT('remove').'</th></tr>';

		do {
			echo '<tr><td class="row1"><tt><a href="users/view_profile.php?mid='.$row['member_id'].SEP.'course='.$_GET['course'].'">'.$row['login'].' ('.$row['member_id'].')</a></tt></td><td class="row1"><tt>';
			if($row['approved'] == 'n'){
				echo _AT('no1');
			}else{
				echo _AT('yes1');
			}
			//echo $row['approved'];
			echo '</tt></td><td class="row1">';

			if ($row['approved'] == 'n') {
				echo ' <input type="checkbox" name="id[]" value="'.$row[member_id].'" id="y'.$row[member_id].'" />';
				echo '<label for="y'.$row[member_id].'">'._AT('approve').'</label>';
			}

			echo '&nbsp;</td><td class="row1">';

			if ($row['approved'] == 'y') {
				echo ' <input type="checkbox" name="nid[]" value="'.$row[member_id].'" id="n'.$row[member_id].'"/>';
				echo '<label for="n'.$row[member_id].'">'._AT('disapprove').'</label>';
			}
			echo '&nbsp;</td>';

			echo '<td class="row1"><input type="checkbox" name="rid[]" value="'.$row[member_id].'" id="r'.$row[member_id].'" /><label for="r'.$row[member_id].'">'._AT('remove').'</label></td>';

			echo '</tr>';
			echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';

		} while ($row = mysql_fetch_array($result));

		echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';
		echo '<tr><td align="center" colspan="5" class="row1"><br />';
		echo '<input type="submit" name="submit" class="button" value="'._AT('done').'" />';
		echo '</td></tr>';

		echo '</table>';
	}

?>
	
	
</form>
<?php

function quote_csv($line) {
	$line = str_replace('"', '""', $line);

	$line = str_replace("\n", '\n', $line);
	$line = str_replace("\r", '\r', $line);
	$line = str_replace("\x00", '\0', $line);

	return '"'.$line.'"';
}
require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
?>