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
// $Id: view_item.php,v 1.2 2004/05/14 20:25:08 joel Exp $

	define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');

	$url = urldecode($_GET['url']);

	@readfile($_GET['url']);

?>