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
// $Id$

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: admin_delete.php?id='.$_GET['id']);
	exit;
} else if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: edit_user.php?id='.$_GET['id']);
	exit;
} else if (isset($_GET['confirm'], $_GET['id'])) {
	$id  = intval($_GET['id']);
	$sql = "UPDATE ".TABLE_PREFIX."members SET status=".AT_STATUS_STUDENT." WHERE member_id=$id";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACCOUNT_CONFIRMED');
} else if (!empty($_GET) && !$_GET['p'] && !$_GET['col'] && !$_GET['filter'] && !$_GET['reset_filter']) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['reset_filter']) {
	unset($_GET);
}

$page_string = '';
if ($_GET['col']) {
	$col = addslashes($_GET['col']);
	$page_string .= SEP.'col='.$_GET['col'];
} else {
	$col = 'login';
}

if ($_GET['order']) {
	$order = addslashes($_GET['order']);
	$page_string .= SEP.'order='.$_GET['order'];
} else {
	$order = 'asc';
}

if (isset($_GET['status']) && ($_GET['status'] != '')) {
	$status = '=' . intval($_GET['status']);
	$page_string .= SEP.'status='.$_GET['status'];
} else {
	$status = '<>-1';
}

if ($_GET['search']) {
	$page_string .= SEP.'search='.urlencode($_GET['search']);
	$search = $addslashes($_GET['search']);
	$search = str_replace(array('%','_'), array('\%', '\_'), $search);
	$search = '%'.$search.'%';
	$search = "((first_name LIKE '$search') OR (last_name LIKE '$search') OR (email LIKE '$search') OR (login LIKE '$search'))";
} else {
	$search = '1';
}

$sql	= "SELECT COUNT(member_id) AS cnt FROM ".TABLE_PREFIX."members WHERE status $status AND $search";
$result = mysql_query($sql, $db);
//debug($sql);
$row = mysql_fetch_assoc($result);
$num_results = $row['cnt'];

$results_per_page = 100;
$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;

$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status $status AND $search ORDER BY $col $order LIMIT $offset, $results_per_page";
$result = mysql_query($sql, $db);

?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('status'); ?><br />
			<input type="radio" name="status" value="0" id="s0" <?php if ($_GET['status'] == 0) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('disabled'); ?></label> 

			<input type="radio" name="status" value="1" id="s1" <?php if ($_GET['status'] == 1) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('unconfirmed'); ?></label> 

			<input type="radio" name="status" value="2" id="s2" <?php if ($_GET['status'] == 2) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('student'); ?></label>

			<input type="radio" name="status" value="3" id="s3" <?php if ($_GET['status'] == 3) { echo 'checked="checked"'; } ?> /><label for="s3"><?php echo _AT('instructor'); ?></label>

			<input type="radio" name="status" value="" id="s" <?php if ($_GET['status'] == '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('username').', '._AT('first_name').', '._AT('last_name') .', '._AT('email'); ?>)</label><br />
			<input type="text" name="search" id="search" size="20" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<div class="paging">
<ul>
	<?php for ($i=1; $i<=$num_pages; $i++): ?>
		<li>
			<?php if ($i == $page) : ?>
				<a class="current" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string; ?>"><em><?php echo $i; ?></em></a>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
		</li>
	<?php endfor; ?>
	</ul>
</div>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="status" value="<?php echo $_GET['status']; ?>" />

<table summary="" class="data" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>

	<th scope="col"><?php echo _AT('username'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=asc" title="<?php echo _AT('username_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('username_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=login<?php echo SEP; ?>order=desc" title="<?php echo _AT('username_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('username_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('first_name'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=first_name<?php echo SEP; ?>order=asc" title="<?php echo _AT('first_name_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('first_name_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=first_name<?php echo SEP; ?>order=desc" title="<?php echo _AT('first_name_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('first_name_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('last_name'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=last_name<?php echo SEP; ?>order=asc" title="<?php echo _AT('last_name_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('last_name_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=last_name<?php echo SEP; ?>order=desc" title="<?php echo _AT('last_name_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('last_name_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('email'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=email<?php echo SEP; ?>order=asc#list" title="<?php echo _AT('email_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('email_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=email<?php echo SEP; ?>order=desc" title="<?php echo _AT('email_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('email_descending'); ?>" border="0" height="7" width="11" /></a></th>

	<th scope="col"><?php echo _AT('status'); ?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=status<?php echo SEP; ?>order=desc" title="<?php echo _AT('status_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('status_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?col=status<?php echo SEP; ?>order=asc" title="<?php echo _AT('status_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('status_descending'); ?>" border="0" height="7" width="11" /></a></th>

</tr>
</thead>
<?php if ($num_results > 0): ?>
<tfoot>
<tr>
	<td colspan="6"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="confirm" value="<?php echo _AT('confirm'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
	<?php while($row = mysql_fetch_assoc($result)): ?>
		<tr onmousedown="document.form['m<?php echo $row['member_id']; ?>'].checked = true;">
			<td><input type="radio" name="id" value="<?php echo $row['member_id']; ?>" id="m<?php echo $row['member_id']; ?>" /></td>
			<td><?php echo $row['login']; ?></td>
			<td><?php echo AT_print($row['first_name'], 'members.first_name'); ?></td>
			<td><?php echo AT_print($row['last_name'], 'members.last_name'); ?></td>
			<td><?php echo AT_print($row['email'], 'members.email'); ?></td>
			<td><?php 
				switch ($row['status']) {
						case AT_STATUS_DISABLED:
								echo _AT('disabled');
							break;
						case AT_STATUS_UNCONFIRMED:
								echo _AT('unconfirmed');
							break;
						case AT_STATUS_STUDENT:
								echo _AT('student');
							break;
						case AT_STATUS_INSTRUCTOR:
								echo _AT('instructor');
							break;
				} ?></td>
		</tr>
	<?php endwhile; ?>
</tbody>
<?php else: ?>
	<tr>
		<td colspan="6"><?php echo _AT('no_users_found'); ?></td>
	</tr>
<?php endif; ?>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>