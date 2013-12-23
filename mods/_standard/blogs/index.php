<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH .'classes/subscribe.class.php');

$sub = new subscription();

if (isset($_GET)){
	if ($_GET['subscribe'] == "set"){
		if($sub->set_subscription('blog',$_SESSION['member_id'],$_GET['group_id'])){;
			$msg->addFeedback('BLOG_SUBSCRIBED');
		}
	} else if ($_GET['subscribe'] == "unset"){
		$sub->unset_subscription('blog',$_SESSION['member_id'],$_GET['group_id']);
		$msg->addFeedback('BLOG_UNSUBSCRIBED');
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT G.group_id, G.title, G.modules FROM %sgroups G INNER JOIN %sgroups_types T USING (type_id) WHERE T.course_id=%d ORDER BY G.title";
$rows_groups = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id']));

echo '<ol id="tools">';

$blogs = false;
foreach($rows_groups as $row){
	if (strpos($row['modules'], '_standard/blogs') !== FALSE) {
		// retrieve the last posted date/time from this blog
		$sql = "SELECT MAX(date) AS date FROM %sblog_posts WHERE owner_type=%d AND owner_id=%d";
		$date_row = queryDB($sql, array(TABLE_PREFIX, BLOGS_GROUP, $row['group_id']), TRUE);
		
		if(count($date_row) > 0){
			$last_updated = ' - ' . _AT('last_updated', AT_date(_AT('forum_date_format'), $date_row['date'], AT_DATE_MYSQL_DATETIME));
		} else {
			$last_updated = '';
		}

		echo '<li class="top-tool"><a href="'.url_rewrite('mods/_standard/blogs/view.php?ot='.BLOGS_GROUP. SEP .'oid='.$row['group_id']).'">'.AT_print($row['title'], 'blog_posts.title').$last_updated.'</a>';
		
		// Check if subscribed and make appropriate button
		if(blogs_authenticate(BLOGS_GROUP, $row['group_id'])){
            if ($sub->is_subscribed('blog',$_SESSION['member_id'],$row['group_id'])){
                echo '<a href="'.$_SERVER['PHP_SELF'].'?group_id='.$row['group_id']. SEP .'subscribe=unset"><img border="0" src="'.AT_BASE_HREF.'images/unsubscribe-envelope.png" alt="" /> '._AT('blog_unsubscribe').'</a>';
            } else {
                echo '<a href="'.$_SERVER['PHP_SELF'].'?group_id='.$row['group_id']. SEP .'subscribe=set"><img border="0" src="'.AT_BASE_HREF.'images/subscribe-envelope.png" alt="" /> '._AT('blog_subscribe').'</a>';
            }
        }
		echo '</li>';
		$blogs = true;
	}
}
echo '</ol>';

if (!$blogs) {
	echo _AT('none_found');
}
?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>