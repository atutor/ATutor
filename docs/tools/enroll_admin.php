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

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enroll_admin.php';


if ($_POST['done']) {
	header('Location: index.php');
	exit;
}

/* make sure we own this course that we're approving for! */
$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db);

if (mysql_num_rows($result) != 1 && !authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$errors[] = AT_ERROR_NOT_OWNER;
	print_errors($errors);
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}
$row = mysql_fetch_assoc($result);
$title = $row['title'];
$access = $row['access'];

if($_GET['export_enrollment'] && !$no_students){

	$sql5 = "SELECT member_id FROM ".TABLE_PREFIX."course_enrollment WHERE course_id = $_SESSION[course_id]";
	$result5 =  mysql_query($sql5,$db);
	$enrolled = array();

	while ($row = mysql_fetch_assoc($result5)){
		$sql1 = "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id = $row[member_id]";
		$result1 = mysql_query($sql1,$db);
		while ($row2 = mysql_fetch_array($result1)){
			if ($row2['member_id'] != $_SESSION['member_id']){
				$this_row .= quote_csv($row2['first_name']).",";
				$this_row .= quote_csv($row2['last_name']).",";
				$this_row .= quote_csv($row2['email'])."\n";
			}
		}
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
if ($_POST['submit']) {
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

			$message  = $row['first_name'].' '.$row['last_name'].'( '._AT('login_name').':'.$row['login'].' - '._AT('password').':'. $row['password'].")\n";
			$message .= _AT('enrol_message_approved', $system_courses[$_POST['form_course_id']]['title'], $_base_href);

			if ($to_email != '') {
				require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

				$mail = new ATutorMailer;

				$mail->From     = ADMIN_EMAIL;
				$mail->AddAddress($to_email);
				$mail->Subject = _AT('enrol_message_subject');
				$mail->Body    = $message;

				if(!$mail->Send()) {
				   echo 'There was an error sending the message';
				   exit;
				}

				unset($mail);

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
	$feedback[] = AT_FEEDBACK_ENROLMENT_UPDATED;
}

$title = _AT('course_enrolment');
require(AT_INCLUDE_PATH.'header.inc.php');

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
<a name="content"></a>
<?php
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
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">

<input type="hidden" name="form_course_id" value="<?php echo $_SESSION['course_id']; ?>" />
<p><a href="tools/import_course_list.php"> <?php echo _AT('list_import_course_list')  ?></a> | <a href="<?php echo $_SERVER['PHP_SELF']; ?>?export_enrollment=1"><?php echo _AT('list_export_course_list')  ?></a> </p>
<?php

	// note: doesn't list the owner of the course.
	$sql	= "SELECT *, C.member_id FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M, ".TABLE_PREFIX."courses S WHERE C.course_id=$_SESSION[course_id] AND S.course_id=$_SESSION[course_id] AND C.member_id=M.member_id AND S.member_id<>C.member_id ORDER BY C.approved, M.login";	
	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_assoc($result))) {
		$infos[]=AT_INFOS_NO_ENROLLMENTS;
		print_infos($infos);
		$no_students =1;

	} else {
		print_help($help);
		echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%" align="center">';
		echo '<tr><th colspan="6" class="cyan">'._AT('enrolled').'</th></tr>';

		echo '<tr><th class="cat" scope="col">'._AT('login_id').'</th><th class="cat" scope="col">'._AT('roles_privileges').'</th><th class="cat" scope="col">'._AT('enrolled').'</th>';
		if ($access == 'private') {
			echo '<th class="cat" scope="col"><input type="checkbox" value="SelectAll" id="all" title="select/unselect all" name="selectall" onclick="CheckAll();"/>'._AT('approve').'</th><th class="cat" scope="col">'._AT('disapprove').'</th>';
		}
		echo '<th class="cat" scope="col">'._AT('remove').'</th></tr>';

		do {
			echo '<tr>';
			echo '<td class="row1"><a href="tools/view_profile.php?mid='.$row['member_id'].'">'.AT_print($row['login'], 'members.login').'</a></td>';

			echo '<td class="row1">';
			echo '<a href="tools/privileges.php?mid='.$row['member_id'].'">';
			if ($row['role']) {
				echo $row['role'];
			} else {
				echo _AT('student');
			}
			echo '</a>';
			echo '</td>';

			echo '<td class="row1">';
			if($row['approved'] == 'n'){
				echo _AT('no1');
			}else{
				echo _AT('yes1');
			}
			echo '</td>';

			if ($_SESSION['member_id'] == $row['member_id']) {
				
				if ($access == 'private') {
					echo '<td class="row1">&nbsp;</td>';
				}
				echo '<td class="row1">&nbsp;</td>';
			} else {
				if ($access == 'private') {
					echo '<td class="row1">';

					if ($row['approved'] == 'n') {
						echo ' <input type="checkbox" name="id[]" value="'.$row['member_id'].'" id="y'.$row['member_id'].'" />';
						echo '<label for="y'.$row['member_id'].'">'._AT('approve').'</label>';
					}

					echo '&nbsp;</td><td class="row1">';

					if ($row['approved'] == 'y') {
						echo ' <input type="checkbox" name="nid[]" value="'.$row['member_id'].'" id="n'.$row['member_id'].'"/>';
						echo '<label for="n'.$row['member_id'].'">'._AT('disapprove').'</label>';
					}
					echo '&nbsp;</td>';
				}

				echo '<td class="row1"><input type="checkbox" name="rid[]" value="'.$row['member_id'].'" id="r'.$row['member_id'].'" /><label for="r'.$row['member_id'].'">'._AT('remove').'</label></td>';
			}
			echo '</tr>';
			echo '<tr><td height="1" class="row2" colspan="6"></td></tr>';

		} while ($row = mysql_fetch_assoc($result));

		echo '<tr><td height="1" class="row2" colspan="6"></td></tr>';
		echo '<tr><td align="center" colspan="6" class="row1"><br />';
		echo '<input type="submit" name="submit" class="button" value="'._AT('submit').'" />  <input type="submit" name="done" class="button" value="'._AT('close').'" />';
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
require(AT_INCLUDE_PATH.'footer.inc.php');
?>
