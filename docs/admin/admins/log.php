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
// $Id: log.php 3619 2005-03-01 15:42:40Z shozubq $

$page = 'log';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'header.inc.php');

$operations[AT_ADMIN_LOG_UPDATE] = 'Update';
$operations[AT_ADMIN_LOG_DELETE] = 'Delete';
$operations[AT_ADMIN_LOG_INSERT] = 'Insert';
$operations[AT_ADMIN_LOG_REPLACE] = 'Replace';
$operations[AT_ADMIN_LOG_OTHER] = 'Other';

if ($_GET['col']) {
	$col = $addslashes($_GET['col']);
} else {
	$col = 'time';
}

if ($_GET['order']) {
	$order = $addslashes($_GET['order']);
} else {
	$order = 'desc';
}

$login_where = '';
if (isset($_GET['alogin'])) {
	$_GET['alogin'] = $addslashes($_GET['alogin']);

	$login_where = ' WHERE login=\''.$_GET['alogin'].'\'';
}

$sql	= "SELECT COUNT(login) FROM ".TABLE_PREFIX."admin_log $login_where";
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

	$offset = ($page-1)*$results_per_page;

	$sql    = "SELECT * FROM ".TABLE_PREFIX."admin_log $login_where ORDER BY `$col` $order LIMIT $offset, $results_per_page";
	$result = mysql_query($sql, $db);
?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data static" rules="cols" align="center">
<thead>
<tr>
	<th scope="col">
		<?php echo _AT('time'); ?>
		 <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=time<?php echo SEP; ?>order=asc" title="<?php echo _AT('time_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('time_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=time<?php echo SEP; ?>order=desc" title="<?php echo _AT('time_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('time_descending'); ?>" border="0" height="7" width="11" />
	</th>
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
</tr>
</thead>
<tbody>
<?php if (mysql_num_rows($result) > 0) : ?>
	<?php while ($row = mysql_fetch_assoc($result)): ?>
		<?php $offset++; ?>
		<tr>
			<td><a href="admin/admins/detail_log.php?offset=<?php echo $offset.SEP.'col='.$col.SEP.'order='.$order; ?>"><?php echo $row['time']; ?></a></td>
			<td><?php echo $row['login']; ?></td>
			<td><?php echo $operations[$row['operation']]; ?></td>
			<td><?php echo $row['table']; ?></td>
		</tr>
	<?php endwhile; ?>
<?php else: ?>
<tr>
	<td colspan="4"><?php echo _AT('empty'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php


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