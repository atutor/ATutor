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

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ( ($_POST['description'] == '') && isset($_POST['form_request_instructor'])){
	$errors[]=AT_ERROR_DESC_REQUIRED;
} else if (isset($_POST['form_request_instructor'])) {
	 if (AUTO_APPROVE_INSTRUCTORS == true) {
		$sql	= "UPDATE ".TABLE_PREFIX."members SET status=1 WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);

		$f = AT_INFOS_ACCOUNT_APPROVED;
	} else {
		$sql	= "INSERT INTO ".TABLE_PREFIX."instructor_approvals VALUES ($_SESSION[member_id], NOW(), '$_POST[description]')";
		$result = mysql_query($sql, $db);
		/* email notification send to admin upon instructor request */
		if (EMAIL_NOTIFY && (ADMIN_EMAIL != '')) {
			$message = _AT('req_message_instructor', $_POST[form_from_login], $_POST[description], $_base_href);

			atutor_mail(ADMIN_EMAIL, _AT('req_message9'), $message, $_POST['form_from_email']);
		}
		$f = AT_INFOS_ACCOUNT_PENDING;
	}

	header('Location: index.php?f='.$f);
	exit;
} 

$title = _AT('request_instructor_account');
require(AT_INCLUDE_PATH.'cc_html/header.inc.php');

if (isset($errors)) { print_errors($errors); }

if (ALLOW_INSTRUCTOR_REQUESTS && ($row['status']!= 1) ) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."instructor_approvals WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	if (!($row = mysql_fetch_array($result))) {
		$infos[]=AT_INFOS_REQUEST_ACCOUNT;
		print_infos($infos);
?>
		<br /><form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<p align="center">
			<input type="hidden" name="form_request_instructor" value="true" />
			<input type="hidden" name="form_from_email" value="<?php echo $email; ?>" />
			<input type="hidden" name="form_from_login" value="<?php echo $login; ?>" />
			<label for="desc"><?php echo _AT('give_description'); ?></label><br /><br />
			<textarea cols="40" rows="3" class="formfield" id="desc" name="description" scroll="no"></textarea><br /><br />
			<input type="submit" name="submit" value="<?php echo _AT('request_instructor_account'); ?>" class="button" />
		</p>
		</form>
<?php
	} else {
		/* already waiting for approval */
		$infos[]=AT_INFOS_ACCOUNT_PENDING;
		print_infos($infos);
	}
} 

require (AT_INCLUDE_PATH.'cc_html/footer.inc.php'); 

?>