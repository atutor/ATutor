<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }


function add_update_course($course_data, $isadmin = FALSE) {
	require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

	global $addslashes;
	global $db;
	global $system_courses;
	global $MaxCourseSize;
	global $msg;
	global $_config;
	global $_config_defaults;
	global $stripslashes;

	$Backup = new Backup($db);
	$missing_fields = array();

	if ($course_data['title'] == '') {
		$missing_fields[] = _AT('title');
	} 
	if (!$course_data['instructor']) {
		$missing_fields[] = _AT('instructor');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	$course_data['access']		  = $addslashes($course_data['access']);
	$course_data['title']			  = strip_tags($addslashes($course_data['title']));
	$course_data['description']	  = $addslashes($course_data['description']);
	$course_data['hide']			  = $addslashes($course_data['hide']);
	$course_data['pri_lang']		  = $addslashes($course_data['pri_lang']);
	$course_data['created_date']	  = $addslashes($course_data['created_date']);
	$course_data['copyright']		  = $addslashes($course_data['copyright']);
	$course_data['icon']			  = $addslashes($course_data['icon']);
	$course_data['banner']		  = $addslashes($course_data['banner']);
	$course_data['course_dir_name'] = $addslashes($course_data['course_dir_name']);

	$course_data['course']	= intval($course_data['course']);
	$course_data['notify']	= intval($course_data['notify']);
	$course_data['hide']		= intval($course_data['hide']);
	$course_data['instructor']= intval($course_data['instructor']);
	$course_data['category_parent']	= intval($course_data['category_parent']);
	$course_data['rss']       = intval($course_data['rss']);

	// Course directory name (aka course slug)
	if ($course_data['course_dir_name'] != ''){
		//validate the course_dir_name, allow only alphanumeric, underscore.
		if (preg_match('/^[\w][\w\d\_]+$/', $course_data['course_dir_name'])==0){
			$msg->addError('COURSE_DIR_NAME_INVALID');
		}

		//check if the course_dir_name is already being used
		
		$sql = "SELECT COUNT(course_id) as cnt FROM %scourses WHERE course_id!=%d AND course_dir_name='%s'";
		$num_of_dir = queryDB($sql, array(TABLE_PREFIX, $course_data['course'], $course_data['course_dir_name']), TRUE);
		
		if (intval($num_of_dir['cnt']) > 0){
			$msg->addError('COURSE_DIR_NAME_IN_USE');
		}		
	}

	// Custom icon
	if ($_FILES['customicon']['name'] != ''){
		// Use custom icon instead if it exists
		// Check if image type is supported
		$gd_info = gd_info();
	    $supported_images = array();
	    if ($gd_info['GIF Create Support']) {
		    $supported_images[] = 'gif';
	    } 
	    if ($gd_info['JPG Support'] || $gd_info['JPEG Support']) {
		    $supported_images[] = 'jpg';
	    }
	    if ($gd_info['PNG Support']) {
	    	$supported_images[] = 'png';
	    }
	    $count_extensions = count($supported_images);
	    
	    $pattern = "/^.*\.(";
	    foreach($supported_images as $extension){
	        $count++;
	        if($count == $count_extension){
	         $pattern .= $extension;
	         }else {
	         $pattern .= $extension."|";
	        }
	    }
	    $pattern .= ")$/i";
	    if(preg_match($pattern, $_FILES['customicon']['name'])){
	    		$course_data['icon']	  = $addslashes($_FILES['customicon']['name']);
	    		$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
                $detectedType = exif_imagetype($_FILES['customicon']['tmp_name']);
                if(!in_array($detectedType, $allowedTypes)){
                    $msg->addError(array('FILE_ILLEGAL', $_FILES['customicon']['name']));
                }
	    } else {
			    $msg->addError(array('FILE_ILLEGAL', $_FILES['customicon']['name']));
	    }
	} 
	if ($_FILES['customicon']['error'] == UPLOAD_ERR_FORM_SIZE){
		// Check if filesize is too large for a POST
		$msg->addError(array('FILE_MAX_SIZE', $_config['prof_pic_max_file_size'] . ' ' . _AT('bytes')));
	}
	if ($course_data['release_date']) {
		$day_release	= intval($course_data['day_release']);
		$month_release	= intval($course_data['month_release']);
		$year_release	= intval($course_data['year_release']);
		$hour_release	= intval($course_data['hour_release']);
		$min_release	= intval($course_data['min_release']);

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

	if ($course_data['end_date']) {
		$day_end	= intval($course_data['day_end']);
		$month_end	= intval($course_data['month_end']);
		$year_end	= intval($course_data['year_end']);
		$hour_end	= intval($course_data['hour_end']);
		$min_end	= intval($course_data['min_end']);

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

	$initial_content_info = explode('_', $course_data['initial_content'], 2);
	//admin
	$course_quotas = '';
	if ($isadmin) {
		$instructor		= $course_data['instructor'];
		$quota			= intval($course_data['quota']);
		$quota_entered  = intval($course_data['quota_entered']);
		$filesize		= intval($course_data['filesize']);
		$filesize_entered= intval($course_data['filesize_entered']);

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
		if (!$course_data['course'])	{
			$course_quotas    =  "max_quota=".AT_COURSESIZE_DEFAULT.", max_file_size=".AT_FILESIZE_DEFAULT.",";
			$row = $Backup->getRow($initial_content_info[0], $initial_content_info[1]);

			if ((count($initial_content_info) == 2) 
				&& ($system_courses[$initial_content_info[1]]['member_id'] == $_SESSION['member_id'])) {
				
					if ($MaxCourseSize < $row['contents']['file_manager']) {
						$msg->addError('RESTORE_TOO_BIG');	
					}
			} else {
				$initial_content_info = intval($course_data['initial_content']);
			}

		} else {
			unset($initial_content_info);
			$course_quotas  =  "max_quota='{$system_courses[$course_data[course]][max_quota]}', max_file_size='{$system_courses[$course_data[course]][max_file_size]}',";
		}
	}

	if ($msg->containsErrors()) {
		return FALSE;
	}

	//display defaults
	if (!$course_data['course']) {
		$menu_defaults = ",home_links='$_config[home_defaults]', main_links='$_config[main_defaults]', side_menu='$_config[side_defaults]'";
	} else {
		$menu_defaults = ',home_links=\''.$system_courses[$course_data['course']]['home_links'].'\', main_links=\''.$system_courses[$course_data['course']]['main_links'].'\', side_menu=\''.$system_courses[$course_data['course']]['side_menu'].'\'';
	}
	
    $sql	= "REPLACE INTO %scourses 
                SET 
                course_id=%d, 
                member_id='%s', 
                access='%s', 
                title='%s', 
                description='%s', 
                course_dir_name='%s', 
                cat_id=%d, 
                content_packaging='%s', 
                notify=%d, 
                hide=%d, 
                $course_quotas
                primary_language='%s',
                created_date='%s',
                rss=%d,
                copyright='%s',
                icon='%s',
                banner='%s',
                release_date='%s', 
                end_date='%s' 
                $menu_defaults";

	$result = queryDB($sql, array(TABLE_PREFIX, 
	            $course_data['course'], 
	            $course_data['instructor'], 
	            $course_data['access'], 
	            $course_data['title'], 
	            $course_data['description'], 
	            $course_data['course_dir_name'], 
	            $course_data['category_parent'],
	            $course_data['content_packaging'],
	            $course_data['notify'],
	            $course_data['hide'],
	            $course_data['pri_lang'],
	            $course_data['created_date'],
	            $course_data['rss'],
	            $course_data['copyright'],
	            $course_data['icon'],
	            $course_data['banner'],
	            $release_date,
	            $end_date));
          

	if (!$result) {
		echo at_db_error();
		echo 'DB Error';
		exit;
	}

	$new_course_id = $_SESSION['course_id'] = at_insert_id();
	if (isset($isadmin)) {
	    global $sqlout;	  
		write_to_log(AT_ADMIN_LOG_REPLACE, 'courses', $result, $sqlout);
	}

	if (isset($isadmin)) {
		//get current instructor and unenroll from course if different from POST instructor	
		$old_instructor = $system_courses[$course_data['course']]['member_id'];
		
		if ($old_instructor != $course_data['instructor']) {
			//remove old from course enrollment
			$sql = "DELETE FROM %scourse_enrollment WHERE course_id=%d AND member_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $course_data['course'], $old_instructor));
			
			global $sqlout;		
			write_to_log(AT_ADMIN_LOG_DELETE, 'course_enrollment', $result, $sqlout);
		} 
	}

	//enroll new instructor
	$sql = "REPLACE INTO %scourse_enrollment VALUES (%d, %d, 'y', 0, '"._AT('instructor')."', 0)";
	$result = queryDB($sql, array(TABLE_PREFIX, $course_data['instructor'], $new_course_id));

	if (isset($isadmin)) {
	    global $sqlout;
		write_to_log(AT_ADMIN_LOG_REPLACE, 'course_enrollment', $result, $sqlout);
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

	if (!$course_data['course_id'] && ($course_data['initial_content'] == '1')) {
		$contentManager = new ContentManager($db, $new_course_id);
		$contentManager->initContent( );

		$cid = $contentManager->addContent($new_course_id, 0, 1,_AT('welcome_to_atutor'),
											addslashes(_AT('this_is_content')),
											'', '', 1, date('Y-m-d H:00:00'));

		$announcement = _AT('default_announcement');
		
		$sql	= "INSERT INTO %snews VALUES (NULL, %d, %d, NOW(), 1, '%s', '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $new_course_id, $instructor, _AT('welcome_to_atutor'), $announcement));		
		
		if ($isadmin) {
		    global $sqlout;
			write_to_log(AT_ADMIN_LOG_INSERT, 'news', $result, $sqlout);
		}

	} else if (!$course_data['course'] && (count($initial_content_info) == 2)){

		$Backup->setCourseID($new_course_id);
		$Backup->restore($material = TRUE, 'append', $initial_content_info[0], $initial_content_info[1]);
	}
 
 	// custom icon, have to be after directory is created

	if($_FILES['customicon']['tmp_name'] != ''){
        $course_data['comments'] = trim($course_data['comments']);

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
            $course_data['description'] = $addslashes(trim($course_data['description']));
            $_FILES['customicon']['name'] = addslashes($_FILES['customicon']['name']);

            if ($course_data['comments']) {
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
			if ($gd_info['JPG Support'] || $gd_info['JPEG Support']) {
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

    }
    //----------------------------------------

	/* delete the RSS feeds just in case: */
	if (file_exists(AT_CONTENT_DIR . 'feeds/' . $new_course_id . '/RSS1.0.xml')) {
		@unlink(AT_CONTENT_DIR . 'feeds/' . $course_data['course'] . '/RSS1.0.xml');
	}
	if (file_exists(AT_CONTENT_DIR . 'feeds/' . $new_course_id . '/RSS2.0.xml')) {
		@unlink(AT_CONTENT_DIR . 'feeds/' . $new_course_id . '/RSS2.0.xml');
	}

	if ($isadmin) {
		$_SESSION['course_id'] = -1;
	}

	$_SESSION['course_title'] = $stripslashes($course_data['title']);
	return $new_course_id;
}

?>
