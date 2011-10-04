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

/* this file simply gets the AT_CONTENT_DIR/CID.html file that was generated
 * by the AChecker page of the content editor.
 * there is no authentication on this page. either the file exists (in which
 * case it is then quickly deleted after), or it doesn't.
 */

$_user_location	= 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH . '/vitals.inc.php');

//get path to file
$args = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']));
$file = AT_CONTENT_DIR . $args;

//check that this file is within the content directory & exists
if (preg_match('/^\/[0-9]+\.html$/', $args) === 1) {
    $real = realpath($file);
    if (file_exists($real) && substr($real, 0, strlen(AT_CONTENT_DIR)) == AT_CONTENT_DIR) {
     	header('Content-Type: text/html');
	    echo file_get_contents($real);
	    exit;
	}
} 
header('HTTP/1.1 404 Not Found');
exit;



?>
