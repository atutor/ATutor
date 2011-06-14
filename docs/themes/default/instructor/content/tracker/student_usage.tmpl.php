<?php global $contentManager;?>
<form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<div class="input-form">
	<div class="row">
		<label for="id"><?php echo _AT('select_member'); ?></label><br />
		<select name="id" id="id">
			<?php
				while ($row = mysql_fetch_assoc($this->result)) {
					$sender = get_display_name($row['member_id']);
					echo '<option value="'.$row['member_id'].'"';
					if ($row['member_id'] == $_GET['id']) {
						echo ' selected="selected"';
					}
					echo '>'.$sender.'</option>';
				}
			?>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('view'); ?>" />
	</div>
</div>
</form>

<?php if ($_GET['id']) : ?>

	<table class="data static" rules="cols" summary="">
	<thead>
	<tr>
		<th scope="col"><?php echo _AT('page'); ?></th>
		<th scope="col"><?php echo _AT('visits'); ?></th>
		<th scope="col"><?php echo _AT('duration'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if ($row = mysql_fetch_assoc($this->result_list)): ?>
		<?php do { ?>
			<tr>
				<td><?php echo $contentManager->_menu_info[$row['content_id']]['title']; ?></td>
				<td><?php echo $row['counter']; ?></td>
				<td><?php echo $row['total']; ?></td>
			</tr>
		<?php } while ($row = mysql_fetch_assoc($this->result_list)); ?>
	<?php else: ?>
		<tr>
			<td colspan="3"><?php echo _AT('none_found'); ?></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>
<?php endif; ?>