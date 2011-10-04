
<?php 
	if ($row = mysql_fetch_assoc($this->result_messages)) {
?>
	<ul id="inbox-msg">
	<li>
		<div class="forum-post-author">
			<a href="profile.php?id=<?php echo $row['to_member_id']; ?>" class="title"><?php echo get_display_name($row['to_member_id']); ?></a><br />
			<?php print_profile_img($row['to_member_id']); ?>
		</div>

		<div class="forum-post-content">
			<h3><?php echo AT_print($row['subject'], 'messages.subject'); ?></h3>
			<div>
				<div class="forum-post-ctrl">
					<a href="inbox/send_message.php?forward=<?php echo $_GET['view']; ?>"><?php echo _AT('forward'); ?></a> | <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=<?php echo $_GET['view']; ?>"><?php echo _AT('delete'); ?></a>
				</div>
				<p class="date"><?php echo AT_date(_AT('forum_date_format'), $row['date_sent'], AT_DATE_MYSQL_DATETIME); ?></p>
			</div>

			<div class="body">
				<p><?php echo AT_print($row['body'], 'messages.body'); ?></p>
			</div>
			<div style="clear: both; font-size:0.1em"></div>
		</div>
	</li>
	</ul>
	<?php
	}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" summary="" rules="rows">
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
<?php if ($row = mysql_fetch_assoc($this->result)): ?>
	<?php do { ?>
		<?php if ($row['message_id'] == $_GET['view']): ?>
			<tr class="selected">
		<?php else: ?>
			<tr onmousedown="document.form['m<?php echo $row['message_id']; ?>'].checked = !document.form['m<?php echo $row['message_id']; ?>'].checked; rowselectbox(this, document.form['m<?php echo $row['message_id']; ?>'].checked, '');" id="r_<?php echo $row['message_id']; ?>_1">
		<?php endif; ?>
		<td><input type="checkbox" name="id[]" value="<?php echo $row['message_id']; ?>" id="m<?php echo $row['message_id']; ?>" <?php if (isset($_POST['id']) && in_array($row['message_id'], $_POST['id'])) { echo 'checked="checked"'; } ?> title="<?php echo _AT('delete').': '.AT_print($row['subject'], 'messages.subject');?>" onmouseup="this.checked=!this.checked" /></td>
		<?php

		$name = get_display_name($row['to_member_id']);

		echo '<td align="left" valign="middle">';

		if ($_GET['view'] != $row['message_id']) {
			echo $name;
		} else {
			echo '<strong>'.$name.'</strong>';
		}
		echo '</td>';

		echo '<td><label for="m'.$row['message_id'].'">';
		if ($_GET['view'] != $row['message_id']) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?view='.$row['message_id'].'">'.AT_print($row['subject'], 'messages.subject').'</a>';
		} else {
			echo '<strong>'.AT_print($row['subject'], 'messages.subject').'</strong>';
		}
		echo '</label></td>';
	
		echo '<td valign="middle" align="left" nowrap="nowrap">';
		echo AT_date(_AT('inbox_date_format'),  $row['date_sent'], AT_DATE_MYSQL_DATETIME);
		echo '</td>';
		echo '</tr>';
	} while ($row = mysql_fetch_assoc($this->result)); ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>
