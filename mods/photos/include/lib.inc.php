<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

/**
 * Convert all input to htmlentities output, in UTF-8.
 * @param	string	input to be convert
 * @param	boolean	true if we wish to change all carrier returns to a <br/> tag, false otherwise.
 */
if(!function_exists('htmlentities_utf8')){
	function htmlentities_utf8($str, $use_nl2br=true){
		$return = htmlentities($str, ENT_QUOTES, 'UTF-8');
		if ($use_nl2br){
			return nl2br($return);
		} 
		return $return;
	}
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
	global $msg;
	$msg = new AjaxMessage();

	// check if GD is installed
	if (!extension_loaded('gd')) {
		$msg->printInfos('FEATURE_NOT_AVAILABLE');
		return false;
	}

	// check if folder exists, if not, create it
	if (!is_dir(AT_CONTENT_DIR.'/photo_album')) {
		mkdir(AT_CONTENT_DIR.'/photo_album');
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
	// TODO

	//check filename
	$file['name'] = str_replace(array('\'', '"', ' ', '|', '\\', '/', '<', '>', ':'), '_' , $file['name'] );
	$file['name'] = preg_replace("/[^A-Za-z0-9._\-]/", '', $file['name'] );
	
	return $file;
}
 
?>
