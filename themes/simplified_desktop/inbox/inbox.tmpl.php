<?php 
	if (isset($this->row_messages) && $this->row_messages != '') {
?>
	<ul id="inbox-msg">
	<li>
		<div class="forum-post-author">
			<a href="profile.php?id=<?php echo $this->row_messages['from_member_id']; ?>" class="title"><?php echo get_display_name($this->row_messages['from_member_id']); ?></a><br />
			<?php print_profile_img($this->row_messages['from_member_id']); ?>
		</div>

		<div class="forum-post-content">
			<h3><?php echo AT_print($this->row_messages['subject'], 'messages.subject'); ?></h3>
			<div>
				<div class="forum-post-ctrl">
					<a href="inbox/send_message.php?reply=<?php echo $_GET['view']; ?>"><?php echo _AT('reply'); ?></a> | <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=<?php echo $_GET['view']; ?>"><?php echo _AT('delete'); ?></a>
				</div>
				<p class="date"><?php echo AT_date(_AT('forum_date_format'), $this->row_messages['date_sent'], AT_DATE_MYSQL_DATETIME); ?></p>
			</div>

			<div class="body">
				<p><?php echo AT_print($this->row_messages['body'], 'messages.body'); ?></p>
			</div>
		</div>

	</li>
	</ul><br /><br />
	<?php
	}
?>
	
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form" >
<div class="table-surround">
<table class="data" summary="" >
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
<?php if ($this->row_sent): ?>
		<?php foreach($this->row_sent as $row) { ?>
		<?php if ($row['message_id'] == $_GET['view']): ?>
			<tr class="selected">
		<?php else: ?>
			<tr onmousedown="document.form['m<?php echo $row['message_id']; ?>'].checked = !document.form['m<?php echo $row['message_id']; ?>'].checked; rowselectbox(this, document.form['m<?php echo $row['message_id']; ?>'].checked, '');" id="r_<?php echo $row['message_id']; ?>_1">
		<?php endif; ?>
		<td><input type="checkbox" name="id[]" value="<?php echo $row['message_id']; ?>" id="m<?php echo $row['message_id']; ?>" <?php if (isset($_POST['id']) && in_array($row['message_id'], $_POST['id'])) { echo 'checked="checked"'; } ?> title="<?php echo _AT('delete').': '.AT_print($row['subject'], 'messages.subject');?>" onmouseup="this.checked=!this.checked" /></td>
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

		echo '<td><label for="m'.$row['message_id'].'">';
		if ($_GET['view'] != $row['message_id']) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?view='.$row['message_id'].'">'.AT_print($row['subject'], 'messages.subject').'</a>';
		} else {
			echo '<strong>'.AT_print($row['subject'], 'messages.subject').'</strong>';
		}
		echo '</label></td>';
	
		echo '<td valign="middle" align="left" nowrap="nowrap">';
		echo AT_date(_AT('forum_date_format'),  $row['date_sent'], AT_DATE_MYSQL_DATETIME);
		echo '</td>';
		echo '</tr>';
	} ?>
<?php else: ?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</form>