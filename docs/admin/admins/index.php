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
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_GET['delete'])) {
	header('Location: delete.php?alogin='.$_GET['alogin']);
	exit;
} else if (isset($_GET['view_log'])) {
	header('Location: log.php?alogin='.$_GET['alogin']);
	exit;
} else if (isset($_GET['edit'])) {
	header('Location: edit.php?alogin='.$_GET['alogin']);
	exit;
}

$id = $_GET['id'];
$L = $_GET['L'];
require(AT_INCLUDE_PATH.'header.inc.php'); 

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

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 90%;">
<thead>
<tr>
	<th scope="col">&nbsp;</th>

	<th scope="col"><?php echo _AT('username'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=asc" title="<?php echo _AT('username_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('username_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=desc" title="<?php echo _AT('username_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('username_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('real_name'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=real_name<?php echo SEP; ?>order=asc" title="<?php echo _AT('real_name_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('real_name_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=real_name<?php echo SEP; ?>order=desc" title="<?php echo _AT('real_name_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('real_name_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('email'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=email<?php echo SEP; ?>order=asc" title="<?php echo _AT('email_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('email_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=email<?php echo SEP; ?>order=desc" title="<?php echo _AT('email_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('email_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('last_login'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=last_login<?php echo SEP; ?>order=asc" title="<?php echo _AT('last_login_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('last_login_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=last_login<?php echo SEP; ?>order=asc" title="<?php echo _AT('last_login_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('last_login_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('status'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=status<?php echo SEP; ?>order=asc" title="<?php echo _AT('status_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('status_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=status<?php echo SEP; ?>order=asc" title="<?php echo _AT('status_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('status_descending'); ?>" border="0" height="7" width="11" /></a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="view_log" value="<?php echo _AT('view_log'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php
	$offset = ($page-1)*$results_per_page;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."admins ORDER BY $col $order";
	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) == 0) { ?>
	<tr>
		<td colspan="6"><?php echo _AT('no_admins_found'); ?></td>
	</tr><?php
	} else {

		while ($row = mysql_fetch_assoc($result)) : ?>
			<tr onmousedown="document.form['m<?php echo $row['login']; ?>'].checked = true;">
				<td><input type="radio" name="alogin" value="<?php echo $row['login']; ?>" id="m<?php echo $row['login']; ?>"></td>
				<td><?php echo $row['login'];      ?></td>
				<td><?php echo $row['real_name'];  ?></td>
				<td><?php echo $row['email'];      ?></td>
				<td><?php echo $row['last_login']; ?></td>
				<td>disabled/active</td>
			</tr>
	 	<?php endwhile; ?>
	<?php } ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>