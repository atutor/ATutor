<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

function createCourse($_POST, $isadmin = FALSE) {
	global $addslashes;
	global $db;

   	$_POST['notify']	= intval($_POST['notify']);
	$_POST['hide']		= intval($_POST['hide']);
	$_POST['title']	= trim($_POST['title']);

	if ($_POST['title'] == '') {
		$errors[]=AT_ERROR_SUPPLY_TITLE;
		return $errors;
	} else {	
		$sql2	= "SELECT preferences FROM ".TABLE_PREFIX."theme_settings WHERE theme_id='4'";
		$result2	= mysql_query($sql2, $db);
		while($row = mysql_fetch_array($result2)){
			$course_default_prefs = $row['preferences'];
		}
	 	$_POST['notify'] = intval($_POST['notify']);

		$_POST['access']      = $addslashes($_POST['access']);
		$_POST['title']       = $addslashes($_POST['title']);
		$_POST['description'] = $addslashes($_POST['description']);
		$_POST['hide']        = $addslashes($_POST['hide']);
		$_POST['pri_lang']	       = $addslashes($_POST['pri_lang']);

		$sql = "INSERT INTO ".TABLE_PREFIX."courses VALUES (0,$_SESSION[member_id], '$_POST[category_parent]', '$_POST[packaging]', '$_POST[access]', NOW(), '$_POST[title]', '$_POST[description]', $_POST[notify], '".AT_COURSESIZE_DEFAULT."', ".AT_FILESIZE_DEFAULT.", $_POST[hide], '', '','','', '', '', 'off', '$_POST[pri_lang]')";

		$result = mysql_query($sql, $db);

		if (!$result) {
			echo 'DB Error';
			exit;
		}

		$course = mysql_insert_id($db);

		$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES($_SESSION[member_id], $course, 'y', 0, '"._AT('instructor')."', 0)";
		$result	= mysql_query($sql, $db);

		// create the course content directory
		$path = AT_CONTENT_DIR . $course . '/';
		@mkdir($path, 0700);
		@copy(AT_CONTENT_DIR . 'index.html', AT_CONTENT_DIR . $course . '/index.html');

		// create the course backup directory
		$path = AT_BACKUP_DIR . $course . '/';
		@mkdir($path, 0700);
		@copy(AT_CONTENT_DIR . 'index.html', AT_BACKUP_DIR . $course . '/index.html');

		$_SESSION['is_admin'] = 1;

		/* insert some default content: */
		if (isset($_POST['extra_content'])) {
			$cid = $contentManager->addContent($course, 0, 1,_AT('welcome_to_atutor'),
												addslashes(_AT('this_is_content')),
												'', '', 1, date('Y-m-d H:00:00'), 0);

			$announcement = _AT('default_announcement');
		
			$sql	= "INSERT INTO ".TABLE_PREFIX."news VALUES (0, $course, $_SESSION[member_id], NOW(), 1, '"._AT('welcome_to_atutor')."', '$announcement')";
			$result = mysql_query($sql,$db);

			// create forum for Welcome Course
			$sql	= "INSERT INTO ".TABLE_PREFIX."forums VALUES (0, $course, '"._AT('forum_general_discussion')."', '', 0, 0, NOW())";
			$result = mysql_query($sql,$db);
		}

		cache_purge('system_courses','system_courses');
		return;
	}
}

//-------------------------------------------------------

function editCourse($_POST, $isadmin = FALSE) {
	global $addslashes;
	global $db;

  if ($_POST['title'] == '') {
	$errors[] = AT_ERROR_TITLE_EMPTY;
  }  else {	
	$course_id = intval($_POST['course_id']);
	$notify	= intval($_POST['notify']);
	$hide		= intval($_POST['hide']);
	$instructor= intval($_POST['instructor']);

	/* if the access is changed from private to public/protected then automatically enroll all those waiting for approval. */
	if ( ($_POST['old_access'] == 'private') && ($_POST['access'] != 'private') ) {
		$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved='y' WHERE course_id=$course";
		$result = mysql_query($sql, $db);
	}

	if ($isadmin) {
		$quota    = intval($_POST['quota']);
		$filesize = intval($_POST['filesize']);
		$cat	  = intval($_POST['category_parent']);
		$_POST['title']       = $addslashes($_POST['title']);
		$_POST['description'] = $addslashes($_POST['description']);

		if (intval($_POST['tracking'])) {
			$tracking = _AT('on');
		} else {
			$tracking = _AT('off');
		}

		$feedback[] = AT_FEEDBACK_COURSE_UPDATED;

		//if they checked 'other', set quota=entered value, if it is empty or negative, set to default (-2)
		if ($quota == '2') {
			if ($quota_entered=='' || empty($quota_entered) || $quota_entered<0 ) {
				$quota = AT_COURSESIZE_DEFAULT;				
			} else {
				$quota = floatval($quota_entered);
				$quota = megabytes_to_bytes($quota);
			}
		}

		//if they checked 'other', set filesize=entered value, if it is empty or negative, set to default 
		if ($filesize=='2') {
			if ($filesize_entered=='' || empty($filesize_entered) || $filesize_entered<0 ) {
				$filesize = AT_FILESIZE_DEFAULT;
				$feedback[] = AT_FEEDBACK_COURSE_DEFAULT_FSIZE;
			} else {
				$filesize = floatval($filesize_entered);
				$filesize = kilobytes_to_bytes($filesize);
			}
		}

		$sql	= "REPLACE INTO ".TABLE_PREFIX."course_enrollment VALUES ($instructor, $course_id, 'y', 0, '"._AT('instructor')."', 0)";
		$result = mysql_query($sql, $db);

		$sql	= "UPDATE ".TABLE_PREFIX."courses SET member_id='$instructor', access='$_POST[access]', title='$_POST[title]', description='$_POST[description]', cat_id='$_POST[category_parent]', content_packaging='$_POST[packaging]', notify=$notify, hide=$hide, cat_id = $cat, max_quota=$quota, max_file_size=$filesize, tracking='$_POST[tracking]', primary_language='$_POST[pri_lang]' WHERE course_id=$course_id";
		$result = mysql_query($sql, $db);
		if (!$result) {
			echo 'DB Error';
			exit;
		}
		cache_purge('system_courses','system_courses');
		return;

	} else {
		$_POST['title']       = $addslashes($_POST['title']);
		$_POST['description'] = $addslashes($_POST['description']);
		$_POST['pri_lang']    = $addslashes($_POST['pri_lang']);

		$sql = "UPDATE ".TABLE_PREFIX."courses SET access='$_POST[access]', title='$_POST[title]', description='$_POST[description]', cat_id='$_POST[category_parent]', content_packaging='$_POST[packaging]', notify=$notify, hide=$hide, primary_language='$_POST[pri_lang]' WHERE course_id=$course_id AND member_id=$_SESSION[member_id]";

		$result = mysql_query($sql, $db);

		if (!$result) {
			echo 'DB Error';
			exit;
		}
		$_SESSION['course_title'] = stripslashes($_POST['title']);
		cache_purge('system_courses','system_courses');

		return;
	}
  }

}
?>