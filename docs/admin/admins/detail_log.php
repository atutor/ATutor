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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'header.inc.php');

$offset = $_GET['offset'] - 1;
$col = $addslashes($_GET['col']);

$order = $addslashes($_GET['order']);

$sql = "SELECT * FROM ".TABLE_PREFIX."admin_log ORDER BY $col $order LIMIT $offset,1";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
?>
<div class="input-form">
	<div class="row">
		<?php echo _AT('time'); ?><br />
		<?php echo $row['time']; ?>
	</div>
	<div class="row">
		<?php echo _AT('login'); ?><br />
		<?php echo $row['login']; ?>
	</div>
	<div class="row">
		<?php echo _AT('action'); ?><br />
		<?php echo $row['operation']; ?>
	</div>

	<div class="row">
		<?php echo _AT('table_name'); ?><br />
		<?php echo $row['table']; ?>
	</div>
	<div class="row">
		<?php echo _AT('affected_entries'); ?><br />
		<?php echo $row['num_affected']; ?>
	</div>
	<div class="row">
		<?php echo _AT('details'); ?><br />
		<kbd>
		<?php echo $row['details']; ?>
		</kbd>
	</div>
</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>