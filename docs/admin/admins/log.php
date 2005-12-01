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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'header.inc.php');

$operations[AT_ADMIN_LOG_UPDATE] = _AT('update_to');
$operations[AT_ADMIN_LOG_DELETE] = _AT('delete_from');
$operations[AT_ADMIN_LOG_INSERT] = _AT('insert_into');
$operations[AT_ADMIN_LOG_REPLACE] = _AT('replace_into');
$operations[AT_ADMIN_LOG_OTHER] = _AT('other');

$login_where = '';
if (isset($_GET['login']) && $_GET['login']) {
	$_GET['login'] = $addslashes($_GET['login']);

	$login_where = ' WHERE login=\''.$_GET['login'].'\'';
}

$sql	= "SELECT COUNT(login) FROM ".TABLE_PREFIX."admin_log $login_where";
$result = mysql_query($sql, $db);

if (($row = mysql_fetch_assoc($result))==0) {
	echo '<tr><td colspan="7" class="row1">'._AT('no_log_found_').'</td></tr>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

	$num_results = $row[0];
	$results_per_page = 50;
	$num_pages = max(ceil($num_results / $results_per_page), 1);
	$page = intval($_GET['p']);
	if (!$page) {
		$page = 1;
	}	
	$count = (($page-1) * $results_per_page) + 1;

	echo '<div class="paging">';
	echo '<ul>';
	for ($i=1; $i<=$num_pages; $i++) {
		echo '<li>';
		if ($i == $page) {
			echo '<a class="current" href="'.$_SERVER['PHP_SELF'].'?p='.$i.SEP.'login='.$_GET['login'].'"><em>'.$i.'</em></a>';
		} else {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.$i.SEP.'login='.$_GET['login'].'#list">'.$i.'</a>';
		}
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';

	$offset = ($page-1)*$results_per_page;

	$sql    = "SELECT * FROM ".TABLE_PREFIX."admin_log $login_where ORDER BY `time` DESC LIMIT $offset, $results_per_page";
	$result = mysql_query($sql, $db);
?>
<table summary="" class="data" rules="cols" align="center">
<thead>
<tr>
	<th scope="col"><?php echo _AT('date');           ?></th>
	<th scope="col"><?php echo _AT('login_name');     ?></th>
	<th scope="col"><?php echo _AT('action');         ?></th>
	<th scope="col"><?php echo _AT('database_table'); ?></th>
</tr>
</thead>
<tbody>
<?php if (mysql_num_rows($result) > 0) : ?>
	<?php while ($row = mysql_fetch_assoc($result)): ?>
		<?php $offset++; ?>
		<tr onmousedown="document.location='<?php echo $_base_href; ?>admin/admins/detail_log.php?offset=<?php echo $offset.SEP.'p='.$page.SEP.'login='.$_GET['login']; ?>'" title="<?php echo _AT('view_details'); ?>">
			<td><a href="<?php echo $_base_href; ?>admin/admins/detail_log.php?offset=<?php echo $offset.SEP.'p='.$page.SEP.'login='.$_GET['login']; ?>"><?php echo $row['time']; ?></a></td>
			<td><?php echo $row['login']; ?></td>
			<td><?php echo $operations[$row['operation']]; ?></td>
			<td><?php echo $row['table']; ?></td>
		</tr>
	<?php endwhile; ?>
<?php else: ?>
<tr>
	<td colspan="4"><?php echo _AT('none_found'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>