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

if ($_GET['view']) {
	$result = mysql_query("UPDATE ".TABLE_PREFIX."messages SET new=0, date_sent=date_sent WHERE to_member_id=$_SESSION[member_id] AND message_id=$_GET[view]",$db);
}

if (isset($_GET['delete'])) {
	$_GET['delete'] = intval($_GET['delete']);

	if($result = mysql_query("DELETE FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] AND message_id=$_GET[delete]",$db)){
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}

	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_yes'], $_POST['ids'])) {
	$ids = $addslashes($_POST['ids']);

	$sql = "DELETE FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] AND message_id IN ($ids)";
	mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');

	header('Location: index.php');
	exit;
} else if (isset($_POST['delete']) && !isset($_POST['id'])) {
	$msg->addError('NO_ITEM_SELECTED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (isset($_GET['view']) && $_GET['view']) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."messages WHERE message_id=$_GET[view] AND to_member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {
?>
	<ul id="inbox-msg">
	<li>
		<div class="forum-post-author">
			<a href="profile.php?id=<?php echo $row['from_member_id']; ?>" class="title"><?php echo get_display_name($row['from_member_id']); ?></a><br />
			<?php print_profile_img($row['from_member_id']); ?>
		</div>

		<div class="forum-post-content">
			<h3><?php echo AT_Print(htmlspecialchars($row['subject'], ENT_COMPAT, "UTF-8"), 'messages.subject'); ?></h3>
			<div>
				<div class="forum-post-ctrl">
					<a href="inbox/send_message.php?reply=<?php echo $_GET['view']; ?>"><?php echo _AT('reply'); ?></a> | <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=<?php echo $_GET['view']; ?>"><?php echo _AT('delete'); ?></a>
				</div>
				<p class="date"><?php echo AT_date(_AT('forum_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME); ?></p>
			</div>

			<div class="body">
				<p><?php echo AT_print(htmlspecialchars($row['body'], ENT_COMPAT, "UTF-8"), 'messages.body'); ?></p>
			</div>
			<div style="clear: both; font-size: 0.1em"></div>
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

$sql	= "SELECT * FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] ORDER BY date_sent DESC";
$result = mysql_query($sql,$db);
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data static" summary="" rules="rows">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col">&nbsp;</th>
	<th scope="col" ><?php echo _AT('from');   ?></th>
	<th scope="col" ><?php echo _AT('subject');?></th>
	<th scope="col" ><?php echo _AT('date');   ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5"><input type="submit" name="delete" value="<?php echo _AT('delete'); ?>"/></td>
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
		<td valign="middle">
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
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php
// since Inbox isn't a module, it can't have a cron job.
// so, we delete the expires sent messages with P =  1/7.
if (!rand(0, 6)) {
	$sql = "DELETE FROM ".TABLE_PREFIX."messages_sent WHERE from_member_id=$_SESSION[member_id] AND TO_DAYS(date_sent) < (TO_DAYS(NOW()) - {$_config['sent_msgs_ttl']}) LIMIT 100";
	mysql_query($sql, $db);
}
?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>