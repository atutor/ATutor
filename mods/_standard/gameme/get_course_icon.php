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

define('AT_INCLUDE_PATH', '../../../include/');

@ob_end_clean();
header("Content-Encoding: none");

$_user_location	= 'public';

require(AT_INCLUDE_PATH . 'vitals.inc.php');
require(AT_INCLUDE_PATH . 'lib/mime.inc.php');
/*
if($_SESSION['course_id'] >0){
    $course_id=$_SESSION['course_id'];
}else{
    $course_id=0;
}
*/
$course_id=0;
$badge_id= intval($_GET['badge_id']);
$course_id= intval($_GET['course_id']);
$content_dir = explode('/',AT_CONTENT_DIR);
array_pop($content_dir);

$sql = "SELECT image_url FROM %sgm_badges WHERE course_id=%d and id=%d";
$badge = queryDB($sql, array(TABLE_PREFIX, $course_id, $badge_id), TRUE);

$badge_file_name = explode("/",$badge['image_url']);
        array_shift($badge_file_name);
        array_shift($badge_file_name);
$badge_file_stem  = implode('/',$badge_file_name);

// Check for course badge first
$badge_file = AT_CONTENT_DIR.$course_id.'/gameme/badges/'.end($badge_file_name);

$real = realpath($badge_file);

if (file_exists($real) && (substr($real, 0, strlen(AT_CONTENT_DIR)) == AT_CONTENT_DIR)) {
	header('Content-Disposition: inline; filename="'.end($badge_file_name).'"');
	
	$ext = explode('.',end($badge_file_name));

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

	header('Content-Type: '.end($ext));

	@readfile($real);
	exit;
} else {
	header('HTTP/1.1 404 Not Found', TRUE);
	exit;
}

?>