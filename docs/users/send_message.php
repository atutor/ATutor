<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page = 'inbox';
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$_section[0][0] = _AT('inbox');
$_section[0][1] = 'users/inbox.php';
$_section[1][0] = _AT('send_message');

$title = _AT('send_message');

if (!$_SESSION['valid_user']) {
	$_user_location	= 'public';
	require(AT_INCLUDE_PATH.'header.inc.php');

	$msg->printInfos('MSG_SEND_LOGIN');
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (($_POST['submit']) || ($_POST['submit_delete'])) {
	if (($_POST['to'] == '') || ($_POST['to'] == 0)) {
		 $msg->addError('NO_RECIPIENT');
	}
	if ($_POST['subject'] == '') {
		$msg->addError('MSG_SUBJECT_EMPTY');
	}
	if ($_POST['message'] == '') {
		$msg->addError('MSG_BODY_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['subject'] = $addslashes($_POST['subject']);
		$_POST['message'] = $addslashes($_POST['message']);

		$sql = "INSERT INTO ".TABLE_PREFIX."messages VALUES (0, $_SESSION[member_id], $_POST[to], NOW(), 1, 0, '$_POST[subject]', '$_POST[message]')";

		$result = mysql_query($sql,$db);

		if ($_POST['replied'] != '') {
			$result = mysql_query("UPDATE ".TABLE_PREFIX."messages SET replied=1 WHERE message_id=$_POST[replied]",$db);
		}

		if ($_POST['submit_delete']) {
			$result = mysql_query("DELETE FROM ".TABLE_PREFIX."messages WHERE message_id=$_POST[replied] AND to_member_id=$_SESSION[member_id]",$db);
		}

		header('Location: ./inbox.php?s=1');
		exit;
	}
}

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND (approved='y' OR approved='a')";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);

if ($row['cnt'] == 0) {
	require(AT_INCLUDE_PATH.'header.inc.php');

	$msg->printErrors('SEND_ENROL');

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}


if ($_GET['reply'] == '') {
	$onload = 'onload="document.form.subject.focus()"';
} else {
	$onload = 'onload="document.form.body.focus()"';
}

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2><a href="users/inbox.php">'._AT('inbox').'</a></h2>';

$msg->printAll();

echo '<p><br />'._AT('send_private_message').'</p>';

if ($_GET['reply'] != '') {

	$_GET['reply'] = intval($_GET['reply']);

	// get the member_id of the sender
	$result = mysql_query("SELECT from_member_id,subject,body FROM ".TABLE_PREFIX."messages WHERE message_id=$_GET[reply] AND to_member_id=$_SESSION[member_id]",$db);
	if ($myinfo = mysql_fetch_array($result)) {
		$reply_to	= $myinfo['from_member_id'];
		$subject	= $myinfo['subject'];
		$body		= $myinfo['body'];
	}
}
if ($_GET['l'] != '') {
	$reply_to = intval($_GET['l']);
}

/* check to make sure we're in the same course */
if ($reply_to) {
	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment E1, ".TABLE_PREFIX."course_enrollment E2 WHERE E1.member_id=$_SESSION[member_id] AND E2.member_id=$reply_to AND E1.course_id=E2.course_id AND (E1.approved='y' OR E1.approved='a') AND (E2.approved='y' OR E2.approved='a')";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_assoc($result);

	if ($row['cnt'] == 0) {
		$msg->printErrors('SEND_MEMBERS');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="replied" value="<?php echo $_GET['reply']; ?>" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" align="left" class="cyan"><?php echo  _AT('send_message'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><b><label for="to"><?php echo  _AT('to'); ?>:</label></b></td>
	<td class="row1"><?php

		if ($_GET['reply'] == '') {
			$sql	= "SELECT DISTINCT M.* FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."course_enrollment E1, ".TABLE_PREFIX."course_enrollment E2 WHERE E2.member_id=$_SESSION[member_id] AND E2.course_id=E1.course_id AND M.member_id=E1.member_id AND (E1.approved='y' OR E1.approved='a') AND (E2.approved='y' OR E2.approved='a')ORDER BY M.login";

			$result = mysql_query($sql, $db);
			$row	= mysql_fetch_assoc($result);
			echo '<select class="formfield" name="to" size="1" id="to">';
			echo '<option value="0"></option>';
			do {
				echo '<option value="'.$row['member_id'].'"';
				if ($reply_to == $row['member_id']){
					echo ' selected="selected"';
				} else if (isset ($_POST ['to']) && $_POST['to'] == $row['member_id']) {
					echo ' selected="selected"';
				}
				echo '>'.AT_print($row['login'], 'members.login').'</option>';
			} while ($row = mysql_fetch_assoc($result));
			echo '</select> <small class="spacer">'._AT('same_course_users').'</small>';
		} else {
			echo get_login($reply_to);
			echo '<input type="hidden" name="to" value="'.$reply_to.'" />';
		}
	?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><label for="subject"><?php echo  _AT('subject'); ?>:</label></b></td>
	<td class="row1"><input class="formfield" type="text" name="subject" id="subject" value="<?php
		if (($subject != '') && ($_POST['subject'] == '')) {
			if (!(substr($subject, 0, 2) == 'Re')) {
				$subject = "Re: $subject";
			}
			echo ContentManager::cleanOutput($subject);
		} else {
			echo ContentManager::cleanOutput($_POST['subject']);
		}
		?>" size="40" maxlength="100" /></td>	
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><b><label for="body"><?php echo _AT('message'); ?>:</label></b></td>
	<td class="row1"><textarea class="formfield" name="message" id="body" rows="15" cols="55"><?php
	if ($body != '') {
		if (strlen($body) > 400){
			$body = substr($body,0,400);
			$pos = strrpos($body,' ');
			$body = substr($body,0,$pos);
			$body .= ' ...';
		}
		$body  = "\n\n\n"._AT('in_reply_to').":\n".$body;
		echo $body;
	} else {
		echo $_POST['message'];
	}
	?></textarea><small class="bigspacer"><br />&middot; <?php echo _AT('html_disabled'); ?>.</small><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2"><a href="<?php echo substr($_my_uri, 0, strlen($_my_uri)-1); ?>#jumpcodes" title="<?php echo _AT('jump_code'); ?>"><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_code'); ?>" border="0" /></a><?php 
		
		$hide_learning_concepts = true;
		require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><a name="jumpcodes"></a><input type="submit" name="submit" value="  <?php  echo _AT('send_message') ?> [Alt-s]  " class="button" accesskey="s" />&nbsp;&nbsp;<?php
	if ($reply != '') {
		echo '<input type="submit" name="submit_delete" value="'._AT('send_delete').' [Alt-n]" accesskey="n" class="button" />&nbsp;';
	}
	?>&nbsp;<input type="reset" value="<?php echo _AT('start_over'); ?>" class="button" /></td>
</tr>
</table>
</form> 

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>