<table class="data" rules="cols" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('login_name'); ?></th>
	<th scope="col"><?php echo _AT('visits'); ?></th>
	<th scope="col"><?php echo _AT('avg_duration'); ?></th>
	<th scope="col"><?php echo _AT('duration'); ?></th>
</tr>
</thead>
<tbody>
<?php if ($row = mysql_fetch_assoc($this->result)) : ?>
	<?php do { ?>
	<tr onmousedown="document.location='<?php echo AT_BASE_HREF; ?>mods/_standard/tracker/tools/student_usage.php?id=<?php echo $row['member_id']; ?>'" title="<?php echo _AT('member_stats'); ?>">
		<td><a href="<?php echo AT_BASE_HREF; ?>mods/_standard/tracker/tools/student_usage.php?id=<?php echo $row['member_id']; ?>"><?php echo get_display_name($row['member_id']); ?></a></td>
		<td><?php echo $row['counter']; ?></td>
		<td><?php echo $row['average']; ?></td>
		<td><?php echo $row['total']; ?></td>
	</tr>
	<?php } while ($row = mysql_fetch_assoc($this->result)); ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
