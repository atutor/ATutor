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
// $Id: users.php 3619 2005-03-01 15:42:40Z joel $

$page = 'log';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_USERS);

require(AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['col']) {
	$col = $addslashes($_GET['col']);
} else {
	$col = 'login';
}

if ($_GET['order']) {
	$order = $addslashes($_GET['order']);
} else {
	$order = 'asc';
}


$sql	= "SELECT COUNT(login) FROM ".TABLE_PREFIX."admin_track";
$result = mysql_query($sql, $db);

if (($row = mysql_fetch_array($result))==0) {
	echo '<tr><td colspan="7" class="row1">'._AT('no_log_found_').'</td></tr>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

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

?>


<tbody>
<?php
	$offset = ($page-1)*$results_per_page;

	$sql    = "SELECT * FROM ".TABLE_PREFIX."admin_log ORDER BY `$col` $order LIMIT $offset, $results_per_page";
	$result = mysql_query($sql, $db);
?>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 90%;">
<thead>
<tr>
	<th scope="col">
		<?php echo _AT('login'); ?>
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=asc" title="<?php echo _AT('username_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('username_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=desc" title="<?php echo _AT('username_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('username_descending'); ?>" border="0" height="7" width="11" />
	</th>
	<th scope="col">
		<?php echo _AT('action'); ?>
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=operation<?php echo SEP; ?>order=asc" title="<?php echo _AT('operation_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('operation_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=operation<?php echo SEP; ?>order=desc" title="<?php echo _AT('operation_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('operation_descending'); ?>" border="0" height="7" width="11" />
	</th>
	<th scope="col">
		<?php echo _AT('table_name'); ?>
		 <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=table<?php echo SEP; ?>order=asc" title="<?php echo _AT('table_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('table_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=table<?php echo SEP; ?>order=desc" title="<?php echo _AT('table_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('table_descending'); ?>" border="0" height="7" width="11" />
	</th>
		<th scope="col"><?php echo _AT('num_affected'); ?>
		 <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=num_affected<?php echo SEP; ?>order=asc" title="<?php echo _AT('num_affected_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('num_affected_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=num_affected<?php echo SEP; ?>order=desc" title="<?php echo _AT('num_affected_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('num_affected_descending'); ?>" border="0" height="7" width="11" />
	</th>
	<th scope="col">
		<?php echo _AT('time'); ?>
		 <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=time<?php echo SEP; ?>order=asc" title="<?php echo _AT('time_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('time_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=time<?php echo SEP; ?>order=desc" title="<?php echo _AT('time_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('time_descending'); ?>" border="0" height="7" width="11" />
	</th>
</tr>
</thead>

<tbody>
<?php
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			echo '<tr>';
				echo '<td>' . $row['login'] .'</td>';
				echo '<td>' . translate_op($row['operation']) . '</td>';
				echo '<td>' . translate_table($row['table'])  . '</td>';
				echo '<td>' . $row['num_affected'] .'</td>';
				echo '<td>' . AT_date(_AT('forum_date_format'), $row['time'], AT_DATE_MYSQL_DATETIME) . '</td>';
			echo '</tr>';
		}
	}
	else {
		echo '<tr>';
			echo '<td colspan="4">'. _AT('empty').'</td>';
		echo '</tr>';
	}

?>
</tbody>
</table>
</form>

<?php

	function translate_op ($operation) {
		if ($operation == AT_ADMIN_UPDATE) {
			return _AT('admin_update');
		} else if ($operation == AT_ADMIN_DELETE) {
			return _AT('admin_delete');
		} else if ($operation == AT_ADMIN_INSERT) {
			return _AT('admin_insert');
		} else if ($operation == AT_ADMIN_REPLACE) {
			return _AT('admin_replace');
		} else if ($operation == AT_ADMIN_OTHER) {
			return _AT('admin_other');
		}
	}

	function translate_table($table_name) {
		/*

		if ($table_name == 'members' || $table_name == 'course_enrollment' || $table_name == '') {
			return _AT('');
		} else if ($table_name == '') {
			return _AT('');
		} else if ($table_name == '') {
			return _AT('');
		} else if ($table_name == '') {
			return _AT('');
		} else if ($table_name == '') {
			return _AT('');
		} else if ($table_name == '') {
			return _AT('');
		} else if ($table_name == '') {
			return _AT('');
		} else if ($table_name == '') {
			return _AT('');
		}
		
		*/
		return _AT($table_name);
	}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>