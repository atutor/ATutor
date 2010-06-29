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
// $Id: view_transcript.php 7208 2008-01-09 16:07:24Z greg $
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');


$file = AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/'.$_GET['t'].'.html';
if (!file_exists($file)) {
	$msg->addError('FILE_NOT_FOUND');
	header('Location: index.php');
	exit;
}
require(AT_INCLUDE_PATH.'header.inc.php');
@readfile($file);
echo '</table>';
require(AT_INCLUDE_PATH.'footer.inc.php');
?>