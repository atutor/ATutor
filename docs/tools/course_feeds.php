<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca											d			*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$course = intval($_GET['course']);

if ($course == 0) {
	$course = $_SESSION['course_id'];
}


/* make sure we own this course that we're approving for! */

if (!(authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) && !(authenticate(AT_PRIV_COURSE_FEEDS, AT_PRIV_RETURN))) {
	$msg->printErrors('PREFS_NO_ACCESS');
	exit;
}

if ($_POST['cancel']) {
	header('Location: index.php');
	exit;
}

$title = _AT('course_feedsl');
require(AT_INCLUDE_PATH.'header.inc.php');
$msg->printAll();
echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>'."\n";

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/course_feeds-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('course_feeds');
}
echo '</h3>'."\n";

/* we own this course! */
//$msg->printErrors();
$msg->addHelp('RSS_FEEDS');
$msg->printALL();
?>
<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
<tr>
<th><?php echo _AT('feed_name');  ?></th>
<th><?php echo _AT('actions');  ?></th>
<th><?php echo _AT('xml_feeds');  ?></th></tr>
<tr><td><?php echo _AT('forum');  ?></td>
<td>
<?php 
	if(file_exists("../pub/feeds/".$_SESSION[course_id]."/forum_feedRSS1.0.xml")){ 
	echo '<a href="'.$_base_href.'tools/feeds/forum_feed.php?delete_rss1=1">'._AT('delete_rss1').'</a>';
	}else{ 
	echo '<a href="'.$_base_href.'tools/feeds/forum_feed.php?create_rss1=1">'._AT('create_rss1').'</a>';
 	} 
	if(file_exists("../pub/feeds/".$_SESSION[course_id]."/forum_feedRSS2.0.xml")){ 
	echo '<a href="'.$_base_href.'tools/feeds/forum_feed.php?delete_rss2=1">'._AT('delete_rss2').'</a>';
	}else{ 
	echo '<a href="'.$_base_href.'tools/feeds/forum_feed.php?create_rss2=1">'._AT('create_rss2').'</a>';
 	}  ?>
</td>
<td>
<?php
	if (file_exists("../pub/feeds/".$_SESSION[course_id]."/forum_feedRSS1.0.xml")) {
		echo '&nbsp;<a href="'.$_base_href.'pub/feeds/'.$_SESSION['course_id'].'/forum_feedRSS1.0.xml"><img src="'.$_base_href.'/images/rss_feed1.jpg" alt="RSS1.0" border="0"><a/>';
		$feed_exists = TRUE;
	}
	if (file_exists("../pub/feeds/".$_SESSION[course_id]."/forum_feedRSS2.0.xml")) {
		echo '&nbsp;<a href="'.$_base_href.'pub/feeds/'.$_SESSION['course_id'].'/forum_feedRSS2.0.xml"><img src="'.$_base_href.'/images/rss_feed.jpg" alt="RSS2.0" border="0"><a/>';
		$feed_exists = TRUE;
	}
	if(!$feed_exists){
		echo _AT('no feeds');
	}
?>
</td></tr>
<tr><td><?php echo _AT('announcements');  ?></td>
<td>
<?php 
	if(file_exists("../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS1.0.xml")){ 
	echo '<a href="'.$_base_href.'tools/feeds/announce_feed.php?delete_rss1=1">'._AT('delete_rss1').'</a>';
	}else{ 
	echo '<a href="'.$_base_href.'tools/feeds/announce_feed.php?create_rss1=1">'._AT('create_rss1').'</a>';
 	} 
	if(file_exists("../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS2.0.xml")){ 
	echo '<a href="'.$_base_href.'tools/feeds/announce_feed.php?delete_rss2=1">'._AT('delete_rss2').'</a>';
	}else{ 
	echo '<a href="'.$_base_href.'tools/feeds/announce_feed.php?create_rss2=1">'._AT('create_rss2').'</a>';
 	}  ?>
</td>
<td>
<?php
	if (file_exists("../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS1.0.xml")) {
		echo '&nbsp;<a href="'.$_base_href.'pub/feeds/'.$_SESSION['course_id'].'/announce_feedRSS1.0.xml"><img src="'.$_base_href.'/images/rss_feed1.jpg" alt="RSS1.0" border="0"><a/>';
		$feed_exists = TRUE;
	}
	if (file_exists("../pub/feeds/".$_SESSION[course_id]."/announce_feedRSS2.0.xml")) {
		echo '&nbsp;<a href="'.$_base_href.'pub/feeds/'.$_SESSION['course_id'].'/announce_feedRSS2.0.xml"><img src="'.$_base_href.'/images/rss_feed.jpg" alt="RSS2.0" border="0"><a/>';
		$feed_exists = TRUE;
	}
	if(!$feed_exists){
		echo _AT('no feeds');
	}
?>
</td></tr>
</table>
<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
