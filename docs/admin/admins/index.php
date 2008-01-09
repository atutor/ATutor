<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
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

if (isset($_GET['delete'], $_GET['login'])) {
	header('Location: delete.php?login='.$_GET['login']);
	exit;
} else if (isset($_GET['view_log'], $_GET['login'])) {
	header('Location: log.php?login='.$_GET['login']);
	exit;
} else if (isset($_GET['password'], $_GET['login'])) {
	header('Location: password.php?login='.$_GET['login']);
	exit;
} else if (isset($_GET['edit'], $_GET['login'])) {
	header('Location: edit.php?login='.$_GET['login']);
	exit;
} else if ((isset($_GET['edit']) || isset($_GET['delete']) || isset($_GET['view_log']))) {
	$msg->addError('NO_ITEM_SELECTED');
}

$id = $_GET['id'];
$L = $_GET['L'];
require(AT_INCLUDE_PATH.'header.inc.php'); 


$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('login' => 1, 'real_name' => 1, 'email' => 1, 'last_login' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'login';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'login';
} else {
	// no order set
	$order = 'asc';
	$col   = 'login';
}

?>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 90%;">
<colgroup>
	<?php if ($col == 'login'): ?>
		<col />
		<col class="sort" />
		<col span="4" />
	<?php elseif($col == 'real_name'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($col == 'email'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'last_login'): ?>
		<col span="4" />
		<col class="sort" />
		<col />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><a href="admin/admins/index.php?<?php echo $orders[$order]; ?>=login<?php echo $page_string; ?>"><?php echo _AT('login_name');        ?></a></th>
	<th scope="col"><a href="admin/admins/index.php?<?php echo $orders[$order]; ?>=real_name<?php echo $page_string; ?>"><?php echo _AT('real_name');   ?></a></th>
	<th scope="col"><a href="admin/admins/index.php?<?php echo $orders[$order]; ?>=email<?php echo $page_string; ?>"><?php echo _AT('email');           ?></a></th>
	<th scope="col"><a href="admin/admins/index.php?<?php echo $orders[$order]; ?>=last_login<?php echo $page_string; ?>"><?php echo _AT('last_login'); ?></a></th>
	<th scope="col"><?php echo _AT('account_status'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" />
		<input type="submit" name="view_log" value="<?php echo _AT('view_log'); ?>" />
		<input type="submit" name="password" value="<?php echo _AT('password'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
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
		while ($row = mysql_fetch_assoc($result)): ?>
			<tr onmousedown="document.form['m<?php echo $row['login']; ?>'].checked = true;rowselect(this);" id="r_<?php echo $row['login']; ?>">
				<td><input type="radio" name="login" value="<?php echo $row['login']; ?>" id="m<?php echo $row['login']; ?>" /></td>
				<td><label for="m<?php echo $row['login']; ?>"><?php echo $row['login'];      ?></label></td>
				<td><?php echo $row['real_name'];  ?></td>
				<td><?php echo $row['email'];      ?></td>
				<td><?php 
					if ($row['last_login'] == '0000-00-00 00:00:00') {
						echo _AT('never');
					} else {
						echo $row['last_login'];
					} ?></td>
				<td><?php 
					if ($row['privileges'] == 1) { 
						echo _AT('priv_admin_super');
					} else if ($row['privileges'] > 0) {
						echo _AT('active_admin');
					} else {
						echo _AT('inactive_admin');
					}
				 ?> </td>
			</tr>
	 	<?php endwhile; ?>
	<?php } ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>