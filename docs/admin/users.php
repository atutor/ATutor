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

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

$id = $_GET['id'];
$L = $_GET['L'];
require(AT_INCLUDE_PATH.'header_footer/header.inc.php'); 

echo '<h2>'._AT('users').'</h2>';
if (isset($_GET['f'])) { 
	$f = intval($_GET['f']);
	if ($f <= 0) {
		/* it's probably an array */
		$f = unserialize(urldecode($_GET['f']));
	}
	print_feedback($f);
}
if (isset($errors)) { print_errors($errors); }
if(isset($warnings)){ print_warnings($warnings); }
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


${'highlight_'.$col} = ' style="font-size: 1em;"';

?>
<?php

$sql	= "SELECT COUNT(member_id) FROM ".TABLE_PREFIX."members";
$result = mysql_query($sql, $db);

if (($row = mysql_fetch_array($result))==0) {
	if($_GET['L']){
		echo '<tr><td colspan="7" class="row1">'._AT('no_users_found_for').' <strong>'.$_GET['L'].'</strong></td></tr>';
	}
} else {
	$num_results = $row[0];
	$results_per_page = 100;
	$num_pages = ceil($num_results / $results_per_page);
	$page = intval($_GET['p']);
	if (!$page) {
		$page = 1;
	}	
	$count = (($page-1) * $results_per_page) + 1;

	for ($i=1; $i<=$num_pages; $i++) {
		if ($i == 1) {
			echo _AT('page').': | ';
		}
		if ($i == $page) {
			echo '<strong>'.$i.'</strong>';
		} else {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.$i.'#list">'.$i.'</a>';
		}
		echo ' | ';
	}


	echo '<br /><br /><a name="list"></a>';
?>	
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%">
<tr>
	<th colspan="7" class="cyan"><?php 
		echo _AT('users');
	?></th>
</tr>
<tr>
	<th scope="col" class="cat"><a name="list"></a><small<?php echo $highlight_member_id; ?>><?php echo _AT('id'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=member_id<?php echo SEP; ?>order=asc#list" title="<?php echo _AT('id_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('id_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=member_id<?php echo SEP; ?>order=desc#list" title="<?php echo _AT('id_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('id_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th scope="col" class="cat"><small<?php echo $highlight_login; ?>><?php echo _AT('username'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=asc#list" title="<?php echo _AT('username_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('username_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=desc#list" title="<?php echo _AT('username_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('username_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th scope="col" class="cat"><small<?php echo $highlight_first_name; ?>><?php echo _AT('first_name'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=first_name<?php echo SEP; ?>order=asc#list" title="<?php echo _AT('first_name_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('first_name_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=first_name<?php echo SEP; ?>order=desc#list" title="<?php echo _AT('first_name_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('first_name_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th scope="col" class="cat"><small<?php echo $highlight_last_name; ?>><?php echo _AT('last_name'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=last_name<?php echo SEP; ?>order=asc#list" title="<?php echo _AT('last_name_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('last_name_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=last_name<?php echo SEP; ?>order=desc#list" title="<?php echo _AT('last_name_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('last_name_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th scope="col" class="cat"><small<?php echo $highlight_status; ?>><?php echo _AT('status'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=status<?php echo SEP; ?>order=desc#list" title="<?php echo _AT('status_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('status_ascending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=status<?php echo SEP; ?>order=asc#list" title="<?php echo _AT('status_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('status_descending'); ?>" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></small></th>

	<th class="cat"><small class="cat"><?php echo _AT('courses'); ?> </small></th>
	<th class="cat"><small>&nbsp;</small></th>
</tr>

<?php
	$offset = ($page-1)*$results_per_page;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."members ORDER BY $col $order LIMIT $offset, $results_per_page";
	$result = mysql_query($sql, $db);

	$count = 0;
	while ($row = mysql_fetch_array($result)) {
		echo '<tr>';
		echo '<td class="row1"><small>'.$row['member_id'].'</small></td>';
		echo '<td class="row1"><small><a href="admin/profile.php?member_id='.$row['member_id'].'"><strong>'.$row['login'].'</strong></a></small></td>';
		echo '<td class="row1"><small>'.AT_print($row['first_name'], 'members.first_name').'&nbsp;</small></td>';
		echo '<td class="row1"><small>'.AT_print($row['last_name'], 'members.last_name').'&nbsp;</small></td>';
		echo '<td class="row1"><small><a href="admin/admin_edit.php?id='.$row['member_id'].'">';
		if ($row['status']) {
			echo '<strong>'._AT('instructor').'</strong></a></small></td>';
			echo '<td class="row1"><small><a href="admin/courses.php?member_id='.$row['member_id'].'"><strong>'._AT('courses').'</strong></a></small></td>';
		} else {
			echo '<strong>'._AT('student1').'</strong></a></small></td>';
			echo '<td class="row1"><small class="spacer">'._AT('na').'</small></td>';
		}
		echo '<td class="row1"><a href="admin/admin_delete.php?id='.$row['member_id'].'"><img src="images/icon_delete.gif" height="18" width="16" border="0" alt="'._AT('delete').'"  title="'._AT('delete').'" class="menuimage18" /></a></td>';
		echo '</tr>';
		if ($count < $num_rows-1) {
			echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
		}
		$count++;
	}
} 

echo '</table>';

require(AT_INCLUDE_PATH.'header_footer/footer.inc.php'); 
?>