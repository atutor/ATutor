<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
	
$_GET['view'] = intval($_GET['view']);

if ($_GET['view']) {
	$result = mysql_query("UPDATE ".TABLE_PREFIX."messages SET new=0 WHERE to_member_id=$_SESSION[member_id] AND message_id=$_GET[view]",$db);
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (!$_SESSION['valid_user']) {
	$msg->printInfos('INVALID_USER');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if ($_GET['delete']) {
	$_GET['delete'] = intval($_GET['delete']);

	if($result = mysql_query("DELETE FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] AND message_id=$_GET[delete]",$db)){
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	$_GET['delete'] = '';
}

$msg->printFeedbacks();


if (isset($_GET['s'])) {
	$msg->printFeedbacks('ACTION_COMPLETED_SUCCESSFULLY');
}

if (isset($_GET['view'])) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."messages WHERE message_id=$_GET[view] AND to_member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
?>
	<table align="center" border="0" cellpadding="2" cellspacing="1" width="98%" class="data static" summary="">
	<thead>
	<tr>
		<th><?php echo AT_print($row['subject'], 'messages.subject'); ?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><?php
			$from = get_display_name($row['from_member_id']);

			echo '<span class="bigspacer">'._AT('from').' <strong>'.AT_print($from, 'members.logins').'</strong> '._AT('posted_on').' ';
			echo AT_date(_AT('inbox_date_format'), $row['date_sent'], AT_DATE_MYSQL_DATETIME);
			echo '</span>';
			echo '<p>';
			echo AT_print($row['body'], 'messages.body');
			echo '</p>';

		?></td>
	</tr>
	</tbody>
	<tfoot>
	<tr>
		<td>
			<form method="get" action="inbox/send_message.php">
			<input type="hidden" name="reply" value="<?php echo $_GET['view']; ?>" />
			<input type="submit" name="submit" value="<?php echo _AT('reply'); ?>" accesskey="r" />
		</form>
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="delete" value="<?php echo $_GET['view']; ?>" />
			<input type="submit" name="submit" value="<?php echo _AT('delete'); ?>" accesskey="x" />
		</form></td>
	</tr>
	</tfoot>
	</table>
	<br />	
	<?php
	}
}

$sql	= "SELECT * FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] ORDER BY date_sent DESC";
$result = mysql_query($sql,$db);
?>
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col" width="100">&nbsp;</th>
	<th scope="col" ><?php echo _AT('from');   ?></th>
	<th scope="col" ><?php echo _AT('subject');?></th>
	<th scope="col" ><?php echo _AT('date');   ?></th>
</tr>
</thead>
<tbody>
<?php if ($row = mysql_fetch_assoc($result)): ?>
	<?php do { ?>
		<?php if ($row['message_id'] == $_GET['view']): ?>
			<tr onmousedown="document.location='<?php echo $_SERVER['PHP_SELF']; ?>?view=<?php echo $row['message_id']; ?>'" class="selected">
		<?php else: ?>
			<tr onmousedown="document.location='<?php echo $_SERVER['PHP_SELF']; ?>?view=<?php echo $row['message_id']; ?>'" title="<?php echo _AT('view_message'); ?>">
		<?php endif; ?>
		<td valign="middle" width="10" align="center">
		<?php
		if ($row['new'] == 1)	{
			echo _AT('new');
		} else if ($row['replied'] == 1) {
			echo _AT('replied');
		}
		echo '</td>';

		$name = get_display_name($row['from_member_id']);

		echo '<td align="left" valign="middle">';

		if ($_GET['view'] != $row['message_id']) {
			echo $name;
		} else {
			echo '<strong>'.$name.'</strong>';
		}
		echo '</td>';

		echo '<td>';
		if ($_GET['view'] != $row['message_id']) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?view='.$row['message_id'].'">'.AT_print($row['subject'], 'messages.subject').'</a>';
		} else {
			echo '<strong>'.AT_print($row['subject'], 'messages.subject').'</strong>';
		}
		echo '</td>';
	
		echo '<td valign="middle" align="left" nowrap="nowrap">';
		echo AT_date(_AT('inbox_date_format'),  $row['date_sent'], AT_DATE_MYSQL_DATETIME);
		echo '</td>';
		echo '</tr>';
	} while ($row = mysql_fetch_assoc($result)); ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>