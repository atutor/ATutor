<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page	 = 'browse_courses';
$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/admin_categories.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

if (count($categories = get_categories()) == 0) {
	$no_cats = true;
	$_GET['show_all'] = 1;
}

?>
<h3><?php echo _AT('browse_courses'); ?></h3>

<?php

echo '<p align="center"><a href="search.php">'._AT('search_courses').'</a></p>';

if ($_GET['show_all'] == 0 && !$no_cats) {
	echo '<p align="center"><a href="browse.php?show_all=1">'._AT('cats_show_all_courses').'</a></p>';
} else if (!$no_cats) {
	echo '<p align="center"><a href="browse.php?show_all=0">'._AT('cats_show_course_categories').'</a></p>';
}

if($_GET['show_all'] == 0) {
	require(AT_INCLUDE_PATH.'html/browse_categories.inc.php');
} else {
	print_infos(_AT('about_browse'));
	echo '<br />';
	?>
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="65%" align="center" summary="">
		<tr>
			<th class="cyan2" colspan="2"><?php echo _AT('courses'); ?></th>			
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
				echo '<tr><td class="row1" width="150" valign="top"><strong>';
				echo '<a href="bounce.php?course='.$row['course_id'].'">'.$system_courses[$row['course_id']]['title'].'</a>';

				echo '</strong></td><td class="row1" valign="top"><small>';
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
				$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='y'";
				$c_result = mysql_query($sql, $db);
				$c_row	  = mysql_fetch_assoc($c_result);

				/* minus 1 because the instructor doesn't count */
				echo '<br />&middot; '._AT('enrolled').': '.max(($c_row['cnt']-1), 0).'<br />';
				echo '&middot; '._AT('created_date').': '.$row['created_date'].'<br />';
				echo '</small></td></tr>';
				if ($count < $num-1) {
					echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
				}
				$count++;
			} while ($row = mysql_fetch_assoc($result));
		} else {
			echo '<tr><td class="row1" colspan="2"><i>'._AT('no_courses').'</i></td></tr>';
		}
		echo '</table>';
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>