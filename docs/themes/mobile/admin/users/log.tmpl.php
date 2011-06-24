<table summary="Date and login name associated with an action in a database table" class="data" rules="cols" align="center">
<thead>
<tr>
	<th scope="col"><?php echo _AT('date');           ?></th>
	<th scope="col"><?php echo _AT('login_name');     ?></th>
	<th scope="col"><?php echo _AT('action');         ?></th>
	<th scope="col"><?php echo _AT('database_table'); ?></th>
</tr>
</thead>
<tbody>
<?php if (mysql_num_rows($this->result) > 0) : ?>
	<?php while ($row = mysql_fetch_assoc($this->result)): ?>
		<?php $offset++; ?>
		<tr onmousedown="document.location='<?php echo AT_BASE_HREF; ?>mods/_core/users/admins/detail_log.php?offset=<?php echo $offset.SEP.'p='.$page.SEP.'login='.$_GET['login']; ?>'" title="<?php echo _AT('view_details'); ?>">
			<td><a href="<?php echo AT_BASE_HREF; ?>mods/_core/users/admins/detail_log.php?offset=<?php echo $offset.SEP.'p='.$page.SEP.'login='.$_GET['login']; ?>"><?php echo $row['time']; ?></a></td>
			<td><?php echo $row['login']; ?></td>
			<td><?php echo $this->operations[$row['operation']]; ?></td>
			<td><?php echo $row['table']; ?></td>
		</tr>
	<?php endwhile; ?>
<?php else: ?>
<tr>
	<td colspan="4"><?php echo _AT('none_found'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>