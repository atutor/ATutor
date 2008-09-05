<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Harris Wong							*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
/* Variables */
$supported_media_type = array(	'gif', 'jpg', 'bmp', 'png', 'jpeg', 
								'txt', 'css', 'html', 'htm', 'csv', 'asc', 'tsv', 'xml', 'xsl',
								'wav', 'au', 'mp3', 'mov');

/*
 * Match the XML files to the actual files found in the content, then copy the media 
 * over to the content folder based on the actual links.  *The XML file names might not be right.
 * @param	array	The list of file names provided by the manifest's resources
 * @param	array	The list of relative files that is used in the question contents.  Default empty.
 */
function copyMedia($files, $xml_items = array()){
	global $msg;
	foreach($files as $file_num => $file_loc){
		$new_file_loc ='';
		foreach ($xml_items as $xk=>$xv){
			if (strpos($file_loc, $xv)!==false){
				$new_file_loc = $xv;
				break;
			} 
		}
		if ($new_file_loc==''){
			$new_file_loc = $file_loc;
		}

		//check if new folder is there, if not, create it.
		createDir(AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$new_file_loc );
		
		//copy files over
//			if (rename(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id'].'/'.$file_loc, 
//				AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$package_base_name.'/'.$new_file_loc) === false) {
		//overwrite files
		if (file_exists(AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$new_file_loc)){
			unlink(AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$new_file_loc);
		}
		if (rename(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id'].'/'.$file_loc, 
			AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$new_file_loc) === false) {
			//TODO: Print out file already exist error.
			if (!$msg->containsErrors()) {
//				$msg->addError('FILE_EXISTED');
			}
		} 
	}
}


/* 
 * Create directory recursively based on the path.
 * @param	string	URI of the path, separated by the directory separator.
 * 
 */
function createDir($path){
	//base case
	if (is_dir($path)){
		return;
	} else {
		preg_match('/(.*)[\/\\\\]([^\\\\\/]+)\/?$/', $path, $matches);
		createDir($matches[1]);
		//make directory if it's not a filename.
		if (preg_match('/(.*)\.[\w]+$/', $matches[2])===0) {
			mkdir($matches[0], 0700);
		}
	}	
}

/*
 * Trimming the value (For array walk function)
 * @param	value reference
 * @return	value reference
 */
function trim_value(&$value) { 
	$value = trim($value); 
}


/** 
  * Check if file exists, return true if it is, false otherwise
  * @param	array	resources that each array items store the information of the resource, such as 
  *					link, href, etc.
  * @return	an array of existing filenames, return empty array otherwise.
  */
function isQTIFileExist($attributes){
	global $supported_media_type; 
	$existing_files = array();

	foreach ($attributes as $resource=>$attrs){
		if ($attrs['type'] == 'imsqti_xmlv1p1' || $attrs['type'] == 'imsqti_item_xmlv2p1'){
			//loop through the file array
			foreach($attrs['file'] as $file_id => $file_name){
				$file_pathinfo = pathinfo($file_name);
//				if ($file_pathinfo['basename'] == $attrs['href']){
//					//This file will be parsed later
//					continue;
//				} 

				if (in_array($file_pathinfo['extension'], $supported_media_type)){
					//check media
					if (file_exists(AT_CONTENT_DIR . $_SESSION['course_id'] . '/' . $file_name)){
						$existing_files[] = $file_name;
					}
				}
			}
		}
	}
	return $existing_files;
}
?>