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
// $Id: get.php 2224 2004-11-09 20:33:24Z greg $

define('AT_INCLUDE_PATH', 'include/');
$_user_location	= 'public';
$_ignore_page = true; /* without this we wouldn't know where we're supposed to go */
require(AT_INCLUDE_PATH . '/vitals.inc.php');

$mime['xml'] = 'text/xml';

//get path to file
$args = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']));
$file = AT_CONTENT_DIR .$_GET['filename'] . $args;

//send header mime type
$ext = pathinfo($file);
$ext = $ext['extension'];
if ($ext == '') {
	$ext = 'application/octet-stream';
}

//check that this file is within the content directory & exists

// NOTE!! for some reason realpath() is not returning FALSE when the file doesn't exist! NOTE!!
$real = realpath($file);

if (file_exists($real) && (substr($real, 0, strlen(AT_CONTENT_DIR)) == AT_CONTENT_DIR)) {
 	header('Content-Type: '.$mime[$ext]);
	echo @file_get_contents($real);
	exit;
} else {
	header('HTTP/1.1 404 Not Found');
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html>
<head>
	<title>404 Not Found</title>
</head>
<body>
<h1>Not Found</h1>
The requested URL <strong><?php echo $file; ?></strong> was not found on this server.
</body>
</html>
<?php
	exit;
}


if ($_GET['pathext'] != '') {
$pathext = urldecode($_GET['pathext']);
}

$args ='/'.$pathext;

?>
