<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

//check valid requester id
$request_id = intval($_REQUEST['id']);
$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=".$request_id;
$result	= mysql_query($sql, $db);
if (!($row = mysql_fetch_array($result))) {
	require(AT_INCLUDE_PATH.'header.inc.php'); 
	echo _AT('no_user_found');
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

//check admin
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if ($_POST['action'] == "process") {
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.$request_id;
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
			
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$mail = new ATutorMailer;

			$mail->From     = ADMIN_EMAIL;
			$mail->AddAddress($to_email);
			$mail->Subject = _AT('instructor_request');
			$mail->Body    = $message;

			if(!$mail->Send()) {
			   echo 'There was an error sending the message';
			   exit;
			}

			unset($mail);
		}
	}
	$_POST['action'] = "done";
	$msg->addFeedback('MSG_SENT');
	Header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>

<h3><?php echo _AT('instructor_requests'); ?></h3>

<?php
	/*
	if (isset($_GET['f'])) { 
		$f = intval($_GET['f']);
		if ($f <= 0) {
			/* it's probably an array *
			$f = unserialize(urldecode($_GET['f']));
		}
		print_feedback($f);
	}
	if (isset($errors)) { print_errors($errors); }
	if(isset($warnings)){ print_warnings($warnings); }
	*/
	$msg->printAll();
	
	echo '<p><br />'._AT('instructor_request_enterdenymsg');
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'"><br />';
	echo '<input type="hidden" name="action" value="process" />';
	echo '<input type="hidden" name="id" value="'.$request_id.'" />';

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


require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>