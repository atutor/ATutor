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

/* Creates course announcement feeds  */
	
define('AT_INCLUDE_PATH' , '../../include/');
include(AT_INCLUDE_PATH."rss/feedcreator.class.php");
//global $myLang;
//$my_charset = $myLang->getCharacterSet();
//if (!is_dir("../../pub/feeds/".$_SESSION[course_id])){
//	mkdir("../../pub/feeds/".$_SESSION[course_id]."/", 0755);
//}
//exit;
if($_POST['title']){
	$write_feed = FALSE;
	$feed_type = "RSS2.0";
	define ('AT_PUB_PATH','../pub');
}else{
	require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	
		if (!is_dir(AT_CONTENT_DIR."feeds/")){
		mkdir(AT_CONTENT_DIR."feeds/", 0777);
	}
	if (!is_dir(AT_CONTENT_DIR."feeds/".$_SESSION[course_id])){
		mkdir(AT_CONTENT_DIR."feeds/".$_SESSION[course_id]."/", 0777);
	}
	
	
	if($_GET['delete_rss1'] == 1){
		//$feed_type=$_GET['feed_type'];
		if(unlink(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml")){
			$msg->addFeedback('FEED_DELETED');
		}else{
			$msg->addError('FEED_NOT_DELETED');
		} 
		header('Location: '.$_base_href.'tools/course_feeds.php');
		exit;	
	}else if($_GET['delete_rss2'] == 1){
		if(unlink(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml")){
			$msg->addFeedback('FEED_DELETED');
		}else{
			$msg->addError('FEED_NOT_DELETED');
		} 
		header('Location: '.$_base_href.'tools/course_feeds.php');
		exit;	
	
	}else  if($_GET['create_rss1'] == 1){
		$write_feed = FALSE;
		if (!file_exists(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml")) {
			$fp = fopen(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml", 'w+');
			$msg->addFeedback('FEED_CREATED');
			if($_GET['create'] == 1){
				header('Location: '.$_base_href.'tools/course_feeds.php');
				exit;
			}
		}
	}else if($_GET['create_rss2'] == 1){
		$write_feed = FALSE;
		if (!file_exists(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xml")) {
			$fp = fopen(AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].".".$_GET['version'].".xmll", 'w+');
			$msg->addFeedback('FEED_CREATED');
			if($_GET['create'] == 1){

				header('Location: '.$_base_href.'tools/course_feeds.php');
				exit;
			}
		}
	}

	
	/*
	if($_GET['delete_rss1'] == 1){
		if(unlink("../../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS1.0.xml")){
			$msg->addFeedback('FEED_DELETED');
		}else{
			$msg->addError('FEED_NOT_DELETED');
		} 
		header('Location: '.$_base_href.'tools/course_feeds.php');
		exit;	
	}else if($_GET['delete_rss2'] == 1){
		if(unlink("../../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS2.0.xml")){
			$msg->addFeedback('FEED_DELETED');
		}else{
			$msg->addError('FEED_NOT_DELETED');
		} 
		header('Location: '.$_base_href.'tools/course_feeds.php');
		exit;	
	
	}else  if($_GET['create_rss1'] == 1){
		define ("AT_PUB_PATH","../../pub");		
		$feed_type = "RSS1.0";
		$write_feed = FALSE;
		if (!file_exists("../../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS1.0.xml")) {
			$fp = fopen("../../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS1.0.xml", 'w+');
			if($_GET['create'] == 1){
				$msg->addFeedback('FEED_CREATED');
				header('Location: '.$_base_href.'tools/course_feeds.php');
				exit;
			}
		}
	}else if($_GET['create_rss2'] == 1){
		define ("AT_PUB_PATH","../../pub");
		$feed_type = "RSS2.0";	
		$write_feed = FALSE;
		if (!is_dir("../../pub/feeds/".$_SESSION[course_id])){
			mkdir("../../pub/feeds/".$_SESSION[course_id]."/", 0755);
		}
		if (!file_exists("../../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS2.0.xml")) {
			$fp = fopen("../../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS2.0.xml", 'w+');
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
$rss->title = $_SESSION['course_title'];
$rss->description = "-";
$rss->link = $_base_href;
$rss->syndicationURL = "http://www.atutor.ca/".$_SERVER['PHP_SELF'];
$image = new FeedImage();
$image->title = "ATutor Logo";
$image->url = $_base_href."images/at-logo.v.3.gif";
$image->link = "http://www.atutor.ca";
$image->description = " - ";
$rss->image = $image;

$sql = "SELECT A.*, M.first_name, M.last_name from ".TABLE_PREFIX."news A, ".TABLE_PREFIX."members M WHERE A.course_id = ".$_SESSION['course_id']." AND A.member_id=M.member_id ORDER  BY date DESC LIMIT 5";

$res = mysql_query($sql, $db);

while ($data = mysql_fetch_object($res)) {
    $item = new FeedItem();
    $item->title = $data->title;
    $item->link = $_base_href."index.php";
    $item->description = $data->body;
    $item->descriptionTruncSize = 50;
    $item->date = strtotime($data->date);
    $item->source = "http://www.atutor.ca/";
    $item->author = $data->first_name." ".$data->last_name;
    $rss->addItem($item);
}
 
if($_POST['title']){
	if(file_exists(AT_CONTENT_DIR."feeds/".$_SESSION[course_id]."/announce_feed.RSS2.0.xml")){
		$rss->saveFeed($feed_type, AT_CONTENT_DIR."feeds/".$_SESSION[course_id]."/announce_feed.RSS2.0.xml", $write_feed);
	}
	if(file_exists(AT_CONTENT_DIR."feeds/".$_SESSION[course_id]."/announce_feed.RSS1.0.xml")){
		$rss->saveFeed($feed_type, AT_CONTENT_DIR."feeds/".$_SESSION[course_id]."/announce_feed.RSS1.0.xml", $write_feed);
	}
	header('Location: '.$_base_href.'index.php?fid='.$_POST['fid'].'');
	exit;	
}else{
	$rss->saveFeed($_GET['version'], AT_CONTENT_DIR."feeds/".$_GET['course']."/".$_GET['type'].'.'.$_GET['version'].".xml",  $write_feed);
	header('Location: '.$_base_href.'tools/course_feeds.php');
	exit;
}
?>