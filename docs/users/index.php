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
$_SESSION['s_is_super_admin'] = false;

if ( $_POST['description']=='' && isset($_POST['form_request_instructor'])){
	$errors[]=AT_ERROR_DESC_REQUIRED;
} else if (isset($_POST['form_request_instructor'])) {
	 if (AUTO_APPROVE_INSTRUCTORS == true) {
		$sql	= "UPDATE ".TABLE_PREFIX."members SET status=1 WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
	} else {
		$sql	= "INSERT INTO ".TABLE_PREFIX."instructor_approvals VALUES ($_SESSION[member_id], NOW(), '$_POST[description]')";
		$result = mysql_query($sql, $db);
		/* email notification send to admin upon instructor request */
		if (EMAIL_NOTIFY && (ADMIN_EMAIL != '')) {
			$message = _AT('req_message_instructor', $_POST[form_from_login], $_POST[description], $_base_href, $_base_href);

			atutor_mail(ADMIN_EMAIL, _AT('req_message9'), $message, $_POST['form_from_email']);
		}
	}

	Header('Location: index.php');
	exit;
}
// Get the course catagories
$sql = "SELECT * from ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
$result = mysql_query($sql);
if(mysql_num_rows($result) != 0){
	while($row = mysql_fetch_array($result)){
		$current_cats[$row['cat_id']] = $row['cat_name'];
		$parent_cats[$row['cat_id']] =  $row['cat_parent'];
		$cat_cats[$row['cat_id']] = $row['cat_id'];

	}
}
if (isset($_GET['auto']) && ($_GET['auto'] == 'disable')) {

	$parts = parse_url($_base_href);

	setcookie('ATLogin', '', time()-172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  '', time()-172800, $parts['path'], $parts['host'], 0);
	Header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_AUTO_DISABLED));
	exit;
} else if (isset($_GET['auto']) && ($_GET['auto'] == 'enable')) {
	$parts = parse_url($_base_href);

	$sql	= "SELECT PASSWORD(password) AS pass FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);

	setcookie('ATLogin', $_SESSION['login'], time()+172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  $row['pass'], time()+172800, $parts['path'], $parts['host'], 0);

	Header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_AUTO_ENABLED));
	exit;
}


require(AT_INCLUDE_PATH.'cc_html/header.inc.php');

if (isset($_GET['f']) && ($_GET['f'] == AT_FEEDBACK_AUTO_ENABLED)) {
	$warnings[] = AT_WARNING_AUTO_LOGIN;
	print_warnings($warnings);
}
if (isset($errors)) { print_errors($errors); }

?>
<h1 class="center"><?php echo _AT('control_centre').' - '._AT('home');  ?></h1>
<?php

	$sql	= "SELECT login, first_name, last_name, email, language, status FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);
	$status	= $row['status'];
	$email  = $row['email'];
	$login  = $row['login'];

	$help[] = AT_HELP_CONTROL_CENTER1;
	if ($status == 1) {
		$help[] = AT_HELP_CONTROL_CENTER2;
	}
	print_help($help);
 ?>
<h2><?php echo _AT('profile'); ?></h2>

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
	<tr>
		<th colspan="2" align="left" class="left"><?php print_popup_help(AT_HELP_CONTROL_PROFILE); ?><?php echo _AT('account_information'); ?></th>
	</tr>
<?php

	echo '<tr><td width="30%" class="row1" align="right"><b>'._AT('login_name').':</b></td><td class="row1">'.$row['login'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><b>'._AT('first_name').':</b></td><td class="row1">&nbsp;'.$row['first_name'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><b>'._AT('last_name').':</b></td><td class="row1">&nbsp;'.$row['last_name'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><b>'._AT('email_address').':</b></td><td class="row1">'.$row['email'].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><b>'._AT('language').':</b></td><td class="row1">'.$available_languages[$row['language']][3].'</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><b>'._AT('status').':</b></td><td class="row1">';
	if ($status == 0) {
		echo _AT('student');
	} else {
		echo _AT('instructor');
	}
	echo '</td></tr>';
	echo '<tr><td height="1" class="row2" colspan="2"></td></tr>';
	echo '<tr><td class="row1" align="right"><b>'._AT('auto_login1').':</b></td><td class="row1">';
	if ( ($_COOKIE['ATLogin'] != '') && ($_COOKIE['ATPass'] != '') ) {
		echo _AT('auto_enable');
	} else {
		echo _AT('auto_disable');
	}
	
	echo '</td></tr>';
?>
	</table>

<br />

<h2><?php echo _AT('enrolled_courses'); ?></h2>

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
	<tr>
		<th scope="col"><?php  echo _AT('course_name');  ?></th>
		<th scope="col"><?php  echo _AT('description');  ?></th>
		<th scope="col"><?php  echo _AT('remove');  ?></th>
	</tr>
<?php
	$sql	= "SELECT E.approved, C.* FROM ".TABLE_PREFIX."course_enrollment E, ".TABLE_PREFIX."courses C WHERE E.member_id=$_SESSION[member_id] AND E.member_id<>C.member_id AND E.course_id=C.course_id ORDER BY C.title";
	$result = mysql_query($sql,$db);

	$num = mysql_num_rows($result);
	if ($row = mysql_fetch_array($result)) {
		do {
			echo '<tr><td class="row1" width="150" valign="top"><b>';
			if (($row['approved'] == 'y') || ($row['access'] != 'private')) {
				echo '<a href="bounce.php?course='.$row['course_id'].'">'.$row['title'].'</a>';
			} else {
				echo $row['title'].' <small>'._AT('pending_approval').'</small>';
			}
			echo '</b></td><td class="row1" valign="top">';
			echo '<small>';
			echo $row['description'];

			echo '</small></td><td class="row1" valign="top">';
			echo '<a href="users/remove_course.php?course='.$row['course_id'].'">'._AT('remove').'</a>';
			echo '</td></tr>';
			if ($count < $num-1) {
				echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
			}
			$count++;
		} while ($row = mysql_fetch_array($result));
	} else {
		echo '<tr><td class="row1" colspan="3"><i>'._AT('no_enrolments').'</i></td></tr>';
	}

	echo '</table>';

	echo '<br />';

if ($status == 1){
	// this user is a teacher
	echo '<h2>'._AT('taught_course').' <a href="users/create_course.php">'._AT('create_course').'</a></h2>';
?>
	<table cellspacing="1" cellpadding="1" border="0" class="bodyline" width="95%" summary="">
	<tr>
		<th scope="col"><?php  echo _AT('course_name');  ?></th>
		<th scope="col" width="50%"><?php  echo _AT('description');  ?></th>
		<th scope="col"><?php  echo _AT('properties');  ?></th>
	</tr>
<?php
	$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE member_id=$_SESSION[member_id] ORDER BY title";
	$result = mysql_query($sql,$db);

	$num = mysql_num_rows($result);
	$count = 1;
	if ($row = mysql_fetch_array($result)) {
		do {
			echo '<tr>';
			
			echo '<td class="row1" width="150" valign="top"><a href="bounce.php?course='.$row['course_id'].'"><b>'.$row['title'].'</b></a></td>';
			echo '<td class="row1"><small>'.$row['description'];

			echo '<br /><br />';
			
			//course category
			echo '&middot; '. _AT('category').': ';
			if ($row['cat_id'] != 0) {
				echo $current_cats[$row['cat_id']];
			} else {
				echo _AT('cats_uncategorized');
			}
			echo '<br />';
			echo '&middot; '._AT('access').': ';
			$pending = '';
			switch ($row['access']){
				case 'public':
					echo _AT('public');
					break;
				case 'protected':
					echo _AT('protected');
					break;
				case 'private':
					echo _AT('private');
					$sql	  = "SELECT COUNT(*) FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='n'";
					$c_result = mysql_query($sql, $db);
					$c_row	  = mysql_fetch_array($c_result);
					$num_rows_c = mysql_num_rows($c_result);
					if($c_row[0] > 0){
						$pending  = '. '.$c_row[0].' '._AT('pending_approval2').' <a href="users/enroll_admin.php?course='.$row['course_id'].'"> '._AT('pending_approval3').'</a>';
					}
					break;
			}
   			$sql	  = "SELECT COUNT(*) FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id]";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_array($c_result);

			/* minus 1 because the instructor doesn't count */
			echo '<br />&middot; '._AT('enrolled').': '.($c_row[0]-1).$pending.'<br />';
			echo '&middot; '._AT('created').': '.$row['created_date'].'<br />';

			$sql	  = "SELECT SUM(guests) AS guests, SUM(members) AS members FROM ".TABLE_PREFIX."course_stats WHERE course_id=$row[course_id]";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_array($c_result);

			echo '&middot; '._AT('logins').': ';
			if ($row['access'] != 'private') {
				echo 'G: '.($c_row['guests'] ? $c_row['guests'] : 0).', ';
			}
			echo 'M: '.($c_row['members'] ? $c_row['members'] : 0).'. <a href="users/course_stats.php?course='.$row['course_id'].SEP.'a='.$row['access'].'">'._AT('details').'</a><br />';

			echo '</small></td>';

			echo '<td class="row1" valign="top"><small>&middot; <a href="users/course_properties.php?course='.$row['course_id'].'">'._AT('properties').'</a><br />';

			echo '&middot; <a href="users/enroll_admin.php?course='.$row['course_id'].'">'._AT('enrolments').'</a><br />';
			echo '&middot; <a href="users/course_email.php?course='.$row['course_id'].'">'._AT('course_email').'</a><br />';
			echo '<br />&middot; <a href="users/delete_course.php?course='.$row['course_id'].'">'._AT('delete').'</a></small></td>';
			echo '</tr>';

			if ($count < $num) {
				echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
			}
			$count++;
		} while ($row = mysql_fetch_array($result));
	} else {

		echo '<tr><td class="row1" colspan="3"><i>'._AT('not_teacher').'</i></td></tr>';
	}
	echo '</table>';

} else if (ALLOW_INSTRUCTOR_REQUESTS) {

	echo '<h2>'._AT('taught_courses2').'</h2>';

	$sql	= "SELECT * FROM ".TABLE_PREFIX."instructor_approvals WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	if (!($row = mysql_fetch_array($result))) {
		$infos[]=AT_INFOS_REQUEST_ACCOUNT;
		print_infos($infos);
?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="form_request_instructor" value="true" />
			<input type="hidden" name="form_from_email" value="<?php echo $email; ?>" />
			<input type="hidden" name="form_from_login" value="<?php echo $login; ?>" />
			<label for="desc"><?php echo _AT('give_description'); ?></label><br />
			<textarea cols="40" rows="3" class="formfield" id="desc" name="description"></textarea><br />
			<input type="submit" name="submit" value="<?php echo _AT('request_instructor_account'); ?>" class="button" />
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