<table>
	<thead>
		<th><?php echo _AT('id'); ?></th>
		<th><?php echo _AT('jb_employer'); ?></th>
		<th><?php echo _AT('jb_categories'); ?></th>
		<th><?php echo _AT('description'); ?></th>
		<th><?php echo _AT('jb_closing_date'); ?></th>
		<th><?php echo _AT('created_date'); ?></th>
		<th></th>
	</thead>
	<tbody>
		<?php 
			if (!empty($this->all_job_posts)):
			debug($this->all_job_posts);
				foreach ($this->all_job_posts as $id=>$row): 
		?>
		<tr>
			<td><?php echo $row['id']; ?></td>				
			<td><?php echo $row['employer_id']; ?></td>
			<td><?php echo json_decode($row['categories']); ?></td>
			<td><?php echo $row['description']; ?></td>
			<td><?php echo $row['closing_date']; ?></td>
			<td><?php echo $row['created_date']; ?></td>
			<td><a href="" title="Click to edit"><?php echo _AT('edit');?></a> | <a href="" title="Click to delete"><?php echo _AT('delete'); ?></a></td>
		</tr>
		<?php endforeach; endif; ?>
	</tbody>
</table>

