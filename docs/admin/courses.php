<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if (!$_SESSION['s_is_super_admin']) {
	exit;
}

require(AT_INCLUDE_PATH.'admin_html/header.inc.php');
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

${'highlight_'.$col} = ' style="text-decoration: underline;"';

$sql	= "SELECT C.*, M.login FROM ".TABLE_PREFIX."courses C, ".TABLE_PREFIX."members M WHERE C.member_id=M.member_id $and ORDER BY $col $order";
$result = mysql_query($sql, $db);

if (!($row = mysql_fetch_array($result))) {
	echo '<h2>'._AT('courses').'</h2>';
	echo '<p>'._AT('no_courses_found').'</p>';
} else {
	if ($_GET['member_id']) {
		echo '<h2>'._AT('courses').' for instructor '.$row['login'].'</h2>';
	} else {
		echo '<h2>'._AT('courses').'</h2>';
	}

	$num_rows = mysql_num_rows($result);
?>

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="">
<tr>
	<th scope="col"><small<?php echo $highlight_course_id; ?>><?php echo _AT('id'); ?> <a href="<?php echo $PHP_SELF; ?>?col=course_id<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('id_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=course_id<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('id_descending'); ?>">D</a></small></th>

	<th scope="col"><small<?php echo $highlight_title; ?>><?php echo _AT('title'); ?> <a href="<?php echo $PHP_SELF; ?>?col=title<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('title_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=title<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('title_descending'); ?>">D</a></small></th>

	<th scope="col"><small<?php echo $highlight_login; ?>><?php echo _AT('instructor'); ?> <a href="<?php echo $PHP_SELF; ?>?col=login<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('instructor_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=login<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('instructor_descending'); ?>">D</a></small></th>

	<th scope="col"><small<?php echo $highlight_access; ?>><?php echo _AT('access'); ?>  <a href="<?php echo $PHP_SELF; ?>?col=access<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('access_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=access<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('access_descending'); ?>">D</a></small></th>
	
	<th scope="col"><small<?php echo $highlight_access; ?>><?php echo _AT('category'); ?></small></th>

	<th scope="col"><small<?php echo $highlight_created_date; ?>><?php echo _AT('created_date'); ?>  <a href="<?php echo $PHP_SELF; ?>?col=created_date<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('created_date_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=created_date<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('created_date_descending'); ?>">D</a></small></th>

	<th scope="col"><small<?php echo $highlight_tracking; ?>><?php echo _AT('tracking'); ?> <a href="<?php echo $PHP_SELF; ?>?col=tracking<?php echo SEP; ?>order=asc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('tracking_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=tracking<?php echo SEP; ?>order=desc<?php echo SEP; ?>member_id=<?php echo $_GET['member_id']; ?>" title="<?php echo _AT('tracking_descending'); ?>">D</a></small></th>

	<th><small>&nbsp;</small></th>
</tr>
<?php
	do {
		echo '<tr>';
		echo '<td class="row1"><small>'.$row['course_id'].'</small></td>';
		echo '<td class="row1"><small><a href="admin/course.php?course='.$row['course_id'].'"><b>'.$row['title'].'</b></a></small>';

		echo ' <small class="spacer">( <a href="admin/instructor_login.php?course='.$row['course_id'].'">'._AT('view').'</a> )</small>';
		
		echo '</td>';

		echo '<td class="row1"><small><a href="admin/profile.php?member_id='.$row['member_id'].'"><b>'.$row['login'].'</b></a></small></td>';
		echo '<td class="row1"><small>'._AT($row['access']).'&nbsp;</small></td>';
		echo '<td class="row1"><small>';
		if($current_cats[$row['cat_id']] != ''){
			echo $current_cats[$row['cat_id']];
		}else{
			echo _AT('cats_uncategorized');
		}
		echo '&nbsp;</small></td>';

		echo '<td class="row1"><small>'.$row['created_date'].'&nbsp;</small></td>';
		if($row['tracking']){
			echo '<td class="row1"><small>'._AT($row['tracking']).'&nbsp;</small></td>';
		}else{
			echo '<td class="row1"><small class="spacer">'._AT('na').'&nbsp;</small></td>';
		}
		echo '<td class="row1"><a href="admin/delete_course.php?course='.$row['course_id'].'"><img src="images/icon_delete.gif" border="0" alt="'._AT('delete').'" title="'._AT('delete').'"/></a></td>';
		echo '</tr>';
		if ($count < $num_rows-1) {
			echo '<tr><td height="1" class="row2" colspan="8"></td></tr>';
		}
		$count++;
	} while ($row = mysql_fetch_array($result));
	echo '</table>';
}

	require(AT_INCLUDE_PATH.'admin_html/footer.inc.php');
?>
