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
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

function add_update_course($_POST, $isadmin = FALSE) {
	global $addslashes;
	global $db;
	global $system_courses;
	global $MaxCourseSize;
	global $msg;

	$Backup =& new Backup($db);

	if ($_POST['title'] == '') {
		$msg->addError('TITLE_EMPTY');
	} 
	if (!$_POST['instructor']) {
		$msg->addError('INSTRUCTOR_EMPTY');
	}

	$_POST['access']      = $addslashes($_POST['access']);
	$_POST['title']       = $addslashes($_POST['title']);
	$_POST['description'] = $addslashes($_POST['description']);
	$_POST['hide']        = $addslashes($_POST['hide']);
	$_POST['pri_lang']	  = $addslashes($_POST['pri_lang']);
	$_POST['created_date']= $addslashes($_POST['created_date']);

	$_POST['course_id']	= intval($_POST['course_id']);
	$_POST['notify']	= intval($_POST['notify']);
	$_POST['hide']		= intval($_POST['hide']);
	$_POST['instructor']= intval($_POST['instructor']);
	$_POST['category_parent']	= intval($_POST['category_parent']);

	$initial_content_info = explode('_', $_POST['initial_content'], 2);

	//admin
	if ($isadmin) {
		$quota    = intval($_POST['quota']);
		$filesize = intval($_POST['filesize']);

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
				$msg->addFeedback('COURSE_DEFAULT_FSIZE');
			} else {
				$filesize = floatval($filesize_entered);
				$filesize = kilobytes_to_bytes($filesize);
			}
		}

	} else {
		if (!$_POST['course_id'])	{
			$quota    = AT_COURSESIZE_DEFAULT;
			$filesize = AT_FILESIZE_DEFAULT;
			$_POST['tracking'] = 'off';
			$row = $Backup->getRow($initial_content_info[0], $initial_content_info[1]);

			if ((count($initial_content_info) == 2) 
				&& ($system_courses[$initial_content_info[1]]['member_id'] == $_SESSION['member_id'])) {
				
					if ($MaxCourseSize < $row['contents']['file_manager']) {
						$msg->addError('RESTORE_TOO_BIG');	
					}
			} else {
				$initial_content_info = intval($_POST['initial_content']);
			}

		} else {
			$quota = 'max_quota';
			$filesize = 'max_file_size';
			$_POST['tracking'] = 'tracking';
			unset($initial_content_info);
		}

	}

	if ($msg->containsErrors()) {
		return;
	}

	$sql	= "REPLACE INTO ".TABLE_PREFIX."courses SET course_id=$_POST[course_id], member_id='$_POST[instructor]', access='$_POST[access]', title='$_POST[title]', description='$_POST[description]', cat_id='$_POST[category_parent]', content_packaging='$_POST[content_packaging]', notify=$_POST[notify], hide=$_POST[hide], max_quota=$quota, max_file_size=$filesize, tracking='$_POST[tracking]', primary_language='$_POST[pri_lang]', created_date='$_POST[created_date]'";
	$result = mysql_query($sql, $db);
	if (!$result) {
		echo 'DB Error';
		exit;
	}
	$new_course_id = mysql_insert_id($db);

	$sql	= "REPLACE INTO ".TABLE_PREFIX."course_enrollment VALUES ($_POST[instructor], $new_course_id, 'y', 0, '"._AT('instructor')."', 0)";
	$result = mysql_query($sql, $db);


	// create the course content directory
	$path = AT_CONTENT_DIR . $new_course_id . '/';
	@mkdir($path, 0700);
	@copy(AT_CONTENT_DIR . 'index.html', AT_CONTENT_DIR . $new_course_id . '/index.html');

	// create the course backup directory
	$path = AT_BACKUP_DIR . $new_course_id . '/';
	@mkdir($path, 0700);
	@copy(AT_CONTENT_DIR . 'index.html', AT_BACKUP_DIR . $new_course_id . '/index.html');

	/* insert some default content: */

	if (!$_POST['course_id'] && ($_POST['initial_content'] == 1)) {
		$contentManager = new ContentManager($db, $new_course_id);
		$contentManager->initContent( );

		$cid = $contentManager->addContent($new_course_id, 0, 1,_AT('welcome_to_atutor'),
											addslashes(_AT('this_is_content')),
											'', '', 1, date('Y-m-d H:00:00'), 0);

		$announcement = _AT('default_announcement');
		
		$sql	= "INSERT INTO ".TABLE_PREFIX."news VALUES (0, $new_course_id, $_SESSION[member_id], NOW(), 1, '"._AT('welcome_to_atutor')."', '$announcement')";
		$result = mysql_query($sql,$db);

		// create forum for Welcome Course
		$sql	= "INSERT INTO ".TABLE_PREFIX."forums VALUES (0, $new_course_id, '"._AT('forum_general_discussion')."', '', 0, 0, NOW())";
		$result = mysql_query($sql,$db);
	} else if (!$_POST['course_id'] && (count($initial_content_info) == 2)){

		$Backup->setCourseID($new_course_id);
		$Backup->restore($material = TRUE, 'append', $initial_content_info[0], $initial_content_info[1]);
	}
 
	if(file_exists(AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS2.0.xml")||
		file_exists(AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS1.0.xml")){
		require_once('../tools/feeds/browse_courses_feed.php');
	}

	
	cache_purge('system_courses','system_courses');
	return $new_course_id;
	
	//Update RSS feeds if they exist		

	

}

?>