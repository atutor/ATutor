<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

function count_login( ) {
	global $db, $moduleFactory;

	$module =& $moduleFactory->getModule(AT_MODULE_DIR_STANDARD.'/statistics');
	if (!$module->isEnabled()) {
		return;
	}
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
//exit;
if($_REQUEST['p']) {
	$page = urldecode($_REQUEST['p']);
	if (substr($page, 0, 1) == '/') {
		$page = substr($page, 1);
	}
} else {
	$page = 'index.php';
}

$_SESSION['enroll']		= AT_ENROLL_NO;
$_SESSION['s_cid']		= 0;
$_SESSION['privileges'] = 0;
$_SESSION['is_admin']   = false;

if ($_SESSION['course_id'] == -1) {
	unset($_SESSION['valid_user']);
	unset($_SESSION['is_guest']);
	unset($_SESSION['login']);
	unset($_SESSION['is_admin']);
	unset($_SESSION['course_id']);
}

if ($_GET['course'] != '') {
	$course	= intval($_GET['course']);
} else {
	$course	= intval($_POST['course']);
}


if (($course === 0) && $_SESSION['valid_user']) {
	$_SESSION['course_id']    = 0;
	$_SESSION['last_updated'] = time()/60 - ONLINE_UPDATE - 1;

	if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) {
		$th = get_default_theme();
		$_SESSION['prefs']['PREF_THEME'] = $th['dir_name'];
	}

	header('Location: users/index.php');
	exit;
} else if (($course === 0) && ($_SESSION['login'] == 'guest')) {

	if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) {
		$th = get_default_theme();
		$_SESSION['prefs']['PREF_THEME'] = $th['dir_name'];
	}

	header('Location: users/index.php');
	exit;
} else if ($course == -1) {
	$_SESSION['course_id']    = 0;
	$_SESSION['last_updated'] = time()/60 - ONLINE_UPDATE - 1;

	if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) {
		$th = get_default_theme();
		$_SESSION['prefs']['PREF_THEME'] = $th['dir_name'];
	}

	header('Location: users/index.php');
	exit; 
}

$sql	= "SELECT member_id, content_packaging, cat_id, access, title FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
$result = mysql_query($sql,$db);
if ($row = mysql_fetch_assoc($result)) {
	$owner_id = $row['member_id'];
	$_SESSION['packaging'] = $row['content_packaging'];

	if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) {
		if ($row['cat_id']) {
			// apply the theme for this category:
			$sql	= "SELECT theme FROM ".TABLE_PREFIX."course_cats WHERE cat_id=$row[cat_id]";
			$result = mysql_query($sql, $db);
			if (($cat_row = mysql_fetch_assoc($result)) && $cat_row['theme']) {
				$_SESSION['prefs']['PREF_THEME'] = $cat_row['theme'];
			} else {			
				$th = get_default_theme();
				$_SESSION['prefs']['PREF_THEME'] = $th['dir_name'];
			}
		} else {			
			$th = get_default_theme();
			$_SESSION['prefs']['PREF_THEME'] = $th['dir_name'];
		}
	}

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
					$_SESSION['enroll']	  = AT_ENROLL_YES;
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
				$_SESSION['enroll'] = AT_ENROLL_YES;
				$_SESSION['s_cid']  = $row2['last_cid'];
				$_SESSION['privileges'] = $row2['privileges'];
			}

			/* update users_online	*/
			add_user_online();

			if ($_GET['f']) {
				header('Location: ./'.$page.'?f='.$addslashes($_GET['f']));
				exit;
			} /* else */
			header('Location: ./'.$page);
			exit;

			break;

		case 'protected':
			if (!$_SESSION['valid_user']) {
				header('Location: ./login.php?course='.intval($course));
				exit;

			} else {
				/* we're already logged in */
				$_SESSION['course_id'] = $course;

				/* check if we're an admin here */
				if ($owner_id == $_SESSION['member_id']) {
					$_SESSION['is_admin'] = true;
					$_SESSION['enroll']	  = AT_ENROLL_YES;

				} else {
					$_SESSION['is_admin'] = false;
					/* add member login to counter: */
					count_login();
				}

				$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
				$result = mysql_query($sql, $db);
				if ($row2 = mysql_fetch_assoc($result)) {
					/* we have requested or are enrolled in this course */
					$_SESSION['enroll'] = AT_ENROLL_YES;
					$_SESSION['s_cid']  = $row2['last_cid'];
					$_SESSION['privileges'] = $row2['privileges'];
				}

				$_SESSION['course_title'] = $row['title'];

				/* update users_online	*/
				add_user_online();

				if ($_GET['f']) {
					header('Location: ./'.$page.'?f='.$addslashes($_GET['f']));
					exit;
				} /* else */
				header('Location: ./'.$addslashes($page));
				exit;
			}

			break;

		case 'private':
			if (!$_SESSION['valid_user']) {
				/* user not logged in: */
				Header('Location: ./login.php?course='.intval($course));
				exit;
			} else {

				if ($owner_id == $_SESSION['member_id']) {
					/* we own this course. so we dont have to enroll */

					$_SESSION['is_admin']  = true;
					$_SESSION['course_id'] = $course;
					$_SESSION['course_title'] = $row['title'];
					$_SESSION['enroll']	  = AT_ENROLL_YES;

					/* update users_online */
					add_user_online();

					if ($_GET['f']) {
						header('Location: ./'.$page.'?f='.$addslashes($_GET['f']));
						exit;
					} /* else */
					header('Location: ./'.$addslashes($page));
					exit;
				}

				/* check if we're enrolled */
				$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
				$result = mysql_query($sql, $db);

				if ($row2 = mysql_fetch_assoc($result)) {
					/* we have requested or are enrolled in this course */

					$_SESSION['enroll'] = AT_ENROLL_YES;
					$_SESSION['s_cid']  = $row2['last_cid'];

					if ($row2['approved'] == 'y' || $row2['approved'] == 'a') {
						/* enrollment has been approved or student is alumni */

						if ($row2['approved'] == 'a') {
							$_SESSION['enroll'] = AT_ENROLL_ALUMNUS;
						}

						/* we're already logged in */
						$_SESSION['course_id'] = $course;

						/* check if we're an admin here */
						$_SESSION['privileges'] = $row2['privileges'];
						$_SESSION['course_title'] = $row['title'];

						/* update users_online			*/
						add_user_online();

						/* add member login to counter: */
						count_login();

						if($_GET['f']){
							header('Location: '.$page.'?f='.$addslashes($_GET['f']));
							exit;
						} /* else */
						header('Location: '.$addslashes($page));
						exit;

					} else {
						/* we have not been approved to enroll in this course */

						$_SESSION['course_id'] = 0;
						header('Location: users/private_enroll.php?course='.intval($course));
						exit;
					}

				} else {
					/* we have not requested enrollment in this course */
					$_SESSION['course_id'] = 0;
					header('Location: users/private_enroll.php?course='.intval($course));
					exit;
				}
			}
		break;
	}
} 

unset($_SESSION);
$_SESSION['language'] = DEFAULT_LANGUAGE;

if (!isset($_SESSION['course_id'])) {
	header('Location: login.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors('NO_SUCH_COURSE');
require(AT_INCLUDE_PATH.'footer.inc.php');

?>