<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', 'include/');
if (isset($_GET['test'])) {
	header('HTTP/1.1 200 OK', TRUE);
	header('ATutor-Get: OK');
	exit;
}
$in_get = TRUE;

require(AT_INCLUDE_PATH . 'vitals.inc.php');
require(AT_INCLUDE_PATH . 'lib/mime.inc.php');

$force_download = false;

//get path to file
if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	if (!empty($_SERVER['PATH_INFO'])) {
        $current_file = $_SERVER['PATH_INFO'];
	} else if (!empty($_SERVER['REQUEST_URI'])) {
		$current_file = $_SERVER['REQUEST_URI'];
    } else if (!empty($_SERVER['PHP_SELF'])) {
		if (!empty($_SERVER['QUERY_STRING'])) {
            $current_file = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
        } else {
	        $current_file = $_SERVER['PHP_SELF'];
		}
    } else if (!empty($_SERVER['SCRIPT_NAME'])) {
		if (!empty($_SERVER['QUERY_STRING'])) {
            $current_file = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
        } else {
	        $current_file = $_SERVER['SCRIPT_NAME'];
		}
    } else if (!empty($_SERVER['URL'])) {
        if (!empty($_SERVER['QUERY_STRING'])) {
            $current_file = $_SERVER['URL'] . '?' . $_SERVER['QUERY_STRING'];
        }
        $current_file = $_SERVER['URL'];
	}

	if ($pos = strpos($current_file, '/get.php/') !== FALSE) {
		$current_file = substr($current_file, $pos + strlen('/get.php/'));
	}
	
	if (substr($current_file, 0, 2) == '/@') {
		$force_download = true;
		$current_file = substr($current_file, 2);
	}
} else {
	$current_file = $_GET['f'];

	if (substr($current_file, 0, 2) == '/@') {
		$force_download = true;
		$current_file = substr($current_file, 2);
	}
}

$file_name = pathinfo($current_file);
$file_name = $file_name['basename'];

if (substr($file_name, 0, 4) == 'b64:') {
	$base64_file_name = substr($file_name, 4);
	$file_name = base64_decode($base64_file_name);
	$current_file = '/'.$file_name;
}


$file = AT_CONTENT_DIR . $_SESSION['course_id'] . $current_file;

//send header mime type
$pathinfo = pathinfo($file);
$ext = $pathinfo['extension'];
if ($ext == '') {
	$ext = 'application/octet-stream';
} else {
	$ext = $mime[$ext][0];
}

//check that this file is within the content directory & exists

// NOTE!! for some reason realpath() is not returning FALSE when the file doesn't exist!
$real = realpath($file);

if (file_exists($real) && (substr($real, 0, strlen(AT_CONTENT_DIR)) == AT_CONTENT_DIR)) {
	if ($force_download) {
		header('Content-Type: application/force-download');
		header('Content-transfer-encoding: binary'); 
		header('Content-Disposition: attachment; filename="'.$pathinfo['basename'].'"');
	} else {
		header('Content-Disposition: filename="'.$pathinfo['basename'].'"');
	}
	
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