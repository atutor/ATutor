<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Harris Wong							*/
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: qti.inc.php 8013 2008-10-02 19:51:24Z hwong $

/* Variables */
$supported_media_type = array(	'gif', 'jpg', 'bmp', 'png', 'jpeg', 
								'txt', 'css', 'html', 'htm', 'csv', 'asc', 'tsv', 'xml', 'xsl',
								'wav', 'au', 'mp3', 'mov');

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