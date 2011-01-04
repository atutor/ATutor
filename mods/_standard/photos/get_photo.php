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
// $Id: get_photo.php 10055 2010-06-29 20:30:24Z cindy $
define('AT_INCLUDE_PATH', '../../../include/');
@ob_end_clean();
header("Content-Encoding: none");

$_user_location	= 'public';

require(AT_INCLUDE_PATH . 'vitals.inc.php');
require(AT_INCLUDE_PATH . 'lib/mime.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
include (AT_PA_INCLUDE.'lib.inc.php');

$aid = intval($_GET['aid']);	//album id
$pid = intval($_GET['pid']);	//photo id
$ph  = $_GET['ph'];				//pid hash

//To increase security so users can't freely browse thru the album, 
//add a block here to take in an extra $_GET variable that reads the pid_path
//check it against the PhotoFilePath here and see if it matches.
//if not, return a "File not found" image.
//TODO

$pa = new PhotoAlbum($aid);
$album_info = $pa->getAlbumInfo();
$photo_info = $pa->getPhotoInfo($pid);
$album_file_path = getAlbumFilePath($album_info['id'], $album_info['created_date']);
if (isset($_GET['size']) && $_GET['size'] == 'o') {
	//if original
	$album_file_path .= DIRECTORY_SEPARATOR;
} else {
	//if thumbnail
	$album_file_path .= '_tn'.DIRECTORY_SEPARATOR;
}
$photo_file_path = getPhotoFilePath($photo_info['id'], $photo_info['name'], $photo_info['created_date']);
$photo_file_hash = getPhotoFilePath($photo_info['id'], '', $photo_info['created_date']);

$file = AT_PA_CONTENT_DIR . $album_file_path . $photo_file_path;

//if file does not exist, quit.
if (!file_exists($file)){
	//TODO: Clean files silently, cleaned but garbaged link remains on page. 
	//Remove node from the DOM tree?
	$pa->deletePhoto($pid);
	header('HTTP/1.1 404 Not Found', TRUE);
	exit;
} 
//if hash doesn't match, then don't load the picture. 
//to prevent trial and error on URL for photos
if ($ph !== $photo_file_hash){
	header('HTTP/1.1 404 Not Found', TRUE);
	exit;
}

$pathinfo = pathinfo($file);
$ext = $pathinfo['extension'];
if ($ext == '') {
	$ext = 'application/octet-stream';
} else {
	$ext = $mime[$ext][0];
}

$real = realpath($file);

if (file_exists($real) && (substr($real, 0, strlen(AT_CONTENT_DIR)) == AT_CONTENT_DIR)) {

	header('Content-Disposition: filename="'.$photo_file_path.'"');
	
	/**
	 * although we can check if mod_xsendfile is installed in apache2
	 * we can't actually check if it's enabled. also, we can't check if
	 * it's enabled and installed in lighty, so instead we send the 
	 * header anyway, if it works then the line after it will not
	 * execute. if it doesn't work, then the line after it will replace
	 * it so that the full server path is not exposed.
	 *
	 * x-sendfile is supported in apache2 and lighttpd 1.5+ (previously
	 * named x-send-file in lighttpd 1.4)
	 */
	header('x-Sendfile: '.$real);
	header('x-Sendfile: ', TRUE); // if we get here then it didn't work

	header('Content-Type: '.$ext);

	@readfile($real);
	exit;
} else {
	header('HTTP/1.1 404 Not Found', TRUE);
	exit;
}

?>