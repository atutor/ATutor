<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: detail_log.php 7208 2008-01-09 16:07:24Z greg $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['submit'])) {
	header('Location: log.php?p='.$_POST['p'].SEP.'login='.$_POST['login']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$offset = $_GET['offset'] - 1;
$col = $addslashes($_GET['col']);
$order = $addslashes($_GET['order']);

$login_where = '';
if (isset($_GET['login']) && $_GET['login']) {
	$_GET['login'] = $addslashes($_GET['login']);

	$login_where = ' WHERE login=\''.$_GET['login'].'\'';
}

$sql = "SELECT * FROM ".TABLE_PREFIX."admin_log $login_where ORDER BY `time` DESC LIMIT $offset,1";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);

$operations[AT_ADMIN_LOG_UPDATE] = _AT('update_to');
$operations[AT_ADMIN_LOG_DELETE] = _AT('delete_from');
$operations[AT_ADMIN_LOG_INSERT] = _AT('insert_into');
$operations[AT_ADMIN_LOG_REPLACE] = _AT('replace_into');
$operations[AT_ADMIN_LOG_OTHER] = _AT('other');

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="p" value="<?php echo $_GET['p']; ?>" />
<input type="hidden" name="login" value="<?php echo $_GET['login']; ?>" />
<div class="input-form">
	<div class="row">
		<?php echo _AT('date'); ?><br />
		<?php echo $row['time']; ?>
	</div>
	<div class="row">
		<?php echo _AT('login_name'); ?><br />
		<?php echo $row['login']; ?>
	</div>
	<div class="row">
		<?php echo _AT('action'); ?><br />
		<?php echo $operations[$row['operation']]; ?>
	</div>

	<div class="row">
		<?php echo _AT('database_table'); ?><br />
		<?php echo TABLE_PREFIX . $row['table']; ?>
	</div>
	<div class="row">
		<?php echo _AT('affected_entries'); ?><br />
		<?php echo $row['num_affected']; ?>
	</div>
	<div class="row">
		<?php echo _AT('details'); ?><br />
		<kbd>
			<?php echo htmlspecialchars(wordwrap($row['details'], 80, "\n", TRUE)); ?>
		</kbd>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('back'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>