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
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/atutor_mail.inc.php');

if (!$_SESSION['s_is_super_admin']) {
	exit;
}
if ($_POST['action'] == "process") {
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.$_POST['id'];
	$result = mysql_query($sql, $db);
		
	/* notify the users that they have been denied: */
	$sql   = "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=".$_POST['id'];
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_array($result)) {
		/* assumes that there is a first and last name for this user, but not required during registration */
		$to_email = $row['email'];
		if ($row['first_name']!="" || $row['last_name']!="") {
			$message  = $row['first_name'].' '.$row['last_name'].",\n\n";		
		}		
		$message .= _AT('instructor_request_deny', $_base_href)." \n\n".$_POST['deny_msg'].' '.$_POST['deny_msg_other'];		

		if ($to_email != '') {
			atutor_mail($to_email, _AT('instructor_request'), $message, ADMIN_EMAIL);
		}
	}
	$_POST['action'] = "done";
	Header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_MSG_SENT));
	exit;
}

require(AT_INCLUDE_PATH.'admin_html/header.inc.php');

$sql	= "SELECT M.login, M.member_id, A.* FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."instructor_approvals A WHERE A.member_id=M.member_id ORDER BY M.login";

if ($result = mysql_query($sql)) {
	$num_pending = mysql_num_rows($result);
}
?>

<h2><?php echo _AT('instructor_requests'); ?></h2>

<?php

	if (isset($_GET['f'])) { 
		$f = intval($_GET['f']);
		if ($f <= 0) {
			/* it's probably an array */
			$f = unserialize(urldecode($_GET['f']));
		}
		print_feedback($f);
	}
	if (isset($errors)) { print_errors($errors); }
	if(isset($warnings)){ print_warnings($warnings); }
	echo '<p><br />'._AT('instructor_request_enterdenymsg');
	echo '<form method="post" action="'.$PHP_SELF.'"><br />';
	echo '<input type="hidden" name="action" value="process" />';
	echo '<input type="hidden" name="id" value="'.$_GET['id'].'" />';

	echo '<select name="deny_msg"><br /><br />';
	echo '<option value="">'._AT('select').'</option><br>';
	echo '<option>'._AT('instructor_request_denymsg1').'</option><br />';
	echo '<option>'._AT('instructor_request_denymsg2').'</option><br />';
	echo '<option>'._AT('instructor_request_denymsg3').'</option><br />';
	echo '<option>'._AT('instructor_request_denymsg4').'</option><br />';
	echo '</select><br /><br />';

	echo '<textarea cols=30 rows=7 name="deny_msg_other"></textarea><br />';
	echo '<input type="submit" name="submit" value="'._AT('send').'" class="button" /><br />';
	echo '</form><br /><br /></p>';


require(AT_INCLUDE_PATH.'cc_html/footer.inc.php'); 
?>
