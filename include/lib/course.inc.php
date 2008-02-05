<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
 * To resize course_icon images
 * @param	uploaded image source path 
 * @param	uploaded image path to be saved as
 * @param	uploaded image's height
 * @param	uploaded image width
 * @param	save file with this height
 * @param	save file with this width
 * @param	file extension type
 * @return	true if successful, false otherwise
 */
function resize_image($src, $dest, $src_h, $src_w, $dest_h, $dest_w, $type) {
	$thumbnail_img = imagecreatetruecolor($dest_w, $dest_h);
	if ($type == 'gif') {
		$source = imagecreatefromgif($src);
	} else if ($type == 'jpg') {
		$source = imagecreatefromjpeg($src);
	} else {
		$source = imagecreatefrompng($src);
	}
	
	$result = imagecopyresampled($thumbnail_img, $source, 0, 0, 0, 0, $dest_w, $dest_h, $src_w, $src_h);

	if ($type == 'gif') {
		$result &= imagegif($thumbnail_img, $dest);
	} else if ($type == 'jpg') {
		$result &= imagejpeg($thumbnail_img, $dest, 75);
	} else {
		$result &= imagepng($thumbnail_img, $dest, 7);
	}
	return $result;
}

function add_update_course($_POST, $isadmin = FALSE) {
	require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

	global $addslashes;
	global $db;
	global $system_courses;
	global $MaxCourseSize;
	global $msg;
	global $_config;
	global $_config_defaults;
	global $stripslashes;

	$Backup =& new Backup($db);
	$missing_fields = array();

	if ($_POST['title'] == '') {
		$missing_fields[] = _AT('title');
	} 
	if (!$_POST['instructor']) {
		$missing_fields[] = _AT('instructor');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	$_POST['access']      = $addslashes($_POST['access']);
	$_POST['title']       = $addslashes($_POST['title']);
	$_POST['description'] = $addslashes($_POST['description']);
	$_POST['hide']        = $addslashes($_POST['hide']);
	$_POST['pri_lang']	  = $addslashes($_POST['pri_lang']);
	$_POST['created_date']= $addslashes($_POST['created_date']);
	$_POST['copyright']	  = $addslashes($_POST['copyright']);
	$_POST['icon']		  = $addslashes($_POST['icon']);
	$_POST['banner']      = $addslashes($_POST['banner']);

	$_POST['course']	= intval($_POST['course']);
	$_POST['notify']	= intval($_POST['notify']);
	$_POST['hide']		= intval($_POST['hide']);
	$_POST['instructor']= intval($_POST['instructor']);
	$_POST['category_parent']	= intval($_POST['category_parent']);
	$_POST['rss']       = intval($_POST['rss']);

	// Custom icon
	if ($_FILES['customicon']['name'] != ''){
		// Use custom icon instead if it exists
		$_POST['icon']	  = $addslashes($_FILES['customicon']['name']);
	} 
	if ($_FILES['customicon']['error'] == UPLOAD_ERR_FORM_SIZE){
		// Check if filesize is too large for a POST
		$msg->addError(array('FILE_MAX_SIZE', $_config['prof_pic_max_file_size'] . ' ' . _AT('bytes')));
	}
	if ($_POST['release_date']) {
		$day_release	= intval($_POST['day_release']);
		$month_release	= intval($_POST['month_release']);
		$year_release	= intval($_POST['year_release']);
		$hour_release	= intval($_POST['hour_release']);
		$min_release	= intval($_POST['min_release']);

		if (!checkdate($month_release, $day_release, $year_release)) { //or date is in the past
			$msg->addError('RELEASE_DATE_INVALID');
		}

		if (strlen($month_release) == 1){
			$month_release = "0$month_release";
		}
		if (strlen($day_release) == 1){
			$day_release = "0$day_release";
		}
		if (strlen($hour_release) == 1){
			$hour_release = "0$hour_release";
		}
		if (strlen($min_release) == 1){
			$min_release = "0$min_release";
		}
		$release_date = "$year_release-$month_release-$day_release $hour_release:$min_release:00";
	} else {
		$release_date = "0000-00-00 00:00:00";
	}

	if ($_POST['end_date']) {
		$day_end	= intval($_POST['day_end']);
		$month_end	= intval($_POST['month_end']);
		$year_end	= intval($_POST['year_end']);
		$hour_end	= intval($_POST['hour_end']);
		$min_end	= intval($_POST['min_end']);

		if (!checkdate($month_end, $day_end, $year_end)) { //or date is in the past
			$msg->addError('END_DATE_INVALID');
		}

		if (strlen($month_end) == 1){
			$month_end = "0$month_end";
		}
		if (strlen($day_end) == 1){
			$day_end = "0$day_end";
		}
		if (strlen($hour_end) == 1){
			$hour_end = "0$hour_end";
		}
		if (strlen($min_end) == 1){
			$min_end = "0$min_end";
		}
		$end_date = "$year_end-$month_end-$day_end $hour_end:$min_end:00";
	} else {
		$end_date = "0000-00-00 00:00:00";
	}

	$initial_content_info = explode('_', $_POST['initial_content'], 2);
	//admin
	$course_quotas = '';
	if ($isadmin) {
		$instructor		= $_POST['instructor'];
		$quota			= intval($_POST['quota']);
		$quota_entered  = intval($_POST['quota_entered']);
		$filesize		= intval($_POST['filesize']);
		$filesize_entered= intval($_POST['filesize_entered']);

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
				$filesize = megabytes_to_bytes($filesize);
			}
		}

		$course_quotas  =  "max_quota='$quota', max_file_size='$filesize',";

	} else {
		$instructor = $_SESSION['member_id'];
		if (!$_POST['course'])	{
			$course_quotas    =  "max_quota=".AT_COURSESIZE_DEFAULT.", max_file_size=".AT_FILESIZE_DEFAULT.",";
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
			unset($initial_content_info);
			$course_quotas  =  "max_quota='{$system_courses[$_POST[course]][max_quota]}', max_file_size='{$system_courses[$_POST[course]][max_file_size]}',";
		}
	}

	if ($msg->containsErrors()) {
		return FALSE;
	}

	//display defaults
	if (!$_POST['course']) {
		$menu_defaults = ",home_links='$_config[home_defaults]', main_links='$_config[main_defaults]', side_menu='$_config[side_defaults]'";
	} else {
		$menu_defaults = ',home_links=\''.$system_courses[$_POST['course']]['home_links'].'\', main_links=\''.$system_courses[$_POST['course']]['main_links'].'\', side_menu=\''.$system_courses[$_POST['course']]['side_menu'].'\'';
	}

	$sql	= "REPLACE INTO ".TABLE_PREFIX."courses SET course_id=$_POST[course], member_id='$_POST[instructor]', access='$_POST[access]', title='$_POST[title]', description='$_POST[description]', cat_id='$_POST[category_parent]', content_packaging='$_POST[content_packaging]', notify=$_POST[notify], hide=$_POST[hide], $course_quotas primary_language='$_POST[pri_lang]', created_date='$_POST[created_date]', rss=$_POST[rss], copyright='$_POST[copyright]', icon='$_POST[icon]', banner='$_POST[banner]', release_date='$release_date', preferences=preferences, header=header, footer=footer, banner_text=banner_text, banner_styles=banner_styles, end_date='$end_date' $menu_defaults";

	$result = mysql_query($sql, $db);
	if (!$result) {
		echo mysql_error($db);
		echo 'DB Error';
		exit;
	}
	$_SESSION['is_admin'] = 1;
	$new_course_id = $_SESSION['course_id'] = mysql_insert_id($db);
	if ($isadmin) {
		write_to_log(AT_ADMIN_LOG_REPLACE, 'courses', mysql_affected_rows($db), $sql);
	}

	if ($isadmin) {
		//get current instructor and unenroll from course if different from POST instructor	
		$old_instructor = $system_courses[$_POST['course']]['member_id'];
		
		if ($old_instructor != $_POST['instructor']) {
			//remove old from course enrollment
			$sql = "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=".$_POST['course']." AND member_id=".$old_instructor;
			$result = mysql_query($sql, $db);
			write_to_log(AT_ADMIN_LOG_DELETE, 'course_enrollment', mysql_affected_rows($db), $sql);
		} 
	}

	//enroll new instructor
	$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($_POST[instructor], $new_course_id, 'y', 0, '"._AT('instructor')."', 0)";
	$result = mysql_query($sql, $db);
	if ($isadmin) {
		write_to_log(AT_ADMIN_LOG_REPLACE, 'course_enrollment', mysql_affected_rows($db), $sql);
	}

	// create the course content directory
	$path = AT_CONTENT_DIR . $new_course_id . '/';
	@mkdir($path, 0700);
	@copy(AT_CONTENT_DIR . 'index.html', AT_CONTENT_DIR . $new_course_id . '/index.html');

	// create the course backup directory
	$path = AT_BACKUP_DIR . $new_course_id . '/';
	@mkdir($path, 0700);
	@copy(AT_CONTENT_DIR . 'index.html', AT_BACKUP_DIR . $new_course_id . '/index.html');

	/* insert some default content: */

	if (!$_POST['course_id'] && ($_POST['initial_content'] == '1')) {
		$contentManager = new ContentManager($db, $new_course_id);
		$contentManager->initContent( );

		$cid = $contentManager->addContent($new_course_id, 0, 1,_AT('welcome_to_atutor'),
											addslashes(_AT('this_is_content')),
											'', '', 1, date('Y-m-d H:00:00'));

		$announcement = _AT('default_announcement');
		
		$sql	= "INSERT INTO ".TABLE_PREFIX."news VALUES (NULL, $new_course_id, $instructor, NOW(), 1, '"._AT('welcome_to_atutor')."', '$announcement')";
		$result = mysql_query($sql,$db);
		
		if ($isadmin) {
			write_to_log(AT_ADMIN_LOG_INSERT, 'news', mysql_affected_rows($db), $sql);
		}

		/**
		 * removed - #3098
		// create forum for Welcome Course
		$sql	= "INSERT INTO ".TABLE_PREFIX."forums VALUES (NULL, '"._AT('forum_general_discussion')."', '', 0, 0, NOW())";
		$result = mysql_query($sql,$db);

		if ($isadmin) {
			write_to_log(AT_ADMIN_LOG_INSERT, 'forums', mysql_affected_rows($db), $sql);
		}

		$sql = "INSERT INTO ".TABLE_PREFIX."forums_courses VALUES (LAST_INSERT_ID(), $new_course_id)";
		$result = mysql_query($sql,$db);

		if ($isadmin) {
			write_to_log(AT_ADMIN_LOG_INSERT, 'forums_courses', mysql_affected_rows($db), $sql);
		}
		***/

	} else if (!$_POST['course'] && (count($initial_content_info) == 2)){

		$Backup->setCourseID($new_course_id);
		$Backup->restore($material = TRUE, 'append', $initial_content_info[0], $initial_content_info[1]);
	}
 
 	// custom icon, have to be after directory is created
//	$_FILES['customicon'] = $_POST['customicon'];	//copy to $_FILES.
	if($_FILES['customicon']['tmp_name'] != ''){
        $_POST['comments'] = trim($_POST['comments']);

        $owner_id = $_SESSION['course_id'];
        $owner_type = "1";
        if ($_FILES['customicon']['error'] == UPLOAD_ERR_INI_SIZE) {
            $msg->addError(array('FILE_TOO_BIG', get_human_size(megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1)))));
        } else if (!isset($_FILES['customicon']['name']) || ($_FILES['customicon']['error'] == UPLOAD_ERR_NO_FILE) || ($_FILES['customicon']['size'] == 0)) {
            $msg->addError('FILE_NOT_SELECTED');

        } else if ($_FILES['customicon']['error'] || !is_uploaded_file($_FILES['customicon']['tmp_name'])) {
            $msg->addError('FILE_NOT_SAVED');
        }
        
        if (!$msg->containsErrors()) {
            $_POST['description'] = $addslashes(trim($_POST['description']));
            $_FILES['customicon']['name'] = addslashes($_FILES['customicon']['name']);

            if ($_POST['comments']) {
                $num_comments = 1;
            } else {
                $num_comments = 0;
            }
            
            $path = AT_CONTENT_DIR.$owner_id."/custom_icons/";
		
            if (!is_dir($path)) {
                @mkdir($path);
            }
			
			// if we can upload custom course icon, it means GD is enabled, no need to check extension again.
			$gd_info = gd_info();
			$supported_images = array();
			if ($gd_info['GIF Create Support']) {
				$supported_images[] = 'gif';
			}
			if ($gd_info['JPG Support']) {
				$supported_images[] = 'jpg';
			}
			if ($gd_info['PNG Support']) {
				$supported_images[] = 'png';
			}

			// check if this is a supported file type
			$filename   = $stripslashes($_FILES['customicon']['name']);
			$path_parts = pathinfo($filename);
			$extension  = strtolower($path_parts['extension']);
			$image_attributes = getimagesize($_FILES['customicon']['tmp_name']);

			if ($extension == 'jpeg') {
				$extension = 'jpg';
			}

			// resize the original but don't backup a copy.
			$width  = $image_attributes[0];
			$height = $image_attributes[1];
			$original_img	= $_FILES['customicon']['tmp_name'];
			$thumbnail_img	= $path . $_FILES['customicon']['name'];

			if ($width > $height && $width>79) {
				$thumbnail_height = intval(79 * $height / $width);
				$thumbnail_width  = 79;
				if (!resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension)){
					$msg->addError('FILE_NOT_SAVED');
				}
			} else if ($width <= $height && $height > 79) {
				$thumbnail_height= 100;
				$thumbnail_width = intval(100 * $width / $height);
				if (!resize_image($original_img, $thumbnail_img, $height, $width, $thumbnail_height, $thumbnail_width, $extension)){
					$msg->addError('FILE_NOT_SAVED');
				}
			} else {
				// no resizing, just copy the image.
				// it's too small to resize.
				copy($original_img, $thumbnail_img);
			}

        } else {
            $msg->addError('FILE_NOT_SAVED');
            
        }
        //header('Location: index.php'.$owner_arg_prefix.'folder='.$parent_folder_id);
        //exit;
    }
    //----------------------------------------

	/* delete the RSS feeds just in case: */
	if (file_exists(AT_CONTENT_DIR . 'feeds/' . $new_course_id . '/RSS1.0.xml')) {
		@unlink(AT_CONTENT_DIR . 'feeds/' . $_POST['course'] . '/RSS1.0.xml');
	}
	if (file_exists(AT_CONTENT_DIR . 'feeds/' . $new_course_id . '/RSS2.0.xml')) {
		@unlink(AT_CONTENT_DIR . 'feeds/' . $new_course_id . '/RSS2.0.xml');
	}

	if ($isadmin) {
		$_SESSION['course_id'] = -1;
	}

	$_SESSION['course_title'] = $stripslashes($_POST['title']);
	return $new_course_id;
}

?>