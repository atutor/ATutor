<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/atutor_mail.inc.php');
require(AT_INCLUDE_PATH.'lib/privileges.inc.php');
$_SESSION['course_id'] = 0;

$title = _AT('home'); 

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
$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_name";
$result = mysql_query($sql,$db);
if(mysql_num_rows($result) != 0){
	while($row = mysql_fetch_assoc($result)){
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

	header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_AUTO_ENABLED));
	exit;
}

require(AT_INCLUDE_PATH.'cc_html/header.inc.php');

?>		
<?php
	if (isset($_GET['f'])) {
		$f = intval($_GET['f']);
		if ($f > 0) {
			print_feedback($f);
		} else {
			/* it's probably an array */
			$f = unserialize(urldecode($_GET['f']));
			print_feedback($f);
		}
	}
	if (isset($feedback)) { print_feedback($feedback); }

	if (isset($errors)) { print_errors($errors); }

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
<a name="content"></a>
<?php
if ($status == 1) {
	// this user is a teacher
?>
	<table width="100%" class="bodyline" cellpadding="0" cellspacing="1" summary="">
		<tr>
		<th class="cyan" colspan="3"><?php echo _AT('taught_course'); ?></th></tr>
		<tr>
			<th class="cat" scope="col"><?php  echo _AT('course_name');  ?></th>
			<th class="cat" scope="col" width="50%"><?php  echo _AT('description');  ?></th>
			<th class="cat" scope="col"><?php  echo _AT('properties');  ?></th>
		</tr>
<?php
	$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE member_id=$_SESSION[member_id] ORDER BY title";
	$result = mysql_query($sql,$db);

	$num = mysql_num_rows($result);
	$count = 1;
	if ($row = mysql_fetch_array($result)) {
		do {
			echo '<tr>';
			
			echo '<td class="row1" width="150" valign="top"><a href="bounce.php?course='.$row['course_id'].'"><strong>'.AT_print($row['title'], 'courses.description').'</strong></a></td>';
			echo '<td class="row1"><small>'.AT_print($row['description'], 'courses.description');

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
   			$sql	  = "SELECT COUNT(*) FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='y'";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_array($c_result);

			/* minus 1 because the instructor doesn't count */
			echo '<br />&middot; '._AT('enrolled').': '.($c_row[0]-1).$pending.'<br />';
			echo '&middot; '._AT('created').': '.$row['created_date'].'<br />';

			$sql	  = "SELECT SUM(guests) + SUM(members) AS totals FROM ".TABLE_PREFIX."course_stats WHERE course_id=$row[course_id]";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_assoc($c_result);

			echo '&middot; '._AT('logins').': '. ($c_row['totals'] ? $c_row['totals'] : 0);
			echo ' <a href="users/course_stats.php?course='.$row['course_id'].SEP.'a='.$row['access'].'">'._AT('details').'</a><br />';

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
		echo '<tr><td class="row1" colspan="3"><em>'._AT('not_teacher').'</em></td></tr>';
	} 
	echo '</table><br />';
}
?>	
	<table width="100%" class="bodyline" cellpadding="0" cellspacing="1" summary="">
		<tr><th class="cyan" colspan="3"><?php echo _AT('enrolled_courses'); ?></th></tr>
		<tr>
			<th class="cat" scope="col"><?php echo _AT('course_name');  ?></th>
			<th class="cat" scope="col" width="50%"><?php echo _AT('description');  ?></th>
			<th class="cat" scope="col"><?php echo _AT('remove');       ?></th>
		</tr>
<?php


	$sql = "SELECT E.*, C.* FROM ".TABLE_PREFIX."course_enrollment E, ".TABLE_PREFIX."courses C WHERE E.member_id=$_SESSION[member_id] AND E.member_id<>C.member_id AND E.course_id=C.course_id ORDER BY C.title";
	$result = mysql_query($sql,$db);

	$num = mysql_num_rows($result);
	if ($row = mysql_fetch_array($result)) {
		do {
			echo '<tr><td class="row1" width="150" valign="top"><strong>';
			if (($row['approved'] == 'y') || ($row['access'] != 'private')) {
				echo '<a href="bounce.php?course='.$row['course_id'].'">'.AT_print($row['title'], 'courses.title').'</a>';
			} else {
				echo AT_print($row['title'], 'courses.title').' <small>'._AT('pending_approval').'</small>';
			}
			echo '</strong></td><td class="row1" valign="top">';			
			echo '<small>';
			echo AT_print($row['description'], 'courses.description');
if ($row['privileges'] > 0) {
	echo '<br /><br />'._AT('roles_privileges').': <strong>'.$row['role'].'</strong><br />';

	$comma = '';
	foreach ($privs as $key => $priv) {				
		if (query_bit($row['privileges'], $key)) { 
			if ($key == AT_PRIV_ENROLLMENT) {
				echo $comma.' <a href="users/enroll_admin.php?course='.$row['course_id'].'">'.$priv.'</a>';
			} else if ($key == AT_PRIV_COURSE_EMAIL) {
				echo $comma.' <a href="users/course_email.php?course='.$row['course_id'].'">'.$priv.'</a>';
			} else {
				echo $comma.' '.$priv;
			}
			$comma=',';
		}
	}
}
			echo '</small></td><td class="row1" valign="top">';
			echo '<small><a href="users/remove_course.php?course='.$row['course_id'].'">'._AT('remove').'</a>';
			echo '</small></td></tr>';
			if ($count < $num-1) {
				echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
			}
			$count++;
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<tr><td class="row1" colspan="3"><em>'._AT('no_enrolments').'</em></td></tr>';
	}
?>
	</table>

<?php
require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
?>