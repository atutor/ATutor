<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_COURSE_EMAIL);

$course = intval($_GET['course']);

if ($course == 0) {
	$course = $_SESSION['course_id'];
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['to_enrolled']   = trim($_POST['to_enrolled']);
	$_POST['to_unenrolled'] = trim($_POST['to_unenrolled']);
	$_POST['to_alumni']     = trim($_POST['to_alumni']);
	$_POST['to_assistants'] = trim($_POST['to_assistants']);

	$_POST['subject'] = trim($_POST['subject']);
	$_POST['body'] = trim($_POST['body']);

	if ( ($_POST['to_enrolled']   == '') &&
		 ($_POST['to_unenrolled'] == '') &&
		 ($_POST['to_alumni']     == '') &&
		 ($_POST['to_assistants'] == '') &&
		 ($_POST['groups']        == '')
		) {
		$msg->addError('MSG_TO_EMPTY');
	}

	if ($_POST['subject'] == '') {
		$msg->addError('MSG_SUBJECT_EMPTY');
	}

	if ($_POST['body'] == '') {
		$msg->addError('MSG_BODY_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$email_sql	= "SELECT email FROM ".TABLE_PREFIX."course_enrollment C INNER JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE C.course_id=$course AND (";
		
		if ($_POST['to_unenrolled']) {
			// choose all unenrolled
			$email_sql .= "C.approved='n' OR ";
		}
		
		if ($_POST['to_alumni']) {
			// choose all alumni
			$email_sql 	.= "C.approved='a' OR ";
		}

		if ($_POST['to_assistants']){
			// choose all assistants
			$email_sql	.= "C.privileges<>0 OR ";
		}

		if ($_POST['groups']) {
			// specific groups
			$groups = implode(',', $_POST['groups']);

			$group_members = array();
			$sql = "SELECT member_id FROM ".TABLE_PREFIX."groups_members WHERE group_id IN ($groups)";
			$result = mysql_query($sql, $db);
			while ($row = mysql_fetch_assoc($result)) {
				$group_members[] = $row['member_id'];
			}
			$group_members = implode(',', $group_members);

			$email_sql .= "M.member_id IN ($group_members) OR ";
		} else if ($_POST['to_enrolled']) {
			// includes instructor
			$email_sql 	.= "C.approved='y' OR ";
		}

		$email_sql = substr_replace($email_sql, '', -4). ')'; // strip off the last ' OR '
		$result = mysql_query($email_sql,$db);

		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		// generate email recipients
		$mail_list = array();
		while ($row = mysql_fetch_assoc($result)) {
			$mail_list[]=$row['email'];
		}

		// Get instructor ID.
		$result = mysql_query("SELECT member_id FROM ".TABLE_PREFIX."courses WHERE course_id=$course",$db);
		$row = mysql_fetch_assoc($result);
		$instructor_id = $row['member_id'];

		// Add instructor to email list if he is not the one sending email.
		if ($instructor_id != $_SESSION['member_id']) {
			$sql = "SELECT email FROM ".TABLE_PREFIX."members WHERE member_id=$instructor_id";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$mail_list[]= $row['email'];
		}

		// Get the sender.		
		$result = mysql_query("SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]", $db);
		$row	= mysql_fetch_assoc($result);
		$mail_list[] = $row['email'];

		// Prep the mailer.
		$mail = new ATutorMailer;
		$mail->From     = $row['email'];
		$mail->FromName = $row['first_name'] . ' ' . $row['last_name'];
		$mail->AddAddress($row['email']);
		$mail->Subject = $_POST['subject'];
		$mail->Body    = $_POST['body'];
		foreach ($mail_list as $recip) {
			$mail->AddBCC($recip);
		}
		if(!$mail->Send()) {
		   //echo 'There was an error sending the message';
		   $msg->printErrors('SENDING_ERROR');
		   exit;
		}
		unset($mail);

		$msg->addFeedback('MSG_SENT');
		header('Location: index.php');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND M.member_id<>$_SESSION[member_id] ORDER BY C.approved, M.login";
$result = mysql_query($sql,$db);
$row	= mysql_fetch_array($result);
if ($row['cnt'] == 0) {
	$msg->printInfos('NO_STUDENTS');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
		<?php echo  _AT('to'); ?><br />
		<input type="checkbox" name="to_assistants" value="1" id="assistants" <?php if ($_POST['to_assistants']=='1') { echo 'checked="checked"'; } ?> /><label for="assistants"><?php echo  _AT('assistants'); ?></label>
		<input type="checkbox" name="to_enrolled" value="1" id="enrolled" <?php if ($_POST['to_enrolled']=='1') { echo 'checked="checked"'; } else { echo 'checked="checked"'; } ?> /><label for="enrolled"><?php echo  _AT('enrolled'); ?></label>
		<input type="checkbox" name="to_unenrolled" value="1" id="unenrolled" <?php if ($_POST['to_unenrolled']=='1') { echo 'checked="checked"'; } ?> /><label for="unenrolled"><?php echo  _AT('unenrolled'); ?></label>
		<input type="checkbox" name="to_alumni" value="1" id="alumni" <?php if ($_POST['to_alumni']=='1') { echo 'checked="checked"'; } ?> /><label for="alumni"><?php echo  _AT('alumni'); ?></label>

		<?php
		$sql = "SELECT type_id, title FROM ".TABLE_PREFIX."groups_types WHERE course_id=$_SESSION[course_id] ORDER BY title";
		$result = mysql_query($sql, $db);
		?>
		<?php if ($row = mysql_fetch_assoc($result)): ?>
			<br /><br />
			<?php echo _AT('or_groups'); ?>:<br />
			<select name="groups[]" multiple="multiple" size="10" style="padding-right: 5px">
				<?php do { ?>
					<optgroup label="<?php echo $row['title']; ?>">
						<?php 
							$sql = "SELECT group_id, title FROM ".TABLE_PREFIX."groups WHERE type_id=$row[type_id] ORDER BY title";
							$group_result = mysql_query($sql, $db);
						?>
						<?php while ($group_row = mysql_fetch_assoc($group_result)): ?>
							<option value="<?php echo $group_row['group_id']; ?>"><?php echo $group_row['title']; ?></option>
						<?php endwhile; ?>
					</optgroup>
				<?php } while ($row = mysql_fetch_assoc($result)); ?>
			</select>
		<?php endif; ?>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" name="subject" size="60" id="subject" value="<?php echo $_POST['subject']; ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea cols="55" rows="18" name="body" id="body"><?php echo $_POST['body']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>