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
// $Id: get_acheck.php 2291 2004-11-16 19:35:41Z joel $

/* call it:
 * ATUTOR_PATH/get_rss.php?COURSE_ID-VERSION

	COURSE_ID: integer value of the course (non-zero)
	VERSION: [1|2] version of RSS
*/

/* assumption: if the rss files exist, then they're supposed to exist and are public.
 * if the rss file does not exist: check if this course has it enabled, and create it if needed.
 * that way rss is only ever created if it's ever called. if it's enabled and never viewed, then there's no need
 * to generate the files.
 */

$_user_location	= 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH . '/vitals.inc.php');

if (isset($_SERVER['QUERY_STRING'])) {
	$parts   = explode('-', $_SERVER['QUERY_STRING'], 2);
	$course  = intval($parts[0]);
	$version = intval($parts[1]);
} else {
	header('HTTP/1.1 404 Not Found');
	exit;
}

if (file_exists(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS' . $version . '.0.xml')) {
 	header('Content-Type: text/xml');
	header('Content-Length: ' . filesize(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS'.$version.'.0.xml'));
	echo file_get_contents(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS'.$version.'.0.xml');
	exit;
} // else: (rss does not exist)
if ($system_courses[$course]['rss'] && (($version == 1) || ($version == 2))) {
	// only RSS1 and 2 for now.

	require(AT_INCLUDE_PATH . 'classes/feedcreator.class.php');

	if (!is_dir(AT_CONTENT_DIR.'feeds/')){
		@mkdir(AT_CONTENT_DIR. 'feeds/', 0700);
	}
	if (!is_dir(AT_CONTENT_DIR . 'feeds/' . $course)){
		@mkdir(AT_CONTENT_DIR . 'feeds/' . $course . '/', 0700);
	}

	$rss = new UniversalFeedCreator();
	$rss->useCached();
	$rss->title          = $system_courses[$course]['title'];
	$rss->description    = $system_courses[$course]['description'];
	$rss->link           = AT_BASE_HREF;
	$rss->syndicationURL = AT_BASE_HREF;
	
	$image = new FeedImage();
	$image->title = 'ATutor Logo';
	$image->url   = AT_BASE_HREF . 'images/at-logo.v.3.gif';
	$image->link  = AT_BASE_HREF;
	$rss->image   = $image;

	$sql = "SELECT A.*, M.login from ".TABLE_PREFIX."news A, ".TABLE_PREFIX."members M WHERE A.course_id = ".$course." AND A.member_id=M.member_id ORDER BY A.date DESC LIMIT 5";

	$res = mysql_query($sql, $db);

	while ($data = mysql_fetch_assoc($res)) {
		$item = new FeedItem();
		
		$item->title          = $data['title'];
		$item->link           = AT_BASE_HREF . 'index.php';
		$item->description    = $data['body'];
		$item->date           = strtotime($data['date']);
		$item->source         = AT_BASE_HREF;
		$item->author         = $data['login'];

		$rss->addItem($item);
	}

 	header('Content-Type: text/xml');
	$rss->saveFeed('RSS'.$version.'.0', AT_CONTENT_DIR . 'feeds/' . $course . '/RSS' . $version . '.0.xml', false);

	echo file_get_contents(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS'.$version.'.0.xml');

	exit;
} // else: this course didn't enable rss

header('HTTP/1.1 404 Not Found');
exit;


?>