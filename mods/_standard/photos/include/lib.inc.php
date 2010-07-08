<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

/**
 * Convert all input to htmlentities output, in UTF-8.
 * @param	string	input to be convert
 * @param	boolean	true if we wish to change all carrier returns to a <br/> tag, false otherwise.
 * TODO: use htmlentities_utf8 in social when this become a standard module.
 */
function htmlentities_utf82($str, $use_nl2br=true){
	$return = htmlentities($str, ENT_QUOTES, 'UTF-8');
	if ($use_nl2br){
		return nl2br($return);
	} 
	return $return;
}


/** 
 * Generate album path padding by using album_id + album_created_date
 */
function getPhotoFilePath($id, $filename, $timestamp){
	$padding = hash('sha1', $id.$timestamp); 
	$path_parts = pathinfo($filename);
	//return the hash if filename is empty.
	//this is used for validation purposes.
	if($filename==''){
		return $padding;
	}

	$extension  = strtolower($path_parts['extension']);
	//Note: the padding might not be unique, but the path is ALWAYS unique 
	//		because the id is unique.  
	return ($id.'_'.substr($padding, -5).'.'.$extension);
}

/** 
 * Generate album path padding by using album_id + album_created_date
 */
function getAlbumFilePath($id, $timestamp){
	$padding = hash('sha1', $id.$timestamp); 
	//Note: the padding might not be unique, but the path is ALWAYS unique 
	//		because the id is unique.  
	return ($id.'_'.substr($padding, -5));
}

/** 
 * Check if the photo is supported, including extension check, file size check
 * and library support checks.
 * @param	string	location of the file.
 * @return	$_FILE[] on successful, null on failure.
 */
function checkPhoto($file){
	global $stripslashes;
	global $msg, $_config;
	$msg = new AjaxMessage();

	// check if GD is installed
	if (!extension_loaded('gd')) {
		$msg->printInfos('FEATURE_NOT_AVAILABLE');
		return false;
	}

	// check if folder exists, if not, create it
	if (!is_dir(AT_PA_CONTENT_DIR)) {
		mkdir(AT_PA_CONTENT_DIR);
	}

	//check GD support 
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
	if (!$supported_images) {
		$msg->printInfos('FEATURE_NOT_AVAILABLE');
		return false;
	}

	// check if this is a supported file type
	$filename   = $stripslashes($file['name']);
	$path_parts = pathinfo($filename);
	$extension  = strtolower($path_parts['extension']);
	$image_attributes = getimagesize($file['tmp_name']);

	//check Extension
	if ($extension == 'jpeg') {
		$extension = 'jpg';
	}
	if (!in_array($extension, $supported_images)) {
		$msg->addError(array('FILE_ILLEGAL', $extension));
		return false;
	} else if ($image_attributes[2] > IMAGETYPE_PNG) {
		$msg->addError(array('FILE_ILLEGAL', $extension));
		return false;
	}

	// make sure under max file size
	$allowed_usage = $_config['pa_max_memory_per_member'] * 1024 *1024;	//mb
	if (memoryUsage($_SESSION['member_id']) > $allowed_usage){
		$msg->addError('PA_EXCEEDED_MAX_USAGE');
		return false;
	}
	
	//check filename
	$file['name'] = str_replace(array('\'', '"', ' ', '|', '\\', '/', '<', '>', ':'), '_' , $file['name'] );
	$file['name'] = preg_replace("/[^A-Za-z0-9._\-]/", '', $file['name'] );
	return $file;
}
 

/**
 * Return the total personal data usage (in bytes)
 */
function memoryUsage($member_id){	
	global $db; 
	$member_id = intval($member_id);
	if ($member_id < 1){
		return false;
	}

	$memory_usage = 0;
	$sql = 'SELECT p.* FROM '.TABLE_PREFIX.'pa_photos p LEFT JOIN '.TABLE_PREFIX."pa_course_album ca ON p.album_id=ca.album_id WHERE member_id=$member_id AND ca.course_id IS NULL";
	$result = mysql_query($sql, $db);
	if ($result){
		while ($row=mysql_fetch_assoc($result)){
			$pa = new PhotoAlbum($row['album_id']);
			$album_info = $pa->getAlbumInfo();
			$photo_info = $pa->getPhotoInfo($row['id']);
			$album_file_path = getAlbumFilePath($album_info['id'], $album_info['created_date']);
			$photo_file_path = getPhotoFilePath($photo_info['id'], $photo_info['name'], $photo_info['created_date']);
			$file = AT_PA_CONTENT_DIR . $album_file_path . DIRECTORY_SEPARATOR . $photo_file_path;
			if (file_exists($file)){
				$memory_usage += filesize($file);
			}
		}
	}
	return $memory_usage;
}
?>
