<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_GET['deny']) && isset($_GET['id'])) {
	header('Location: admin_deny.php?id='.$_GET['id']);
	exit;
	/*
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.intval($_GET['id']);
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', mysql_affected_rows($db), $sql);
	*/

} else if (isset($_GET['approve']) && isset($_GET['id'])) {
	$id = intval($_GET['id']);

	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.$id;
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', mysql_affected_rows($db), $sql);

	$sql = 'UPDATE '.TABLE_PREFIX.'members SET status='.AT_STATUS_INSTRUCTOR.', creation_date=creation_date, last_login=last_login WHERE member_id='.$id;
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_UPDATE, 'members', mysql_affected_rows($db), $sql);

	/* notify the users that they have been approved: */
	$sql   = "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$to_email = $row['email'];

		if ($row['first_name']!="" || $row['last_name']!="") {
			$tmp_message  = $row['first_name'].' '.$row['last_name'].",\n\n";		
		}	
		$tmp_message .= _AT('instructor_request_reply', AT_BASE_HREF);

		if ($to_email != '') {
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$mail = new ATutorMailer;

			$mail->From     = $_config['contact_email'];
			$mail->AddAddress($to_email);
			$mail->Subject = _AT('instructor_request');
			$mail->Body    = $tmp_message;

			if(!$mail->Send()) {
			   //echo 'There was an error sending the message';
			   $msg->printErrors('SENDING_ERROR');
			   exit;
			}

			unset($mail);
		}
	}

	$msg->addFeedback('PROFILE_UPDATED_ADMIN');
} else if (!empty($_GET) && !$_GET['submit']) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql	= "SELECT M.login, M.first_name, M.last_name, M.email, M.member_id, A.* FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."instructor_approvals A WHERE A.member_id=M.member_id ORDER BY M.login";
$result = mysql_query($sql, $db);
$num_pending = mysql_num_rows($result);
?>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('login_name');     ?></th>
	<th scope="col"><?php echo _AT('first_name');   ?></th>
	<th scope="col"><?php echo _AT('last_name');    ?></th>
	<th scope="col"><?php echo _AT('email');        ?></th>
	<th scope="col"><?php echo _AT('notes');        ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
	<input type="submit" name="deny" value="<?php echo _AT('deny'); ?>" /> 
	<input type="submit" name="approve" value="<?php echo _AT('approve'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php
	if ($row = mysql_fetch_assoc($result)) {
		do {
			echo '<tr onmousedown="document.form[\'i'.$row['member_id'].'\'].checked = true;rowselect(this);" id="r_'.$row['member_id'].'">';
			echo '<td><input type="radio" name="id" value="'.$row['member_id'].'" id="i'.$row['member_id'].'" /></td>';
			echo '<td><label for="i'.$row['member_id'].'">'.AT_print($row['login'], 'members.login').'</label></td>';
			echo '<td>'.AT_print($row['first_name'], 'members.first_name').'</td>';
			echo '<td>'.AT_print($row['last_name'], 'members.last_name').'</td>';
			echo '<td>'.AT_print($row['email'], 'members.email').'</td>';
			
			echo '<td>'.AT_print($row['notes'], 'instructor_approvals.notes').'</td>';

			echo '</tr>';
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<tr><td colspan="6">'._AT('none_found').'</td></tr>';
	}
?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>