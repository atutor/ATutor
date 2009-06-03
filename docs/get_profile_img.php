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
define('AT_INCLUDE_PATH', 'include/');
@ob_end_clean();
header("Content-Encoding: none");

$_user_location	= 'public';

require(AT_INCLUDE_PATH . 'vitals.inc.php');
require(AT_INCLUDE_PATH . 'lib/mime.inc.php');

$id = intval($_GET['id']);
if (isset($_GET['size']) && $_GET['size'] == 'o') {
	$size = 'originals'; //t (thumbnail) or o (original)
} else {
	$size = 'thumbs';
}

$file = AT_CONTENT_DIR . 'profile_pictures/' . $size .'/'. $id .'.';


$extensions = array('gif', 'jpg', 'png');

foreach ($extensions as $extension) {
	if (file_exists($file.$extension)) {
		$file .= $extension;
	}
}

//if file does not exist, quit.
if (!file_exists($file)){
	return;
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

	header('Content-Disposition: filename="'.$size.$id.'.'.$pathinfo['extension'].'"');
	
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