<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

$page = 'my_courses';
$_user_location	= 'users';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ($_SESSION['valid_user'] !== true) {
	require(AT_INCLUDE_PATH.'header.inc.php');

	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$title = _AT('home'); 

if ( $_POST['description']=='' && isset($_POST['form_request_instructor'])) {
	$msg->addError('DESC_REQUIRED');
} else if (isset($_POST['form_request_instructor'])) {
	 if (AUTO_APPROVE_INSTRUCTORS == true) {
		$sql	= "UPDATE ".TABLE_PREFIX."members SET status=1 WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
	} else {
		$sql	= "INSERT INTO ".TABLE_PREFIX."instructor_approvals VALUES ($_SESSION[member_id], NOW(), '$_POST[description]')";
		$result = mysql_query($sql, $db);
		/* email notification send to admin upon instructor request */
		if (EMAIL_NOTIFY && (ADMIN_EMAIL != '')) {
			$message = _AT('req_message_instructor', $_POST['form_from_login'], $_POST['description'], $_base_href, $_base_href);

			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$mail = new ATutorMailer;

			$mail->From     = $_POST['form_from_email'];
			$mail->AddAddress(ADMIN_EMAIL);
			$mail->Subject = _AT('req_message9');
			$mail->Body    = $message;

			if(!$mail->Send()) {
			   echo 'There was an error sending the message';
			   exit;
			}

			unset($mail);
		}
	}

	header('Location: index.php');
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

//is this section used on this page?
if (isset($_GET['auto']) && ($_GET['auto'] == 'disable')) {

	$parts = parse_url($_base_href);

	setcookie('ATLogin', '', time()-172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  '', time()-172800, $parts['path'], $parts['host'], 0);
	
	$msg->addFeedback('AUTO_DISABLED');
	Header('Location: index.php');
	exit;
} else if (isset($_GET['auto']) && ($_GET['auto'] == 'enable')) {
	$parts = parse_url($_base_href);

	$sql	= "SELECT PASSWORD(password) AS pass FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);

	setcookie('ATLogin', $_SESSION['login'], time()+172800, $parts['path'], $parts['host'], 0);
	setcookie('ATPass',  $row['pass'], time()+172800, $parts['path'], $parts['host'], 0);

	$msg->addFeedback('AUTO_ENABLED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT E.approved, E.role, E.last_cid, C.* FROM ".TABLE_PREFIX."course_enrollment E, ".TABLE_PREFIX."courses C WHERE E.member_id=$_SESSION[member_id] AND E.course_id=C.course_id ORDER BY C.title";
$result = mysql_query($sql,$db);

while ($row = mysql_fetch_assoc($result)): $count++; ?>
	<div class="course" onmousedown="document.location='bounce.php?course=<?php echo $row['course_id']; ?>'">
		<h2><a href="bounce.php?course=<?php echo $row['course_id']; ?>"><?php echo $row['title']; ?></a></h2>

		<a href="bounce.php?course=<?php echo $row['course_id']; ?>">
		<?php 
			if ($row['icon'] == '') {
				echo '<img src="images/clr.gif" class="icon" border="0" width="79" height="79" />';
			} else {
				echo '<img src="images/courses/' . $row['icon'] .'" class="icon" border="0" />';
			}
		?>
		</a>
		<p>Instructor: <a href=""><?php echo get_login($row['member_id']); ?></a><br />
			My Role: <?php echo $row['role']; ?><br /></p>

		<div class="shortcuts">
			<a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('content.php?cid='.$row['last_cid']); ?>"><img src="http://marathonman.sourceforge.net/docs/images/ug/resume.gif" border="0" title="Resume Shortcut" /></a>
		</div>
	</div>
<?php endwhile; ?>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>
<style>
div.course {
	position: relative;
	width: 300px;
	border: rgb(204, 204, 204) 1px solid;
	background-color: #FFFCE5;
	float: left;
	margin: 3px;
	padding: 3px;
}

div.course.break {
	clear: left;
}

div.course h2 {
	border: 0px;
	font-weight: normal;
	font-size: large;

}

div.course:hover {
	background-color: #FFF8C8;
	border: #AAAAAA 1px solid;
	cursor: pointer;
}

div.course a {
	text-decoration: none;
}

div.course:hover a {
	color: #006699;
}

div.course a:hover {
	color: #000000;
}

div.course p {
	font-size: small;
}

div.course p a {
	font-weight: bold;
}

div.course img.icon	{
	float: left;
	border: rgb(234, 234, 234) 1px solid;
	background-color: #FFF8C8;
	margin: 2px;
}


div.course div.shortcuts {
	text-align: right;
	clear: left;
	vertical-align: middle;
}

</style>
<?php
exit;

	/*
	$msg->addHelp('CONTROL_CENTER1');
	if (get_instructor_status( )) {
		$msg->addHelp('CONTROL_CENTER2');
	}
	$msg->printHelps();
	*/

if (get_instructor_status( )) { /* see vitals */
	// this user is a teacher
?>
	<table width="95%" align="center" class="bodyline" cellpadding="0" cellspacing="1" summary="">
		<tr>
		<th class="cyan" colspan="3"><?php echo _AT('taught_course'); ?></th></tr>
		<tr>
			<th class="cat" scope="col"><?php  echo _AT('course_name');  ?></th>
			<th class="cat" scope="col" width="50%"><?php  echo _AT('description');  ?></th>
			<th class="cat" scope="col"><?php  echo _AT('shortcuts');  ?></th>
		</tr>
<?php
	$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE member_id=$_SESSION[member_id] ORDER BY title";
	$result = mysql_query($sql,$db);

	$num = mysql_num_rows($result);
	$count = 1;
	if ($row = mysql_fetch_assoc($result)) {
		do {
			echo "\n".'<tr>';
			
			echo '<td class="row1" width="150" valign="top"><a href="bounce.php?course='.$row['course_id'].'"><strong>'.AT_print($row['title'], 'courses.description').'</strong></a></td>';
			echo '<td class="row1"><small>'.AT_print($row['description'], 'courses.description');

			echo '<br /><br />'."\n";
			
			//course category
			echo '&middot; '. _AT('category').': ';
			if ($row['cat_id'] != 0) {
				echo $current_cats[$row['cat_id']];
			} else {
				echo _AT('cats_uncategorized');
			}
			echo '<br />'."\n";
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
						$pending  = ', '.$c_row[0].' <em><strong>'._AT('pending_approval2').' <a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('tools/enrollment/index.php?current_tab=1').'"> '._AT('pending_approval3').'</a></strong></em>.';
					}
					break;
			}
   			$sql	  = "SELECT COUNT(*) FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='y'";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_array($c_result);

			/* minus 1 because the instructor doesn't count */
			echo "<br />\n&middot; "._AT('enrolled').": ".($c_row[0]-1).$pending." ";

   			$sql	  = "SELECT COUNT(*) FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='a'";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_array($c_result);
			echo _AT('alumni') . ': ' . $c_row[0] . '<br />';

			echo '&middot; '._AT('created').': '.$row['created_date'].'<br />'."\n";

			$sql	  = "SELECT SUM(guests) + SUM(members) AS totals FROM ".TABLE_PREFIX."course_stats WHERE course_id=$row[course_id]";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_assoc($c_result);

			echo '&middot; '._AT('logins').': '. ($c_row['totals'] ? $c_row['totals'] : 0);
			echo ' <a href="users/course_stats.php?course='.$row['course_id'].SEP.'a='.$row['access'].'">'._AT('details').'</a><br />';

			echo '</small></td>';

			echo '<td class="row1" valign="top"><small>';

			echo '&middot; <a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('tools/index.php#ins-tools').'">'._AT('tools_shortcut').'</a><br />'."\n";

			echo '&middot; <a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('forum/list.php').'">'._AT('forums_shortcut').'</a><br />'."\n";

			if (defined('AC_PATH') && AC_PATH) {
				echo '&middot; <a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('acollab/bounce.php').'" >'._AT('groups_shortcut').'</a><br />';
			}
			echo '<br />'."\n".'&middot; <a href="users/delete_course.php?course='.$row['course_id'].'">'._AT('delete').'</a></small></td>'."\n";
			echo '</tr>';

			if ($count < $num) {
				echo '<tr><td height="1" class="row2" colspan="3"></td></tr>'."\n";
			}
			$count++;
		} while ($row = mysql_fetch_array($result));
	} else {
		echo '<tr><td class="row1" colspan="3"><em>'._AT('not_teacher').'</em></td></tr>'."\n";
	} 
	echo '</table><br />'."\n";
}
?>	
	<table width="95%" align="center" class="bodyline" cellpadding="0" cellspacing="1" summary="">
		<tr><th class="cyan" colspan="3"><?php echo _AT('enrolled_courses'); ?></th></tr>
		<tr>
			<th class="cat" scope="col"><?php echo _AT('course_name');  ?></th>
			<th class="cat" scope="col" width="50%"><?php echo _AT('description');  ?></th>
			<th class="cat" scope="col"><?php echo _AT('shortcuts');       ?></th>
		</tr>
<?php


	$sql = "SELECT E.*, C.* FROM ".TABLE_PREFIX."course_enrollment E, ".TABLE_PREFIX."courses C WHERE E.member_id=$_SESSION[member_id] AND E.member_id<>C.member_id AND E.course_id=C.course_id ORDER BY C.title";
	$result = mysql_query($sql,$db);

	$num = mysql_num_rows($result);
	if ($row = mysql_fetch_assoc($result)) {
		do {
			echo "\n".'<tr><td class="row1" width="150" valign="top"><strong>';
			if (($row['approved'] == 'y') || ($row['approved'] == 'a') || ($row['access'] != 'private')) {
				echo '<a href="bounce.php?course='.$row['course_id'].'">'.AT_print($row['title'], 'courses.title').'</a>';
			} else {
				echo AT_print($row['title'], 'courses.title').' <small>'._AT('pending_approval').'</small>';
			}
			echo '</strong></td><td class="row1" valign="top">';			
			echo '<small>';
			echo AT_print($row['description'], 'courses.description');
			if ($row['privileges'] > 0) {
				echo "<br /><br />\n"._AT('role').": <strong>".$row['role']."</strong><br />\n"._AT('privileges').":";
				$comma = '';
				foreach ($_privs as $key => $priv) {				
					if (query_bit($row['privileges'], $key)) { 
						if ($key == AT_PRIV_ENROLLMENT) {
							echo $comma.' <a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('tools/enrollment/index.php').'">'.$priv['name'].'</a>';
						} else if ($key == AT_PRIV_COURSE_EMAIL) {
							echo $comma.' <a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('tools/course_email.php').'">'.$priv['name'].'</a>';
						} else {
							echo $comma.' '.$priv['name'];
						}
						$comma=',';
					}
				}
			}
			echo '</small></td><td class="row1" valign="top">';
			echo '<small>';
		
			if ($row['approved'] == 'y' || $row['approved'] == 'a' || $row['access'] == 'public' || $row['access'] == 'protected') {
				if (defined('AC_PATH') && AC_PATH) {
					echo '&middot; <a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('acollab/bounce.php').'" >'._AT('groups_shortcut').'</a><br />'."\n";
				}

				echo '&middot; <a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('forum/list.php').'">'._AT('forums_shortcut').'</a><br />'."\n";
			}
				echo '&middot; <a href="users/contact_instructor.php?course='.$row['course_id'].'">'._AT('contact_instructor').'</a><br /><br />'."\n";

			echo '&middot; <a href="users/remove_course.php?course='.$row['course_id'].'">'._AT('remove').'</a>';
			echo '</small>';			
			
			echo '</td></tr>'."\n";
			if ($count < $num-1) {
				echo '<tr><td height="1" class="row2" colspan="3"></td></tr>'."\n";
			}
			$count++;
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<tr><td class="row1" colspan="3"><em>'._AT('no_enrolments').'</em></td></tr>'."\n";
	}
?>
	</table>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>
