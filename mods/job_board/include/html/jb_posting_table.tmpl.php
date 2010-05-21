<div>
	<table>
		<thead>
			<th><?php echo _AT('id'); ?></th>
			<th><?php echo _AT('jb_employer'); ?></th>
			<th><?php echo _AT('jb_categories'); ?></th>
			<th><?php echo _AT('description'); ?></th>
			<th><?php echo _AT('jb_closing_date'); ?></th>
			<th><?php echo _AT('created_date'); ?></th>
		</thead>

		<tbody>
			<?php 
				if (!empty($this->all_job_posts)):
					foreach ($this->all_job_posts as $row): 
			?>
			<tr>
				<td><?php echo $row['id']; ?></td>				
				<td><?php echo $row['employer_id']; ?></td>
				<td><?php echo $row['categories']; ?></td>
				<td><?php echo $row['description']; ?></td>
				<td><?php echo $row['closing_date']; ?></td>
				<td><?php echo $row['created_date']; ?></td>
			</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>
</div>