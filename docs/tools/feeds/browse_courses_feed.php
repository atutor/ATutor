<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: new_thread.php 2212 2004-11-09 17:09:43Z greg $

/* Creates browse courses feeds  */
define('AT_INCLUDE_PATH' , '../../include/');
include(AT_INCLUDE_PATH."rss/feedcreator.class.php");
//$page	 = 'browse_courses';
//$_user_location = 'admin';	

//define('AT_INCLUDE_PATH' , '../../include/');
//global $_base_href;
//include(AT_INCLUDE_PATH."rss/feedcreator.class.php");

$_GET['course'] ==0;
if($_POST['title']){
	$write_feed = FALSE;
	//$feed_type = "RSS2.0";
	//define ('AT_PUB_PATH','../pub');
}else if($_GET['d'] == 2){
	$delete_course = TRUE;
	//define ('AT_PUB_PATH','../pub');
	$write_feed = FALSE;
}else{
	require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
	global $savant;
	$msg =& new Message($savant);
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	if (!is_dir(AT_CONTENT_DIR."feeds/")){
		mkdir(AT_CONTENT_DIR."feeds/", 0777);
	}
	if (!is_dir(AT_CONTENT_DIR."feeds/".$_GET['course'])){
		mkdir(AT_CONTENT_DIR."feeds/".$_GET['course']."/", 0777);
	}
	if($_GET['delete_rss1'] == 1){
		//$feed_type=$_GET['feed_type'];
		if(unlink(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml")){
			$msg->addFeedback('FEED_DELETED');
		}else{
			$msg->addError('FEED_NOT_DELETED');
		} 
		header('Location: '.$_base_href.'admin/course_feeds.php');
		exit;	
	}else if($_GET['delete_rss2'] == 1){
		if(unlink(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml")){
			$msg->addFeedback('FEED_DELETED');
		}else{
			$msg->addError('FEED_NOT_DELETED');
		} 
		header('Location: '.$_base_href.'admin/course_feeds.php');
		exit;	
	
	}else  if($_GET['create_rss1'] == 1){
		$write_feed = FALSE;
		if (!file_exists(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml")) {
			$fp = fopen(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml", 'w+');	
			$msg->addFeedback('FEED_CREATED');
			if($_GET['create'] == 1){
 				header('Location: '.$_base_href.'admin/course_feeds.php');
				exit;
			}
		}
	}else if($_GET['create_rss2'] == 1){
		$write_feed = FALSE;
		if (!file_exists(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml")) {
			$fp = fopen(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml", 'w+');
			$msg->addFeedback('FEED_CREATED');
			if($_GET['create'] == 1){

				header('Location: '.$_base_href.'admin/course_feeds.php');
				exit;
			}
		}
	}
	/*
	if($_GET['delete_rss1'] == 1){
		if(unlink("../../pub/feeds/0/browse_courses_feedRSS1.0.xml")){
			$msg->addFeedback('FEED_DELETED');
		}else{
			$msg->addError('FEED_NOT_DELETED');
		} 
		header('Location: '.$_base_href.'admin/course_feeds.php');
		exit;	
	}else if($_GET['delete_rss2'] == 1){
		if(unlink("../../pub/feeds/0/browse_courses_feedRSS2.0.xml")){
			$msg->addFeedback('FEED_DELETED');
		}else{
			$msg->addError('FEED_NOT_DELETED');
		} 
		header('Location: '.$_base_href.'admin/course_feeds.php');
		exit;	
	
	}else  if($_GET['create_rss1'] == 1){
		define ("AT_PUB_PATH","../../pub");		
		$feed_type = "RSS1.0";
		$write_feed = FALSE;
		if (!file_exists("../../pub/feeds/0/browse_courses_feedRSS1.0.xml")) {
			$fp = fopen("../../pub/feeds/0/browse_courses_feedRSS1.0.xml", 'w+');
			$msg->addFeedback('FEED_CREATED');
			if($_GET['create'] == 1){
				header('Location: '.$_base_href.'tools/course_feeds.php');
				exit;
			}
		}
	}else if($_GET['create_rss2'] == 1){
		define ("AT_PUB_PATH","../../pub");
		$feed_type = "RSS2.0";	
		$write_feed = FALSE;
		if (!is_dir("../../pub/feeds/0")){
			mkdir("../../pub/feeds/0/", 0777);
		}
		if (!file_exists("../../pub/feeds/0/browse_courses_feedRSS2.0.xml")) {
			$fp = fopen("../../pub/feeds/0/browse_courses_feedRSS2.0.xml", 'w+');
			$msg->addFeedback('FEED_CREATED');
			if($_GET['create'] == 1){
	
				header('Location: '.$_base_href.'tools/course_feeds.php');
				exit;
			}
		}
	}*/
}
$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = SITE_NAME;
$rss->description = "-";
$rss->link = $_base_href;
$rss->syndicationURL = "http://www.atutor.ca/";
$image = new FeedImage();
$image->title = "ATutor Logo";
$image->url = $_base_href."images/at-logo.v.3.gif";
$image->link = "http://www.atutor.ca";
$image->description = " - ";
$rss->image = $image;

$sql= "SELECT C.*, M.member_id, M.first_name, M.last_name from ".TABLE_PREFIX."courses C, ".TABLE_PREFIX."members M WHERE C.hide<>1 AND C.member_id=M.member_id ORDER BY created_date DESC";
$res = mysql_query($sql, $db);

while ($data = mysql_fetch_object($res)) {
    $item = new FeedItem();
    $item->title = $data->title;
    $item->link = $_base_href."bounce.php?course=".$data->course_id;
    $item->description = $data->description;
    $item->descriptionTruncSize = 50;
    $item->date = strtotime($data->created_date);
    $item->source = "http://www.atutor.ca/";
    $item->author = $data->first_name." ".$data->last_name;
    $rss->addItem($item);
}

if($_POST['title']){
	if(file_exists(AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS2.0.xml")){
		$rss->saveFeed("RSS2.0", AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS2.0.xml", $write_feed);
	}
	if(file_exists(AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS1.0.xml")){
		$rss->saveFeed("RSS1.0", AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS1.0.xml", $write_feed);
	}
	
	$_POST['instructor'] = $_SESSION['member_id'];
	$errors = add_update_course($_POST);
	cache_purge('system_courses','system_courses');
	return $new_course_id;
	if (is_numeric($errors)) {
		$msg->addFeedback('COURSE_CREATED');
		header('Location: ../bounce.php?course='.$errors.SEP.'p='.urlencode('index.php'));
		exit;
	}
}else if($delete_course){
	if(file_exists(AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS2.0.xml")){
		$rss->saveFeed("RSS2.0", AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS2.0.xml", $write_feed);
	}
	if(file_exists(AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS1.0.xml")){
		$rss->saveFeed("RSS1.0", AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS1.0.xml", $write_feed);
	}
	$msg->addFeedback('COURSE_DELETED');
	header('Location: index.php');
	exit;
	
}else{
	$rss->saveFeed($_GET['version'], AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml",  $write_feed);
	header('Location: '.$_base_href.'admin/course_feeds.php');
	exit;
}
?>