<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

function apply_category_theme($category_id) {
	if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) {
		global $db;

		if ($category_id) {
			// apply the theme for this category:
			$sql	= "SELECT theme FROM ".TABLE_PREFIX."course_cats WHERE cat_id=$category_id";
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
}

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
			$sql   = "UPDATE ".TABLE_PREFIX."course_stats SET guests=guests+1 WHERE course_id=$_SESSION[course_id] AND login_date=CURDATE()";
		} else {
			$sql   = "UPDATE ".TABLE_PREFIX."course_stats SET members=members+1 WHERE course_id=$_SESSION[course_id] AND login_date=CURDATE()";
		}
		$result = @mysql_query($sql, $db);
	}
}

function get_groups($course_id) {
	global $db;

	$groups = array();

	if (authenticate(AT_PRIV_GROUPS, true)) {
		$sql = "SELECT G.group_id FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types T USING (type_id) WHERE T.course_id=$course_id";
	} else {
		$sql = "SELECT G.group_id FROM ".TABLE_PREFIX."groups G INNER JOIN (".TABLE_PREFIX."groups_types T, ".TABLE_PREFIX."groups_members M) ON (G.type_id=T.type_id AND  G.group_id=M.group_id) WHERE T.course_id=$course_id AND M.member_id=$_SESSION[member_id]";
	}
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$groups[$row['group_id']] = $row['group_id'];
	}

	return $groups;
}

$_user_location	= 'public';
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if($_config['just_social'] == 1){
	header('Location: mods/_standard/social/index.php');
	exit;
}
$set_to_public = false;
if ($_SERVER['PHP_SELF'] == $_base_path."acl.php") {
	//search through the auth table and find password that matches get password
	$key = $addslashes(key($_GET));
	$sql = "SELECT * FROM ".TABLE_PREFIX."course_access WHERE password='$key' AND (expiry_date > NOW() OR expiry_date+0 = 0) AND enabled=1";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$set_to_public = true;
		$_GET['course'] = $row['course_id'];
		$_SESSION['member_id'] = 0;
		$_SESSION['valid_user'] = false;
		$_SESSION['login'] = 'guest';
	}
}


if (isset($_GET['admin']) && isset($_SESSION['is_super_admin'])) {
	$sql = "SELECT login, `privileges`, language FROM ".TABLE_PREFIX."admins WHERE login='$_SESSION[is_super_admin]' AND `privileges`>0";
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
		$sql = "UPDATE ".TABLE_PREFIX."admins SET last_login=NOW() WHERE login='$_SESSION[is_super_admin]'";
		mysql_query($sql, $db);

		$_SESSION['login']		= $row['login'];
		$_SESSION['valid_user'] = true;
		$_SESSION['course_id']  = -1;
		$_SESSION['privileges'] = intval($row['privileges']);
		$_SESSION['lang'] = $row['language'];
		assign_session_prefs(unserialize(stripslashes($_config['pref_defaults'])));
		unset($_SESSION['member_id']);
		unset($_SESSION['is_super_admin']);

		write_to_log(AT_ADMIN_LOG_UPDATE, 'admins', mysql_affected_rows($db), $sql);

		$msg->addFeedback('LOGIN_SUCCESS');

		header('Location: admin/index.php');
		exit;
	}
}

if (!empty($_REQUEST['pu'])) {
	//request ib stands for 'is bounced', this is to avoid the infinite 302 redirect
	//A better way to deal with this rather than using querystring? (Session won't work)
	//Session doesn't work,leads to bounce out error as well.
	if (!empty($_REQUEST['ib'])) {
		return;
	}
	
	//for pretty url iff mod_rewrite is not on
	if ($_config['apache_mod_rewrite'] > 0){
		//URL are in pretty format, but not in .htaccess RewriteRule format
		//http://www.atutor.ca/atutor/mantis/view.php?id=3426
		$page = url_rewrite($_REQUEST['pu'], AT_PRETTY_URL_NOT_HEADER, true) . '/ib/1';
	} else {
		$page = AT_PRETTY_URL_HANDLER.$_REQUEST['pu'] . SEP .'ib=1';
	}
} elseif (!empty($_REQUEST['p'])) {
	//For search
	$page = urldecode($_REQUEST['p']);
} elseif (($_config['pretty_url'] > 0) && preg_match('/bounce.php\?course=([\d]+)$/', $_SERVER['REQUEST_URI'])==1) {
	//for browse, and my start page url rewrite.	
	$page = url_rewrite($_SERVER['REQUEST_URI'], AT_PRETTY_URL_NOT_HEADER, true).'/index.php';	//force overwrite
} else {
	//handles jump menu
	if (isset($_POST['jump']) && abs($_POST['course']) > 0){
		$_SESSION['course_id'] = abs($_POST['course']);
	}
	$page = url_rewrite('index.php');
}

if (substr($page, 0, 1) == '/') {
	$page = substr($page, 1);
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

if (isset($_GET['course'])) {
	$course	= abs($_GET['course']);
} else if (isset($_POST['course'])) {
	$course	= abs($_POST['course']);
} else {
	$course = 0;
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
} else if (($course === 0) && !$_SESSION['valid_user']) { // guests
	header('Location: '.AT_BASE_HREF.'login.php');
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

$sql	= "SELECT member_id, content_packaging, cat_id, access, title, UNIX_TIMESTAMP(release_date) AS u_release_date, UNIX_TIMESTAMP(end_date) AS u_end_date FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
$result = mysql_query($sql,$db);
if (!$row = mysql_fetch_assoc($result)) {
	$msg->addError('ITEM_NOT_FOUND');
	if ($_SESSION['member_id']) {
		header('Location: '.AT_BASE_HREF.'users/index.php');
	} else {
		header('Location: '.AT_BASE_HREF.'login.php');
	}
	exit;
}

if (!$_SESSION['member_id']) {
	assign_session_prefs(unserialize(stripslashes($_config['pref_defaults'])));
}

$owner_id = $row['member_id'];
$_SESSION['packaging'] = $row['content_packaging'];

$_SESSION['groups'] = array();
unset($_SESSION['fs_owner_type']);
unset($_SESSION['fs_owner_id']);
unset($_SESSION['fs_folder_id']);

//check for acl var
if ($set_to_public) {
	$row['access'] = "public";
}

switch ($row['access']){
	case 'public':
		apply_category_theme($row['cat_id']);

		if (!$_SESSION['valid_user'] && ($row['u_release_date'] < time()) && (!$row['u_end_date'] || $row['u_end_date'] > time())) {
			$_SESSION['course_id']	  = $course;
			/* guest login */
			$_SESSION['login']		= 'guest';
			$_SESSION['valid_user']	= false;
			$_SESSION['member_id']	= 0;
			$_SESSION['is_admin']	= false;
			$_SESSION['is_guest']	= true;

			/* add guest login to counter: */
			count_login();
		} else if (!$_SESSION['valid_user']) {
			if ($row['u_release_date'] > time()) {
				$msg->addError(array('COURSE_NOT_RELEASED', AT_Date(_AT('announcement_date_format'), $row['u_release_date'], AT_DATE_UNIX_TIMESTAMP)));
			} else {
				$msg->addError(array('COURSE_ENDED', AT_Date(_AT('announcement_date_format'), $row['u_end_date'], AT_DATE_UNIX_TIMESTAMP)));
			}
			header('Location: '.AT_BASE_HREF.'browse.php');
			exit;

		} else {
			$_SESSION['course_id']	  = $course;
			/* check if we're an admin here */
			if ($owner_id == $_SESSION['member_id']) {
				$_SESSION['is_admin'] = true;
				$_SESSION['enroll']	  = AT_ENROLL_YES;
			} else {
				$_SESSION['is_admin'] = false;
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

		if (($row['u_release_date'] > time()) && !($_SESSION['is_admin'] || $_SESSION['privileges'])) {
			$msg->addError(array('COURSE_NOT_RELEASED', AT_Date(_AT('announcement_date_format'), $row['u_release_date'], AT_DATE_UNIX_TIMESTAMP)));
			header('Location: '.AT_BASE_HREF.'bounce.php?course=0');
			exit;
		} else if ($row['u_release_date'] > time()) {
			$msg->addInfo(array('COURSE_RELEASE', AT_Date(_AT('announcement_date_format'), $row['u_release_date'], AT_DATE_UNIX_TIMESTAMP)));

		} else if ($row['u_end_date'] && ($row['u_end_date'] < time()) && !($_SESSION['is_admin'] || $_SESSION['privileges'])) {
			$msg->addError(array('COURSE_ENDED', AT_Date(_AT('announcement_date_format'), $row['u_end_date'], AT_DATE_UNIX_TIMESTAMP)));
			header('Location: '.AT_BASE_HREF.'bounce.php?course=0');
			exit;
		} else if ($row['u_end_date'] && $row['u_end_date'] < time()) {
			$msg->addInfo(array('COURSE_ENDED', AT_Date(_AT('announcement_date_format'), $row['u_end_date'], AT_DATE_UNIX_TIMESTAMP)));
		}

		/* add member login to counter: */
		if (!$_SESSION['is_admin'] && $_SESSION['member_id'] > 0) {
			count_login();
		}

		/* update users_online	*/
		add_user_online();

		$_SESSION['groups'] = get_groups($course);

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
		} /* else */
		/* we're already logged in */
		$_SESSION['course_id'] = $course;

		apply_category_theme($row['cat_id']);

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

		if (($row['u_release_date'] > time()) && !($_SESSION['is_admin'] || $_SESSION['privileges'])) {
			$msg->addError(array('COURSE_NOT_RELEASED', AT_Date(_AT('announcement_date_format'), $row['u_release_date'], AT_DATE_UNIX_TIMESTAMP)));
			header('Location: '.AT_BASE_HREF.'bounce.php?course=0');
			exit;
		} else if ($row['u_release_date'] > time()) {
			$msg->addInfo(array('COURSE_RELEASE', AT_Date(_AT('announcement_date_format'), $row['u_release_date'], AT_DATE_UNIX_TIMESTAMP)));

		} else if ($row['u_end_date'] && ($row['u_end_date'] < time()) && !($_SESSION['is_admin'] || $_SESSION['privileges'])) {
			$msg->addError(array('COURSE_ENDED', AT_Date(_AT('announcement_date_format'), $row['u_end_date'], AT_DATE_UNIX_TIMESTAMP)));
			header('Location: '.AT_BASE_HREF.'bounce.php?course=0');
			exit;
		} else if ($row['u_end_date'] && $row['u_end_date'] < time()) {
			$msg->addInfo(array('COURSE_ENDED', AT_Date(_AT('announcement_date_format'), $row['u_end_date'], AT_DATE_UNIX_TIMESTAMP)));
		}


		$_SESSION['course_title'] = $row['title'];

		/* update users_online	*/
		add_user_online();

		$_SESSION['groups'] = get_groups($course);

		if ($_GET['f']) {
			header('Location: ./'.$page.'?f='.$addslashes($_GET['f']));
			exit;
		} /* else */
		header('Location: ./'.$addslashes($page));
		exit;

		break;

	case 'private':
		if (!$_SESSION['valid_user']) {
			/* user not logged in: */
			header('Location: ./login.php?course='.intval($course));
			exit;
		} /* else */

		if ($owner_id == $_SESSION['member_id']) {
			/* we own this course. so we dont have to enroll or get the groups */

			$_SESSION['is_admin']  = true;
			$_SESSION['course_id'] = $course;
			$_SESSION['course_title'] = $row['title'];
			$_SESSION['enroll']	  = AT_ENROLL_YES;

			$sql	= "SELECT last_cid FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
			$result = mysql_query($sql, $db);
			$row2 = mysql_fetch_assoc($result);

			$_SESSION['s_cid']  = $row2['last_cid'];

			/* update users_online */
			add_user_online();

			apply_category_theme($row['cat_id']);

			$_SESSION['groups'] = get_groups($course);

			if (!empty($_GET['f'])) {
				header('Location: ./'.$page.'?f='.$addslashes($_GET['f']));
				exit;
			} /* else */
			if ($row['u_release_date'] > time()) {
				$msg->addInfo(array('COURSE_RELEASE', AT_Date(_AT('announcement_date_format'), $row['u_release_date'], AT_DATE_UNIX_TIMESTAMP)));
			} else if ($row['u_end_date'] && $row['u_end_date'] < time()) {
				$msg->addInfo(array('COURSE_ENDED', AT_Date(_AT('announcement_date_format'), $row['u_end_date'], AT_DATE_UNIX_TIMESTAMP)));
			}
			header('Location: ./'.$addslashes($page));
			exit;
		}

		/* check if we're enrolled */
		$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
		$result = mysql_query($sql, $db);

		if (!$row2 = mysql_fetch_assoc($result)) {
			/* we have not requested enrollment in this course */
			$_SESSION['course_id'] = 0;
			header('Location: users/private_enroll.php?course='.intval($course));
			exit;
		} /* else */

		if (($row['u_release_date'] > time()) && !($_SESSION['is_admin'] || $_SESSION['privileges'])) {
			$msg->addError(array('COURSE_NOT_RELEASED', AT_Date(_AT('announcement_date_format'), $row['u_release_date'], AT_DATE_UNIX_TIMESTAMP)));
			header('Location: '.AT_BASE_HREF.'bounce.php?course=0');
			exit;
		} else if ($row['u_release_date'] > time()) {
			$msg->addInfo(array('COURSE_RELEASE', AT_Date(_AT('announcement_date_format'), $row['u_release_date'], AT_DATE_UNIX_TIMESTAMP)));

		} else if ($row['u_end_date'] && ($row['u_end_date'] < time()) && !($_SESSION['is_admin'] || $_SESSION['privileges'])) {
			$msg->addError(array('COURSE_ENDED', AT_Date(_AT('announcement_date_format'), $row['u_end_date'], AT_DATE_UNIX_TIMESTAMP)));
			header('Location: '.AT_BASE_HREF.'bounce.php?course=0');
			exit;
		} else if ($row['u_end_date'] && $row['u_end_date'] < time()) {
			$msg->addInfo(array('COURSE_ENDED', AT_Date(_AT('announcement_date_format'), $row['u_end_date'], AT_DATE_UNIX_TIMESTAMP)));
		}
		/* we have requested or are enrolled in this course */

		apply_category_theme($row['cat_id']);

		$_SESSION['enroll'] = AT_ENROLL_YES;
		$_SESSION['s_cid']  = $row2['last_cid'];

		if ($row2['approved'] == 'n') {
			/* we have not been approved to enroll in this course */
			$_SESSION['course_id'] = 0;
			header('Location: users/private_enroll.php?course='.$course);
			exit;
		} /* else */

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

		$_SESSION['groups'] = get_groups($course);

		/* add member login to counter: */
		count_login();

		if($_GET['f']){
			header('Location: '.$page.'?f='.$addslashes($_GET['f']));
			exit;
		} /* else */
		header('Location: '.$addslashes($page));
		exit;
	break;
} // end switch
 

?>