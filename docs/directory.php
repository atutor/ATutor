<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: directory.php 3580 2005-02-28 17:30:52Z shozubq $

$page = 'directory';
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

/* should only be in here if you are enrolled in the course!!!!!! */


if ($_GET['col'] && $_GET['order']) {
	$col   = $addslashes($_GET['col']);
	$order = $addslashes($_GET['order']);
} else {
	//set default sorting order
	$col   = 'login';
	$order = 'asc';
}

/* look through enrolled students list */
$sql = "SELECT C.member_id, C.approved, C.role, M.login FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$_SESSION[course_id] AND C.member_id=M.member_id AND (C.approved='a' OR C.approved='y') ORDER BY $col $order";
$result_query = mysql_query($sql, $db);

require(AT_INCLUDE_PATH.'header.inc.php');

if ($result_query) { ?>
	<table class="data static" rules="cols" summary="">
	<thead>
	<tr>
		<th scope="col"><?php echo _AT('username') . ' <a href="' . $_SERVER['PHP_SELF'] . '?col=login' . SEP . 'order=asc" title="' . _AT('username_ascending') . '"><img src="images/asc.gif" alt="' . _AT('username_ascending') . '" border="0" height="7" width="11" /></a> <a href="' . $_SERVER['PHP_SELF'] . '?col=login' . SEP . 'order=desc" title="' . _AT('username_descending') . '"><img src="images/desc.gif" alt="' . _AT('username_descending') . '" border="0" height="7" width="11" /></a>'; ?></th>
		<th scope="col"><?php echo _AT('role') . ' <a href="' . $_SERVER['PHP_SELF'] . '?col=role' . SEP . 'order=asc" title="' . _AT('role_ascending') . '"><img src="images/asc.gif" alt="' . _AT('role_ascending') . '" border="0" height="7" width="11" /></a> <a href="' . $_SERVER['PHP_SELF'] . '?col=role' . SEP . 'order=desc" title="' . _AT('role_descending') . '"><img src="images/desc.gif" alt="' . _AT('role_descending') . '" border="0" height="7" width="11" /></a>'; ?></th>
	</tr>
	</thead>
	<tbody>
<?php
	while ($row = mysql_fetch_assoc($result_query)) {
		echo '<tr>';
		
		/* if enrolled display login, role */
		if ($row['approved'] == 'y') {
			echo '<td>'.$row['login'].'</td>';
			if ($row['role'] != '') {
				echo '<td>'.$row['role'].'</td>';
			} else {
				echo '<td>'._AT('student').'</td>';
			}
		}
		
		/* if alumni display alumni */
		else if ($row['status'] == 'a') {
			echo '<td>'.$row['login'].'</td>';
			echo '<td>'.$row['approved'].'</td>';
		}

		echo '</tr>';
	}	
	echo '</tbody>';
	echo '</table>';
}

// no students
else {
	echo _AT('no_students');
}

require(AT_INCLUDE_PATH.'footer.inc.php');