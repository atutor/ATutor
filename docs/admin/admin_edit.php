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
$_include_path = '../include/';
require($_include_path.'vitals.inc.php');
require($_include_path.'lib/atutor_mail.inc.php');

if (!$_SESSION['s_is_super_admin']) {
	Header('Location: index.php');
	exit;
}

if ($_POST['submit']) {
	$_POST['form_status']	= intval($_POST['form_status']);
	$_POST['form_id']		= intval($_POST['form_id']);
	$_POST['old_status']	= intval($_POST['old_status']);

	$sql = "UPDATE ".TABLE_PREFIX."members SET status=$_POST[form_status] WHERE member_id=$_POST[form_id]";
	$result = mysql_query($sql, $db);

	if (!$result) {
		echo 'DB Error';
		exit;
	}

	if ($_POST['form_status'] > $_POST['old_status']) {
		/* delete the request: */
		$sql = "DELETE FROM ".TABLE_PREFIX."instructor_approvals WHERE member_id=$_POST[form_id]";
		$result = mysql_query($sql, $db);

		/* notify the users that they have been approved: */
		$sql   = "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$_POST[form_id]";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_array($result)) {
			/* assumes that there is a first and last name for this user, but not required during registration */
			$to_email = $row['email'];

			if ($row['first_name']!="" || $row['last_name']!="") {
			$message  = $row['first_name'].' '.$row['last_name'].",\n\n";		
			}	
			//$message .= 'Your instructor account request for the ATutor system has been approved. Go to '.$_base_href.' to login to your control centre and start creating your courses. ';
			//$message .= _AT('instructor_request_msg1').' '.$_base_href.' '._AT('instructor_request_msg2');
			$message .= _AT('instructor_request_reply', $_base_href);

			if ($to_email != '') {
				atutor_mail($to_email, _AT('instructor_request'), $message, ADMIN_EMAIL);
			}
		}
	}
	Header('Location: ./profile.php?member_id='.$_POST['form_id']);
	exit;
}

require($_include_path.'admin_html/header.inc.php'); 
?>
<h2><?php echo _AT('atutor_administration'); ?></h2>
<h3><?php echo _AT('edit_user'); ?></h3>

<?php
		$id		= intval($_GET[id]);
		$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=$id";
		$result = mysql_query($sql);
		if (!($row = mysql_fetch_array($result)))
		{
			echo _AT('no_user_found');
		} else {
			echo _AT('login').": <b>$row[login]</b>";
			echo '<br />';
			echo '<form method="post" action="'.$PHP_SELF.'">';
			echo '<input type="hidden" name="form_id" value="'.$id.'" />';
			if ($row['status'])
			{
				$inst = ' checked="checked"';
			} else {
				$stnd = ' checked="checked"';
			}
			echo _AT('status').': <input type="radio" name="form_status" value="1" id="inst"'.$inst.' /><label for="inst">'._AT('instructor').'</label>, <input type="radio" name="form_status" value="0" id="stnd"'.$stnd.' /><label for="stnd">'._AT('student1').'</label>';
			echo '<br />';
			echo '<br />';
			echo '<input type="submit" name="submit" value="'._AT('update_status').'" class="button" />';
			echo '<input type="hidden" name="old_status" value="'.$row['status'].'" />';
			echo '</form>';
		}
?>
 
<?php
	require($_include_path.'cc_html/footer.inc.php'); 
?>
