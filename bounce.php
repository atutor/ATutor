<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.                */
/****************************************************************/
// $Id$

function apply_category_theme($category_id) {
	if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) {
		global $db;

		if ($category_id) {
			// apply the theme for this category:
		    $sql	= "SELECT theme FROM %scourse_cats WHERE cat_id=%d";
			$row = queryDB($sql, array(TABLE_PREFIX, $category_id), TRUE);
			if($row['theme'] !=''){
				$_SESSION['prefs']['PREF_THEME'] = $row['theme'];
			} else {			
				$_SESSION['prefs']['PREF_THEME'] = get_default_theme();
			}
		} else {			
			$_SESSION['prefs']['PREF_THEME'] = get_default_theme();
		}
	}
}

function count_login( ) {
	global $db, $moduleFactory;

	$module =& $moduleFactory->getModule(AT_MODULE_DIR_STANDARD.'/statistics');
	if (!$module->isEnabled()) {
		return;
	}

    $sql = "SELECT * from %scourse_stats WHERE course_id = %d AND login_date = CURDATE()";
    $row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']), TRUE);
    
    if(!$row['course_id']){
        if ($_SESSION['is_guest']) {
            $sql   = "INSERT INTO %scourse_stats VALUES (%d, NOW(), 1, 0)";
        } else {
            $sql    = "INSERT INTO %scourse_stats VALUES (%d, NOW(), 0, 1)";
        }

        $result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
    }else{
		/* that entry already exists, then update it. */
		if ($_SESSION['is_guest']) {
			$sql   = "UPDATE %scourse_stats SET guests=guests+1 WHERE course_id=%d AND login_date=CURDATE()";
			unset($msg);
		} else {
			$sql   = "UPDATE %scourse_stats SET members=members+1 WHERE course_id=%d AND login_date=CURDATE()";
		}
		$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
	}
}

function get_groups($course_id) {
	global $db;

	$groups = array();

	if (authenticate(AT_PRIV_GROUPS, true)) {
		$sql = "SELECT G.group_id FROM %sgroups G INNER JOIN %sgroups_types T USING (type_id) WHERE T.course_id=%d";
		$rows = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $course_id));
	} else {
		$sql = "SELECT G.group_id FROM %sgroups G INNER JOIN (%sgroups_types T, %sgroups_members M) ON (G.type_id=T.type_id AND  G.group_id=M.group_id) WHERE T.course_id=%d AND M.member_id=%d";
		$rows = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX,TABLE_PREFIX, $course_id, $_SESSION['member_id']));
	}

	foreach($rows as $row){
		$groups[$row['group_id']] = $row['group_id'];
	}

	return $groups;
}

$_user_location	= 'public';
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if($_config['just_social'] == 1){
	header('Location: mods/_standard/social/index_mystart.php');
	exit;
}
$set_to_public = false;

if ($_SERVER['PHP_SELF'] == $_base_path."acl.php") {
	//search through the auth table and find password that matches get password
	$key = $addslashes(key($_GET));

	$sql = "SELECT * FROM %scourse_access WHERE password='%s' AND (expiry_date > NOW() OR expiry_date+0 = 0) AND enabled=1";
	$row = queryDB($sql, array(TABLE_PREFIX, $key), TRUE);

	if ($row['password'] != '') {
		$set_to_public = true;
		$_REQUEST['course'] = $row['course_id'];
		$_SESSION['member_id'] = 0;
		$_SESSION['valid_user'] = false;
		$_SESSION['login'] = 'guest';
	}
}


if (isset($_GET['admin']) && isset($_SESSION['is_super_admin'])) {

    $sql = "SELECT login, `privileges`, language FROM %sadmins WHERE login='%s' AND `privileges`>0";
	$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['is_super_admin']), TRUE);

    // update admin login when returning from viewing courses
	if($row['login']){
		$sql = "UPDATE %sadmins SET last_login=NOW() WHERE login='%s'";
		$num_rows = queryDB($sql, array(TABLE_PREFIX, $_SESSION['is_super_admin']));
		
		$_SESSION['login']		= $row['login'];
		$_SESSION['valid_user'] = true;
		$_SESSION['course_id']  = -1;
		$_SESSION['privileges'] = intval($row['privileges']);
		$_SESSION['lang'] = $row['language'];
		unset($_SESSION['prefs']);
		assign_session_prefs(unserialize(stripslashes($_config['pref_defaults'])), 1);
		unset($_SESSION['member_id']);
		unset($_SESSION['is_super_admin']);
        unset($_SESSION['message']['help']);
		write_to_log(AT_ADMIN_LOG_UPDATE, 'admins', $num_rows, $sql);

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
		if ($_config['pretty_url'])
		{
			$orig_url = AT_PRETTY_URL_HANDLER.$_REQUEST['pu'];
			$page = (substr($_REQUEST['pu'], -1) == '/') ? ($orig_url. 'ib/1/') : ($orig_url .'/ib/1/');
		}
		else
			$page = AT_PRETTY_URL_HANDLER.$_REQUEST['pu'] . SEP .'ib=1';
	}
} elseif (!empty($_REQUEST['p'])) {
	//For search
    //p is a relative path, check that.  #4773
    if (strpos($_REQUEST['p'], 'http') !== false) {
        //if not relative, reset it.
        $_REQUEST['p'] = "";
    }
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

if (isset($_REQUEST['course'])) { // is set guests access protected course
	$course	= abs($_REQUEST['course']);
} else if (isset($_REQUEST['p_course'])) { // is set when pretty url is turned on, access public course
	$course	= abs($_REQUEST['p_course']);
} else {
	$course = 0;
}

if (($course === 0) && $_SESSION['valid_user']) {
	$_SESSION['course_id']    = 0;
	$_SESSION['last_updated'] = time()/60 - ONLINE_UPDATE - 1;

	if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) {
		$_SESSION['prefs']['PREF_THEME'] = get_default_theme();
	}
	
	// If instructor with no course, direct to create course screen unless it is disabled
    
    $sql = 'SELECT status FROM %smembers WHERE member_id=%d';
    $row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);

	if ($row['status'] == 3) {
 		
 		$sql = 'SELECT COUNT(*) AS count FROM %scourses WHERE member_id=%d';
  		$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
        $this_count = $row['count'];
		 		
		$sql = "SELECT * FROM %smodules WHERE dir_name ='_core/services' && status ='2'";
		$row = queryDB($sql, array(TABLE_PREFIX), TRUE);
		
		if($row['dir_name']){
		    //This is a Service site 
			$service_site = 1;
		}
  		if ($this_count == 0) {
            if(isset($service_site) && $_config['disable_create'] != 1){
                $msg->addFeedback('CREATE_NEW_COURSE');
                header('Location: mods/_core/services/users/create_course.php');
                exit;
            }else if($_config['disable_create'] != 1){
                $msg->addFeedback('CREATE_NEW_COURSE');
                header('Location: mods/_core/courses/users/create_course.php');
                exit;
            }
        }
    }
    /* http://atutor.ca/atutor/mantis/view.php?id=4587
     * 	for users with no enrolled courses, default to the Browse Courses screen instead of My Courses. 
     */
    $sql = 'SELECT COUNT(*) AS count FROM %scourse_enrollment WHERE member_id=%d';
    $row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
    if ($row['count'] == 0) {
        header('Location: users/browse.php');
        exit;
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
		$_SESSION['prefs']['PREF_THEME'] = get_default_theme();
	}

	header('Location: users/index.php');
	exit; 
}
/*
Check the release and end dates on a course, redirect to login or MyStart if not released, or expired.
*/
$sql	= "SELECT member_id, content_packaging, cat_id, access, title, UNIX_TIMESTAMP(release_date) AS u_release_date, UNIX_TIMESTAMP(end_date) AS u_end_date FROM %scourses WHERE course_id=%d";
$row = queryDB($sql,array(TABLE_PREFIX, $course), TRUE);

if (!$row['member_id']) {
	$msg->addError('ITEM_NOT_FOUND');
	if ($_SESSION['member_id']) {
		header('Location: '.AT_BASE_HREF.'users/index.php');
	} else {
		header('Location: '.AT_BASE_HREF.'login.php');
	}
	exit;
}

if (!$_SESSION['member_id']) {
	assign_session_prefs(unserialize(stripslashes($_config['pref_defaults'])), 1);
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
		if ($_GET['f']) {
			$dest = './'.$page.'?f='.$addslashes($_GET['f']);
		} /* else */
		$dest = './'.$page;
		
		apply_category_theme($row['cat_id']);

		if (!$_SESSION['valid_user'] && ($row['u_release_date'] < time()) && (!$row['u_end_date'] || $row['u_end_date'] > time())) {
			$_SESSION['course_id']	  = $course;
			/* guest login */
			$_SESSION['login']		= 'guest';
			$_SESSION['valid_user']	= false;
			$_SESSION['member_id']	= 0;
			$_SESSION['is_admin']	= false;
			$_SESSION['is_guest']	= true;
			$_SESSION['course_title'] = $row['title'];

			/* add guest login to counter: */
			count_login();
			if ($_config['pretty_url'])
			{
				if (!strpos($dest, '/p_course/')) $dest .= '/p_course/'.$course;
				header('Location: '.$dest);
				exit;
			}
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
		
		$sql	= "SELECT * FROM %scourse_enrollment WHERE member_id=%d AND course_id=%d";
		$row2 = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $course), TRUE);		
		
		if ($row2['member_id']) {
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

		header('Location: '.$dest);
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
		
		$sql	= "SELECT * FROM %scourse_enrollment WHERE member_id=%d AND course_id=%d";
		$row2 = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $course), TRUE);
			
		if ($row2['member_id']) {
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

			$sql	= "SELECT last_cid FROM %scourse_enrollment WHERE member_id=%d AND course_id=%d";
			$row2 = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $course), TRUE);

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
		
		$sql	= "SELECT * FROM %scourse_enrollment WHERE member_id=%d AND course_id=%d";
		$row2 = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $course), TRUE);

		if (!$row2['member_id']) {
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
