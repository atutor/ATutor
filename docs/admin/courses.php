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

if (isset($_GET['view'])) {
	header('Location: instructor_login.php?course='.$_GET['id']);
	exit;
} else if (isset($_GET['edit'])) {
	header('Location: edit_course.php?course='.$_GET['id']);
	exit;
} else if (isset($_GET['backups'])) {
	header('Location: backup/index.php?course='.$_GET['id']);
	exit;
} else if (isset($_GET['delete'])) {
	header('Location: delete_course.php?course='.$_GET['id']);
	exit;
}

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

if ($_GET['id']) {
	$and .= ' AND C.member_id='.intval($_GET['id']);
}

${'highlight_'.$col} = ' style="background-color: #fff;"';


$sql	  = "SELECT COUNT(*)-1 AS cnt, course_id FROM ".TABLE_PREFIX."course_enrollment WHERE approved='y' OR approved='a' GROUP BY course_id";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$enrolled[$row['course_id']] = $row['cnt'];
}

$sql	= "SELECT C.*, M.login FROM ".TABLE_PREFIX."courses C, ".TABLE_PREFIX."members M WHERE C.member_id=M.member_id $and ORDER BY $col $order";
$result = mysql_query($sql, $db);

if (!($row = mysql_fetch_assoc($result))) {
	echo '<p>'._AT('no_courses_found').'</p>';
?>
	<p align="center"><img src="images/create.jpg" alt="" height="15" width="16" class="menuimage17" /> <a href="admin/create_course.php"><?php echo _AT('create_course'); ?></a> | <img src="images/icons/default/forum-small.gif" alt="" height="15" width="16" class="menuimage" /> <a href="admin/forums.php"><?php echo _AT('forums'); ?></a></p>
<?php

} else {
	if ($_GET['id']) {
		echo '<h3>'._AT('courses_for_login', AT_print($row['login'], 'members.login')).'</h3>';
	}
		
	$msg->printAll();

	$num_rows = mysql_num_rows($result);
?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols" style="width: 90%;">
<thead>
<tr>
	<th scope="col">&nbsp;</th>

	<th scope="col"><?php echo _AT('title'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=title<?php echo SEP; ?>order=asc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('title_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('title_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=title<?php echo SEP; ?>order=desc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('title_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('title_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('instructor'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=asc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('instructor_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('instructor_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=desc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('instructor_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('instructor_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('access'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=access<?php echo SEP; ?>order=asc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('access_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('access_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=access<?php echo SEP; ?>order=desc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('access_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('access_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('created_date'); ?>  <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=created_date<?php echo SEP; ?>order=asc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('created_date_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('create_date_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=created_date<?php echo SEP; ?>order=desc<?php echo SEP; ?>id=<?php echo $_GET['id']; ?>" title="<?php echo _AT('created_date_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('create_date_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('category'); ?></th>

	<th scope="col"><?php echo _AT('enrolled'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="7"><input type="submit" name="view" value="<?php echo _AT('view'); ?>" /> 
					<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
					<input type="submit" name="backups" value="<?php echo _AT('backups'); ?>" /> 
					<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php
	do { ?>
		<tr onmousedown="document.form['m<?php echo $row['course_id']; ?>'].checked = true;">
			<td><input type="radio" name="id" value="<?php echo $row['course_id']; ?>" id="m<?php echo $row['course_id']; ?>"></td>

		<?php
		echo '<td>'.AT_print($row['title'], 'courses.title').'';


		echo '</td>';

		echo '<td>'.AT_print($row['login'],'members.login').'</td>';
		echo '<td>'._AT($row['access']).'&nbsp;</td>';
		echo '<td>'.$row['created_date'].'</td>';

		echo '<td>';
		if($current_cats[$row['cat_id']] != ''){
			echo $current_cats[$row['cat_id']];
		}else{
			echo _AT('cats_uncategorized');
		}
		echo '</td>';

		echo '<td>'.$enrolled[$row['course_id']].'</td>';

		echo '</tr>';

	} while ($row = mysql_fetch_assoc($result));
	echo '</tbody></table></form>';
}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>