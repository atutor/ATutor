<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

if (isset($_GET['remove'])) {
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.intval($_GET['remove']);
	$result = mysql_query($sql, $db);
}

if (defined('AT_DEVEL_TRANSLATE') && (AT_DEVEL_TRANSLATE == 1)) { 
	$msg->addWarning('TRANSLATE_ON');	
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>
<form method="get" action="http://atutor.ca/check_atutor_version.php">
<input type="hidden" name="v" value="<?php echo urlencode(VERSION); ?>" />
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('atutor_version'); ?></h3>
		<p><?php echo _AT('atutor_version_text', VERSION); ?></strong></p>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
	</div>
</div>
</form>

<form method="get" action="<?php echo $_base_href; ?>admin/fix_content.php">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('fix_content_ordering'); ?></h3>
		<p><?php echo _AT('fix_content_ordering_text'); ?></p>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
	</div>
</div>
</form>

<?php
$sql	= "SELECT M.login, M.member_id, A.* FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."instructor_approvals A WHERE A.member_id=M.member_id ORDER BY M.login";
$result = mysql_query($sql, $db);
$num_pending = mysql_num_rows($result);
?>
<br />
<h3><?php echo _AT('instructor_requests'); ?></h3>
<table class="data static" summary="" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('username');     ?></th>
	<th scope="col"><?php echo _AT('notes');        ?></th>
	<th scope="col"><?php echo _AT('request_date'); ?></th>
	<th scope="col"><?php echo _AT('remove');       ?></th>
	<th scope="col"><?php echo _AT('approve');      ?></th>
</tr>
</thead>
<tbody>
<?php
	if ($row = mysql_fetch_assoc($result)) {
		do {
			$counter++;
			echo '<tr>';
			echo '<td><a href="admin/profile.php?member_id='.$row['member_id'].'">'.AT_print($row['login'], 'members.login').'</a></td>';
			
			echo '<td>'.AT_print($row['notes'], 'instructor_approvals.notes').'</td>';
			echo '<td>'.substr($row['request_date'], 0, -3).'</td>';
			echo '<td><a href="admin/admin_deny.php?id='.$row['member_id'].'">'._AT('remove').'</a></td>';
			echo '<td><a href="admin/admin_edit.php?id='.$row['member_id'].SEP.'from_approve=1">'._AT('approve').'</a></td>';

			echo '</tr>';
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<tr>
			<td colspan="5"><em>'._AT('none').'</em></td>
		</tr>';
	}
?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>