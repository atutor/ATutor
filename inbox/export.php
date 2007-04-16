<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('INVALID_USER');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} else if (isset($_POST['export'], $_POST['messages'])) {
	$inbox_messages = $sent_messages = '';

	$my_display_name = get_display_name($_SESSION['member_id']);

	// inbox messages
	if ($_POST['messages'] == 1 || $_POST['messages'] == 2) {
		$sql = "SELECT * FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] ORDER BY date_sent";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$msg  = _AT('from')   .': ' . get_display_name($row['from_member_id']) . "\r\n";
			$msg .= _AT('to')     .': ' . $my_display_name . "\r\n";
			$msg .= _AT('subject').': ' . $row['subject'] . "\r\n";
			$msg .= _AT('date')   .': ' . $row['date_sent'] . "\r\n";
			$msg .= _AT('body')   .': ' . $row['body'] . "\r\n";
			$msg .= "\r\n=============================================\r\n\r\n";

			$inbox_messages .= $msg;
		}
	}

	// sent messages
	if ($_POST['messages'] == 1 || $_POST['messages'] == 3) {
		$sql = "SELECT * FROM ".TABLE_PREFIX."messages_sent WHERE from_member_id=$_SESSION[member_id] ORDER BY date_sent";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$msg  = _AT('from')   .': ' . $my_display_name  . "\r\n";
			$msg .= _AT('to')     .': ' . get_display_name($row['from_member_id']). "\r\n";
			$msg .= _AT('subject').': ' . $row['subject'] . "\r\n";
			$msg .= _AT('date')   .': ' . $row['date_sent'] . "\r\n";
			$msg .= _AT('body')   .': ' . $row['body'] . "\r\n";
			$msg .= "\r\n=============================================\r\n\r\n";

			$sent_messages .= $msg;
		}
	}

	if ($inbox_messages && $sent_messages) {
		// add the two to a zip file
		require(AT_INCLUDE_PATH.'classes/zipfile.class.php'); // for zipfile
		$zipfile = new zipfile();
		$zipfile->add_file($inbox_messages, _AT('inbox').'.txt');
		$zipfile->add_file($sent_messages, _AT('sent_messages').'.txt');
		$zipfile->close();
		$zipfile->send_file(_AT('inbox').'-'.date('Ymd'));
		exit;
	} else if ($inbox_messages) {
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="'._AT('inbox').'-'.date('Ymd').'.txt"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.strlen($inbox_messages));

		echo $inbox_messages;
		exit;
	} else if ($sent_messages) {
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="'._AT('sent_messages').'-'.date('Ymd').'.txt"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.strlen($sent_messages));

		echo $sent_messages;
		exit;
	} // else. nothing to export

}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"/>
<div class="input-form">
	<div class="row">
		<?php echo _AT('export'); ?><br />
		<input type="radio" name="messages" value="1" id="m1" checked="checked"><label for="m1"><?php echo _AT('all'); ?></label><br />
		<input type="radio" name="messages" value="2" id="m2"><label for="m2"><?php echo _AT('inbox'); ?></label><br />
		<input type="radio" name="messages" value="3" id="m3"><label for="m3"><?php echo _AT('sent_messages'); ?></label><br />
	</div>

	<div class="row buttons">
		<input type="submit" name="export" value="<?php echo _AT('export'); ?>"/>
	</div>
</div>
</form>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>