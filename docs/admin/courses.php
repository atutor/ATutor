<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

$page = 'courses';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql = "SELECT * from ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
$result = mysql_query($sql, $db);
if(mysql_num_rows($result) != 0){
	while($row = mysql_fetch_array($result)){
		$current_cats[$row['cat_id']] = $row['cat_name'];
	}
}

if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'title';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'asc';
}

if ($_GET['member_id']) {
	$and .= ' AND C.member_id='.intval($_GET['member_id']);
}

${'highlight_'.$col} = ' style="font-size: 1em;"';

$sql	= "SELECT C.*, M.login FROM ".TABLE_PREFIX."courses C, ".TABLE_PREFIX."members M WHERE C.member_id=M.member_id $and ORDER BY $col $order";
$result = mysql_query($sql, $db);

if (!($row = mysql_fetch_assoc($result))) {
	echo '<p>'._AT('no_courses_found').'</p>';
?>
	<p align="center"><img src="images/create.jpg" alt="" height="15" width="16" class="menuimage17" /> <a href="admin/create_course.php"><?php echo _AT('create_course'); ?></a> | <img src="images/icons/default/forum-small.gif" alt="" height="15" width="16" class="menuimage" /> <a href="admin/forums.php"><?php echo _AT('forums'); ?></a></p>
<?php

} else {
	if ($_GET['member_id']) {
		echo '<h3>'._AT('courses_for_login', AT_print($row['login'], 'members.login')).'</h3>';
	}
		
	$msg->printAll();

	$num_rows = mysql_num_rows($result);
?>

<p align="center"><img src="images/create.jpg" alt="" height="15" width="16" class="menuimage17" /> <a href="admin/create_course.php"><?php echo _AT('create_course'); ?></a> | <img src="images/icons/default/forum-small.gif" alt="" height="15" width="16" class="menuimage" /> <a href="admin/forums.php"><?php echo _AT('forums'); ?></a> <?php
		/* | <img src="images/icons/default/course_feeds-small.gif" alt="" height="15" width="16" class="menuimage" /> <a href="admin/course_feeds.php"> _AT('rss_feeds'); </a>
	*/?>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%" align="center">
<tr>
	<th colspan="8" class="cyan"><?php echo _AT('courses'); ?></th>
</tr>
<tr>
	<th scope="col" class="cat"><small<?php echo $highlight_title; ?>><?php echo _AT('title'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=title<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('title_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('title_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=title<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('title_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('title_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th scope="col" class="cat"><small<?php echo $highlight_login; ?>><?php echo _AT('instructor'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('instructor_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('instructor_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('instructor_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('instructor_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th scope="col" class="cat"><small<?php echo $highlight_access; ?>><?php echo _AT('access'); ?>  <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=access<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('access_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('access_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=access<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('access_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('access_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>
	
	<th scope="col" class="cat"><small<?php echo $highlight_access; ?>><?php echo _AT('category'); ?></small></th>

	<th scope="col" class="cat"><small<?php echo $highlight_created_date; ?>><?php echo _AT('created_date'); ?>  <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=created_date<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('created_date_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('create_date_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=created_date<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('created_date_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('create_date_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th scope="col" class="cat"><small<?php echo $highlight_tracking; ?>><?php echo _AT('tracking'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=tracking<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('tracking_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('tracking_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=tracking<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('tracking_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('tracking_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th class="cat"><small>&nbsp;</small></th>
</tr>
<?php
	do {
		echo '<tr>';
		echo '<td class="row1"><small><a href="admin/edit_course.php?course_id='.$row['course_id'].'"><strong>'.AT_print($row['title'], 'courses.title').'</strong></a></small>';

		echo ' <small class="spacer">( <a href="admin/instructor_login.php?course='.$row['course_id'].'">'._AT('view').'</a> | <a href="admin/backup/index.php?course='.$row['course_id'].'">'._AT('backups').'</a> )</small>';
		
		echo '</td>';

		echo '<td class="row1"><small><a href="admin/profile.php?member_id='.$row['member_id'].'"><strong>'.AT_print($row['login'],'members.login').'</strong></a></small></td>';
		echo '<td class="row1"><small>'._AT($row['access']).'&nbsp;</small></td>';
		echo '<td class="row1"><small>';
		if($current_cats[$row['cat_id']] != ''){
			echo $current_cats[$row['cat_id']];
		}else{
			echo _AT('cats_uncategorized');
		}
		echo '&nbsp;</small></td>';

		echo '<td class="row1"><small>'.$row['created_date'].'</small></td>';
		echo '<td class="row1"><small>';
		if ($row['tracking']) {
			echo _AT($row['tracking']);
		}
		echo '</small></td>';
		echo '<td class="row1"><a href="admin/delete_course.php?course='.$row['course_id'].'"><img src="images/icon_delete.gif" border="0" alt="'._AT('delete').'" title="'._AT('delete').'" width="16" height="18" class="menuimage18" /></a></td>';
		echo '</tr>';
		if ($count < $num_rows-1) {
			echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
		}
		$count++;
	} while ($row = mysql_fetch_assoc($result));
	echo '</table>';
}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>