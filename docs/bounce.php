<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: bounce.php,v 1.13 2004/04/19 17:51:12 boonhau Exp $

function count_login( ) {
	global $db;

	if ($_SESSION['is_guest']) {
	    $sql   = "INSERT INTO ".TABLE_PREFIX."course_stats VALUES ($_SESSION[course_id], NOW(), 1, 0)";
	} else {
	   $sql    = "INSERT INTO ".TABLE_PREFIX."course_stats VALUES ($_SESSION[course_id], NOW(), 0, 1)";
	}

    $result = @mysql_query($sql, $db);

    if (!$result) {
		/* that entry already exists, then update it. */
		if ($_SESSION['is_guest']) {
			$sql   = "UPDATE ".TABLE_PREFIX."course_stats SET guests=guests+1 WHERE course_id=$_SESSION[course_id] AND login_date=NOW()";
		} else {
			$sql   = "UPDATE ".TABLE_PREFIX."course_stats SET members=members+1 WHERE course_id=$_SESSION[course_id] AND login_date=NOW()";
		}
		$result = @mysql_query($sql, $db);
	}
}

$_user_location	= 'public';
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if($_REQUEST['p']) {
	$page=urldecode($_REQUEST['p']);
} else {
	$page='index.php';
}

$_SESSION['enroll']		 = false;
$_SESSION['from_cid']	 = 0;
$_SESSION['s_cid']		 = 0;
$_SESSION['prefs_saved'] = '';
$_SESSION['privileges'] = 0;
$_SESSION['is_admin'] = false;

if ($_GET['course'] != '') {
	$course	= intval($_GET['course']);
} else {
	$course	= intval($_POST['course']);
}


if (($course === 0) && ($_SESSION['valid_user'])) {
	$_SESSION['course_id']    = 0;
	$_SESSION['last_updated'] = time()/60 - ONLINE_UPDATE - 1;
	header('Location: users/index.php');
	exit;
} else if ($course == -1) {
	$_SESSION['course_id']    = 0;
	$_SESSION['last_updated'] = time()/60 - ONLINE_UPDATE - 1;
	header('Location: users/index.php');
	exit; 
}

$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
$result = mysql_query($sql,$db);

if ($row = mysql_fetch_assoc($result)) {
	$owner_id = $row['member_id'];
	$tracking = $row['tracking'];
	$_SESSION['packaging'] = $row['content_packaging'];

	$_SESSION['track_me'] = ($tracking == 'on') ? 1 : 0;

	switch ($row['access']){
		case 'public':

			$_SESSION['course_id']	  = $course;

			if (!$_SESSION['valid_user']) {
				/* guest login */
				$_SESSION['login']		= 'guest';
				$_SESSION['valid_user']	= false;
				$_SESSION['member_id']	= 0;
				$_SESSION['is_admin']	= false;
				$_SESSION['is_guest']	= true;
	
				/* add guest login to counter: */
				count_login();
			} else {
				/* check if we're an admin here */
				if ($owner_id == $_SESSION['member_id']) {
					$_SESSION['is_admin'] = true;
					$_SESSION['enroll']	  = true;
				} else {
					$_SESSION['is_admin'] = false;

					/* add member login to counter: */
					count_login();
				}
			}

			/* title wont be needed. comes from the cache. */
			$_SESSION['course_title'] = $row['title'];

			$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
			$result = mysql_query($sql, $db);
			if ($row2 = mysql_fetch_assoc($result)) {
				/* we have requested or are enrolled in this course */
				$_SESSION['enroll'] = true;
				$_SESSION['s_cid']  = $row2['last_cid'];
				$_SESSION['privileges'] = $row2['privileges'];
			}

			/* update users_online	*/
			add_user_online();

			/* get prefs:			*/
			$sql	= "SELECT preferences FROM ".TABLE_PREFIX."preferences WHERE member_id=$_SESSION[member_id] AND course_id=$course";
			$result = mysql_query($sql, $db);
			if ($row2 = mysql_fetch_array($result)) {
				assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
			} else {
				$sql	= "SELECT preferences FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
				$result = mysql_query($sql, $db);
				if ($row2 = mysql_fetch_array($result)) {
					assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
				}
			}

			if ($_GET['f']) {
				header('Location: ./'.$page.'?f='.$_GET['f'].SEP.'g=30');
				exit;
			} /* else */
			Header('Location: ./'.$page.'?g=30');
			exit;

			break;

		case 'protected':
			if (!$_SESSION['valid_user']) {
				header('Location: ./login.php?course='.$course);
				exit;

			} else {
				/* we're already logged in */
				$_SESSION['course_id'] = $course;

				/* check if we're an admin here */
				if ($owner_id == $_SESSION['member_id']) {
					$_SESSION['is_admin'] = true;
					$_SESSION['enroll']	  = true;

				} else {
					$_SESSION['is_admin'] = false;
					/* add member login to counter: */
					count_login();
				}

				$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
				$result = mysql_query($sql, $db);
				if ($row2 = mysql_fetch_assoc($result)) {
					/* we have requested or are enrolled in this course */
					$_SESSION['enroll'] = true;
					$_SESSION['s_cid']  = $row2['last_cid'];
					$_SESSION['privileges'] = $row2['privileges'];
				}

				$_SESSION['course_title'] = $row['title'];

				/* update users_online	*/
				add_user_online();

				/* get prefs:			*/
				$sql	= "SELECT preferences FROM ".TABLE_PREFIX."preferences WHERE member_id=$_SESSION[member_id] AND course_id=$course";
				$result = mysql_query($sql, $db);
				if ($row2 = mysql_fetch_assoc($result)) {
					assign_session_prefs(unserialize(stripslashes($row2['preferences'])));

				} else {
					$sql	= "SELECT preferences FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
					$result = mysql_query($sql, $db);
					if ($row2 = mysql_fetch_assoc($result)) {
						assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
					}
				}

				if ($_GET['f']) {
					header('Location: ./'.$page.'?f='.$_GET['f'].SEP.'g=30');
					exit;
				} /* else */
				header('Location: ./'.$page.'?g=30');
				exit;
			}

			break;

		case 'private':
			if (!$_SESSION['valid_user']) {
				/* user not logged in: */
				Header('Location: ./login.php?course='.$course);
				exit;
			} else {

				if ($owner_id == $_SESSION['member_id']) {
					/* we own this course. so we dont have to enroll */

					$_SESSION['is_admin']  = true;
					$_SESSION['course_id'] = $course;
					$_SESSION['course_title'] = $row['title'];
					$_SESSION['enroll']	  = true;

					/* update users_online */
					add_user_online();

					/* get prefs:			*/
					$sql	= "SELECT preferences FROM ".TABLE_PREFIX."preferences WHERE member_id=$_SESSION[member_id] AND course_id=$course";
					$result = mysql_query($sql, $db);
					if ($row2 = mysql_fetch_assoc($result)) {
						assign_session_prefs(unserialize(stripslashes($row2['preferences'])));

					} else {
						$sql	= "SELECT preferences FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
						$result = mysql_query($sql, $db);
						if ($row2 = mysql_fetch_assoc($result)) {
							assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
						}
					}

					if ($_GET['f']) {
						header('Location: ./'.$page.'?f='.$_GET['f']);
						exit;
					} /* else */
					header('Location: ./'.$page.'');
					exit;
				}

				/* check if we're enrolled */
				$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
				$result = mysql_query($sql, $db);

				if ($row2 = mysql_fetch_assoc($result)) {
					/* we have requested or are enrolled in this course */

					$_SESSION['enroll'] = true;
					$_SESSION['s_cid']  = $row2['last_cid'];

					if ($row2['approved'] == 'y') {
						/* enrollment has been approved */

						/* we're already logged in */
						$_SESSION['course_id'] = $course;

						/* check if we're an admin here */
						$_SESSION['privileges'] = $row2['privileges'];
						$_SESSION['course_title'] = $row['title'];

						/* update users_online			*/
						add_user_online();

						/* add member login to counter: */
						count_login();

						/* get prefs:					*/
						$sql	= "SELECT preferences FROM ".TABLE_PREFIX."preferences WHERE member_id=$_SESSION[member_id] AND course_id=$course";
						$result = mysql_query($sql, $db);
						if ($row2 = mysql_fetch_assoc($result)) {
							assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
						} else {
							$sql	= "SELECT preferences FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
							$result = mysql_query($sql, $db);
							if ($row2 = mysql_fetch_assoc($result)) {
								assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
							}
						}

						if($_GET['f']){
							header('Location: '.$page.'?f='.$_GET['f'].SEP.'g=30');
							exit;
						} /* else */
						header('Location: '.$page.'?g=30');
						exit;

					} else {
						/* we have not been approved to enroll in this course */

						$_SESSION['course_id'] = 0;
						header('Location: users/private_enroll.php?course='.$course);
						exit;
					}

				} else {
					/* we have not requested enrollment in this course */
					$_SESSION['course_id'] = 0;
					header('Location: users/private_enroll.php?course='.$course);
					exit;
				}
			}
		break;
	}
} /* else */

require(AT_INCLUDE_PATH.'basic_html/header.php');
$errors[] = AT_ERROR_NO_SUCH_COURSE;
print_errors($errors);
require(AT_INCLUDE_PATH.'basic_html/footer.php');

?>