<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page = 'browse_courses';
$_user_location	= 'users';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('browse_courses');

$title = _AT('browse_courses');

$sql = "SELECT * from ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
$result = mysql_query($sql,$db);
if(mysql_num_rows($result) == 0){
	$empty = true;
	$_GET['show_all'] = 1;
	if($_GET['show_all'] == 0){
		$infos[] = AT_INFOS_NO_CATEGORIES;
	}
}

	require(AT_INCLUDE_PATH.'header.inc.php');
	echo '<h2>'._AT('browse_courses').'</h2>';

if(!$empty){
	if($_GET['show_all'] != 1){
		echo '<p align="center"><a href="'.$_SERVER['PHP_SELF'].'?show_all=1">'._AT('cats_show_all_courses').'</a></p>';
	}else{
		echo '<p align="center"><a href="'.$_SERVER['PHP_SELF'].'?show_all=0">'._AT('cats_show_course_categories').'</a></p><br />';
	}
}

if ($_GET['show_all'] == 0){
	require(AT_INCLUDE_PATH.'html/browse_categories.inc.php');
} else {
	while($row = mysql_fetch_array($result)){
		$current_cats[$row['cat_id']] = $row['cat_name'];
	}

	?>
	<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
	<tr>
		<th class="cyan" colspan="2"><?php echo _AT('courses'); ?></th>			
	</tr>
	<tr>
		<th class="cat" scope="col"><?php echo _AT('course_name'); ?></th>
		<th class="cat" scope="col"><?php echo _AT('description'); ?></th>
	</tr>
	<?php
		$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE hide=0 ORDER BY title";
		$result = mysql_query($sql,$db);

		$num = mysql_num_rows($result);
		if ($row = mysql_fetch_assoc($result)) {
			do {
				echo '<tr><td class="row1" width="150" valign="top">';
				echo '<strong><a href="bounce.php?course='.$row['course_id'].'">'.$system_courses[$row['course_id']]['title'].'</a></strong>';
				echo '</td><td class="row1" valign="top">';
				echo '<small>';
				echo $row['description'];
				echo '<br /><br />&middot; '. _AT('access').': ';

				$pending = '';
				switch ($row['access']){
					case 'public':
						echo _AT('public');
						break;
					case 'protected':
						echo _AT('protected');
						break;
					case 'private':
						echo _AT('private');
						break;
				}
				echo '<br />&middot; '. _AT('category').': ';
				if ($row['cat_id'] != 0) {
					echo $current_cats[$row['cat_id']];
				} else {
					echo _AT('cats_uncategorized');
				}

				$sql	  = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='y'";
				$c_result = mysql_query($sql, $db);
				$c_row	  = mysql_fetch_assoc($c_result);

				/* minus 1 because the instructor doesn't count */
				echo '<br />&middot; '._AT('enrolled').': '.max(($c_row['cnt']-1), 0).'<br />';
				echo '&middot; '. _AT('created').': '.$row['created_date'].'<br />';
				echo '&middot; <a href="users/contact_instructor.php?course='.$row['course_id'].SEP.'from_browse=1">'._AT('contact_instructor').'</a>';

				echo '</small></td>';
				echo '</tr>';
				if ($count < $num-1) {
					echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
				}
				$count++;
			} while ($row = mysql_fetch_assoc($result));
		} else {
			echo '<tr><td class="row1" colspan="3"><i>'._AT('no_courses').'</i></td></tr>';
		}
		echo '</table>';
}
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>