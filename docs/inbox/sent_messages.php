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

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('INVALID_USER');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$_GET['view'] = intval($_GET['view']);

if ($_GET['delete']) {
	$_GET['delete'] = intval($_GET['delete']);

	if($result = mysql_query("DELETE FROM ".TABLE_PREFIX."messages_sent WHERE from_member_id=$_SESSION[member_id] AND message_id=$_GET[delete]",$db)){
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_POST['submit_yes'], $_POST['ids'])) {
	$ids = $addslashes($_POST['ids']);

	$sql = "DELETE FROM ".TABLE_PREFIX."messages_sent WHERE from_member_id=$_SESSION[member_id] AND message_id IN ($ids)";
	mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if (isset($_POST['move'], $_POST['id'])) {
	$_POST['id'][] = 0; // to make it non-empty
	$_POST['id'] = implode(',', $_POST['id']);
	$ids = $addslashes($_POST['id']);

	$sql = "INSERT INTO ".TABLE_PREFIX."messages SELECT 0, course_id, from_member_id, {$_SESSION['member_id']}, date_sent, 0, 0, subject, body FROM ".TABLE_PREFIX."messages_sent WHERE from_member_id=$_SESSION[member_id] AND message_id IN ($ids)";
	mysql_query($sql, $db);

	$sql = "DELETE FROM ".TABLE_PREFIX."messages_sent WHERE from_member_id=$_SESSION[member_id] AND message_id IN ($ids)";
	mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
} else if ((isset($_POST['delete']) || isset($_POST['move'])) && !isset($_POST['id'])) {
	$msg->addError('NO_ITEM_SELECTED');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (isset($_GET['view']) && $_GET['view']) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."messages_sent WHERE message_id=$_GET[view] AND from_member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
?>
	<ul id="inbox-msg">
	<li>
		<div class="forum-post-author">
			<a href="profile.php?id=<?php echo $row['to_member_id']; ?>" class="title"><?php echo get_display_name($row['to_member_id']); ?></a><br />
			<?php print_profile_img($row['to_member_id']); ?>
		</div>

		<div class="forum-post-content">
			<h3><?php echo AT_Print($row['subject'], 'messages.subject'); ?></h3>
			<div>
				<div class="forum-post-ctrl">
					<a href="inbox/send_message.php?forward=<?php echo $_GET['view']; ?>"><?php echo _AT('forward'); ?></a> | <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=<?php echo $_GET['view']; ?>"><?php echo _AT('delete'); ?></a>
				</div>
				<p class="date"><?php echo AT_date(_AT('forum_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME); ?></p>
			</div>

			<div class="body">
				<p><?php echo AT_print($row['body'], 'messages.body'); ?></p>
			</div>
			<div style="clear: both; font-size:0.1em"></div>
		</div>
	</div>
	</li>
	</ul>
	<?php
	}
} else if (isset($_POST['delete'], $_POST['id'])) {
	$hidden_vars['ids'] = implode(',', $_POST['id']);

	$msg->addConfirm('DELETE_MSGS', $hidden_vars);
	$msg->printConfirm();
}

$msg->printInfos(array('INBOX_SENT_MSGS_TTL', $_config['sent_msgs_ttl']));

$sql	= "SELECT * FROM ".TABLE_PREFIX."messages_sent WHERE from_member_id=$_SESSION[member_id] ORDER BY date_sent DESC";
$result = mysql_query($sql,$db);
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data static" summary="" rules="rows">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col" ><?php echo _AT('to');   ?></th>
	<th scope="col" ><?php echo _AT('subject');?></th>
	<th scope="col" ><?php echo _AT('date');   ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4">
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>"/>
		<input type="submit" name="move" value="<?php echo _AT('move_to_inbox'); ?>"/>
	</td>
</tr>
</tfoot>
<tbody>
<?php if ($row = mysql_fetch_assoc($result)): ?>
	<?php do { ?>
		<?php if ($row['message_id'] == $_GET['view']): ?>
			<tr class="selected">
		<?php else: ?>
			<tr>
		<?php endif; ?>
		<td><input type="checkbox" name="id[]" value="<?php echo $row['message_id']; ?>" id="m<?php echo $row['message_id']; ?>" <?php if (isset($_POST['id']) && in_array($row['message_id'], $_POST['id'])) { echo 'checked="checked"'; } ?> title="<?php echo _AT('delete').': '.AT_print($row['subject'], 'messages.subject');?>"/></td>
		<?php

		$name = get_display_name($row['to_member_id']);

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
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>