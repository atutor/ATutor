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

$section = 'users';
$page	 = 'browse'; 
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/admin_categories.inc.php');

require(AT_INCLUDE_PATH.'basic_html/header.php');

if (count($categories = get_categories()) <= 0) {
	$no_cats = true;
	$_GET['show_all'] = 1;
}

?>
<h2><?php echo _AT('browse_courses'); ?></h2>

<?php

if ($_GET['show_all'] != 1 && !$no_cats) {
	echo '[ <a href="?show_all=1">'._AT('cats_show_all_courses').'</a> ]';
} else if (!$no_cats) {
	echo '[ <a href="?show_all=0">'._AT('cats_show_course_categories').'</a> ]';
}

if(!$_GET['show_all'] == 1) {
	require(AT_INCLUDE_PATH.'html/browse_categories.inc.php');
} else {
	print_infos(_AT('about_browse'));
	echo '<br />';
	?>
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" align="center" summary="">
		<tr>
			<th scope="col"><?php echo _AT('course_name'); ?></th>
			<th scope="col"><?php echo _AT('description'); ?></th>
		</tr>
	<?php
		$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE hide=0 ORDER BY title";
		$result = mysql_query($sql,$db);

		$num = mysql_num_rows($result);
		if ($row = mysql_fetch_array($result)) {
			do {
				echo '<tr><td class="row1" width="150" valign="top"><b>';
				echo '<a href="bounce.php?course='.$row[course_id].'">'.$system_courses[$row['course_id']]['title'].'</a>';

				echo '</b></td><td class="row1" valign="top">';
				echo '<small>';
				echo $row['description'];

				echo '<br /><br />&middot; '._AT('access').': ';
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
				$sql = "SELECT COUNT(*) FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='y'";
				$c_result = mysql_query($sql, $db);
				$c_row	  = mysql_fetch_array($c_result);

				/* minus 1 because the instructor doesn't count */
				echo '<br />&middot; '._AT('enrolled').': '.max(($c_row[0]-1), 0).'<br />';
				echo '&middot; '._AT('created_date').': '.$row[created_date].'<br />';

				echo '</small></td>';
				echo '</tr>';
				if ($count < $num-1) {
					echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
				}
				$count++;
			} while ($row = mysql_fetch_array($result));
		} else {
			echo '<tr><td class=row1 colspan=3><i>'._AT('no_courses').'</i></td></tr>';
		}
		echo '</table>';
	//}
}
	require(AT_INCLUDE_PATH.'basic_html/footer.php');
?>