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
$_include_path = '../../include/';
require($_include_path.'vitals.inc.php');

if (!$_SESSION['s_is_super_admin']) {
	exit;
}
$id = $_GET['id'];
$L = $_GET['L'];
require($_include_path.'admin_html/header.inc.php'); 

echo '<h2>'._AT('users').'</h2>';
$letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', _AT('all'));


if ($_GET['col']) {
	$col = addslashes($_GET['col']);
} else {
	$col = 'login';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
} else {
	$order = 'asc';
}

if (!$_GET['L']) {
	$_GET['L'] = 'A';
}

${'highlight_'.$col} = ' style="text-decoration: underline;"';

	echo '<div align="center">';

	$letter			 = '';
	$letter_sql		 = '';
	$next_letter_sql = '';
	for($i=0; $i<27; $i++) {
		echo '<a href="'.$PHP_SELF.'?L='.$letters[$i].'#list">';
		if ($letters[$i] == $_GET['L']) {
			echo '<b>'.$letters[$i].'</b>';

			$letter = $letters[$i];
			if ($i < 26) {
				$letter_sql = " AND login>='$letter'";
			}
			if ($i < 25) {
				$next_letter_sql = " AND login<'{$letters[$i+1]}'";
			}
		} else {
			echo $letters[$i];
		}

		echo '</a>';
		
		if ($i < 26) {
			echo ' | ';
		}
	}

	echo '</div>';
?>
<hr />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th scope="col"><a name="list"></a><small<?php echo $highlight_member_id; ?>><?php echo _AT('id'); ?><a href="<?php echo $PHP_SELF; ?>?col=member_id<?php echo SEP; ?>order=asc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('id_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=member_id<?php echo SEP; ?>order=desc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('id_descending'); ?>">D</a></small></th>

	<th scope="col"><small<?php echo $highlight_login; ?>><?php echo _AT('username'); ?> <a href="<?php echo $PHP_SELF; ?>?col=login<?php echo SEP; ?>order=asc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('username_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=login<?php echo SEP; ?>order=desc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('username_descending'); ?>">D</a></small></th>

	<th scope="col"><small<?php echo $highlight_first_name; ?>><?php echo _AT('first_name'); ?> <a href="<?php echo $PHP_SELF; ?>?col=first_name<?php echo SEP; ?>order=asc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('first_name_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=first_name<?php echo SEP; ?>order=desc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('first_name_descending'); ?>">D</a></small></th>

	<th scope="col"><small<?php echo $highlight_last_name; ?>><?php echo _AT('last_name'); ?> <a href="<?php echo $PHP_SELF; ?>?col=last_name<?php echo SEP; ?>order=asc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('last_name_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=last_name<?php echo SEP; ?>order=desc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('last_name_descending'); ?>">D</a></small></th>

	<th scope="col"><small<?php echo $highlight_status; ?>><?php echo _AT('status'); ?> <a href="<?php echo $PHP_SELF; ?>?col=status<?php echo SEP; ?>order=desc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('status_ascending'); ?>">A</a>/<a href="<?php echo $PHP_SELF; ?>?col=status<?php echo SEP; ?>order=asc<?php echo SEP; ?>L=<?php echo $_GET['L']; ?>#list" title="<?php echo _AT('status_descending'); ?>">D</a></small></th>

	<th><small><?php echo _AT('courses'); ?> </small></th>
	<th><small>&nbsp;</small></th>
	</tr>
<?php

$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE 1 $letter_sql $next_letter_sql ORDER BY $col $order";
$result = mysql_query($sql);

if (!($row = mysql_fetch_array($result))) {
	if($_GET['L']){
		echo '<tr><td colspan="7" class="row1">'._AT('no_users_found_for').' <b>'.$_GET['L'].'</b></td></tr>';
	}

} else {
	$num_rows = mysql_num_rows($result);

	do {
		echo '<tr>';
		echo '<td class="row1"><small>'.$row['member_id'].'</small></td>';
		echo '<td class="row1"><small><a href="users/admin/profile.php?member_id='.$row['member_id'].'"><b>'.$row['login'].'</b></a></small></td>';
		echo '<td class="row1"><small>'.$row['first_name'].'&nbsp;</small></td>';
		echo '<td class="row1"><small>'.$row['last_name'].'&nbsp;</small></td>';
		echo '<td class="row1"><small><a href="users/admin/admin_edit.php?id='.$row['member_id'].'">';
		if ($row['status']) {
			echo '<b>'._AT('instructor').'</b></a></small></td>';
			echo '<td class="row1"><small><a href="users/admin/courses.php?member_id='.$row['member_id'].'"><b>'._AT('courses').'</b></a></small></td>';
		} else {
			echo '<b>'._AT('student1').'</b></a></small></td>';
			echo '<td class="row1"><small class="spacer">'._AT('na').'</small></td>';
		}
		//echo '<td class="row1"><small>'.$row['email'].'</small></td>';
		echo '<td class="row1"><a href="users/admin/admin_delete.php?L='.$L.SEP.'id='.$row['member_id'].'"><img src="images/icon_delete.gif" border="0" alt="'._AT('delete').'"  title="'._AT('delete').'" /></a></td>';
		echo '</tr>';
		if ($count < $num_rows-1) {
			echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
		}
		$count++;
	} while ($row = mysql_fetch_array($result));
}

echo '</table>';

	require($_include_path.'cc_html/footer.inc.php'); 
?>
